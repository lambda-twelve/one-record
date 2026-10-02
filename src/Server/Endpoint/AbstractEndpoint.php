<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\JsonLd\Context;
use LambdaTwelve\OneRecord\JsonLd\Writer;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Http\Responder;
use LambdaTwelve\OneRecord\Server\Services;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Server\Spi\StoredObject;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use Psr\Http\Message\ServerRequestInterface;

/**
 * What endpoints share: resolving an object id to a stored object under the
 * access policy, writing an object with its revision properties and headers,
 * reading the spec's timestamps and query parameters.
 */
abstract class AbstractEndpoint implements Endpoint
{
    public function __construct(protected readonly Services $services) {}

    /**
     * The stored object (latest, or at a revision or time), after the policy
     * allowed $action on it. A hidden or missing object is a 404; a forbidden
     * one a 403. Both use the same body shape so nothing leaks.
     */
    protected function requireObject(string $id, Agent $agent, Action $action, ?int $revision = null, ?DateTimeImmutable $at = null): StoredObject
    {
        $iri = $this->services->config->logisticsObjectIri($id);
        $decision = $this->services->policy->decide($agent, $action, $iri);
        if ($decision === Decision::Hide) {
            throw HttpException::notFound('Logistics Object', $iri->value);
        }
        $stored = match (true) {
            $revision !== null => $this->services->objects->revision($iri, $revision),
            $at !== null => $this->services->objects->at($iri, $at),
            default => $this->services->objects->latest($iri),
        };
        if ($stored === null) {
            if ($decision === Decision::Forbid && $this->services->objects->exists($iri)) {
                throw HttpException::forbidden($iri->value);
            }
            throw HttpException::notFound('Logistics Object', $iri->value);
        }
        if ($decision === Decision::Forbid) {
            throw HttpException::forbidden($iri->value);
        }

        return $stored;
    }

    protected function decide(Agent $agent, Action $action, ?Iri $resource): void
    {
        $decision = $this->services->policy->decide($agent, $action, $resource);
        if ($decision === Decision::Hide) {
            throw HttpException::notFound('The requested resource', $resource?->value);
        }
        if ($decision === Decision::Forbid) {
            throw HttpException::forbidden($resource?->value);
        }
    }

    /**
     * @return array<string, string>
     */
    protected function objectHeaders(StoredObject $stored, ?string $location = null): array
    {
        $type = $stored->object->mostSpecificType($this->services->vocabulary);
        $headers = [
            'Revision' => (string) $stored->revision,
            'Latest-Revision' => (string) $stored->latestRevision,
            'Last-Modified' => Responder::httpDate($stored->lastModified),
            'Location' => $location ?? $stored->object->iri->value,
        ];
        if ($type !== null) {
            $headers['Type'] = $type;
        }

        return $headers;
    }

    /**
     * The object as JSON-LD with api:hasRevision and api:hasLatestRevision,
     * optionally with linked objects on this server embedded (?embedded=true)
     * and, for a historical read, every local link carrying the same ?at=.
     *
     * @return array<string, mixed>
     */
    protected function writeObject(StoredObject $stored, Agent $agent, bool $embedded, ?string $atParameter): array
    {
        $graph = new Graph($stored->object->graph);
        $root = $stored->object->iri;
        $this->addRevisions($graph, $root, $stored);
        if ($embedded) {
            $this->embedLinked($graph, $root, $agent, $atParameter, $this->services->config->embeddedDepth, [$root->value => true]);
        }
        if ($atParameter !== null) {
            // The document is about the object at that time: its @id and every local link carry the same ?at=.
            $historicalRoot = new Iri($root->value . '?at=' . $atParameter);
            $graph = $this->rewriteLocalLinks($graph, $atParameter, $root, $historicalRoot);
            $root = $historicalRoot;
        }
        $context = new Context(['cargo' => \LambdaTwelve\OneRecord\Spec\Namespaces::CARGO, 'api' => \LambdaTwelve\OneRecord\Spec\Namespaces::API]);

        return (new Writer())->write($graph, $root, $context);
    }

    private function addRevisions(Graph $graph, Iri $subject, StoredObject $stored): void
    {
        $graph->add(new Triple($subject, new Iri(Api::hasRevision), Literal::integer($stored->revision)));
        $graph->add(new Triple($subject, new Iri(Api::hasLatestRevision), Literal::integer($stored->latestRevision)));
    }

    /**
     * @param array<string, true> $seen
     */
    private function embedLinked(Graph $graph, Iri $node, Agent $agent, ?string $atParameter, int $depth, array $seen): void
    {
        if ($depth <= 0) {
            return;
        }
        foreach ($graph->about($node) as $triple) {
            $object = $triple->object;
            if (!$object instanceof Iri || isset($seen[$object->value]) || LogisticsObject::isEmbeddedId($object)) {
                continue;
            }
            $relative = $this->services->config->relativePath($object);
            if ($relative === null || preg_match('#^logistics-objects/[^/]+$#', $relative) !== 1) {
                continue;
            }
            // Only objects the caller may read are embedded; others stay links, exactly as a direct GET would answer.
            if (!$this->services->policy->decide($agent, Action::ReadLogisticsObject, $object)->allowed()) {
                continue;
            }
            $linked = $atParameter !== null
                ? $this->services->objects->at($object, self::parseAt($atParameter))
                : $this->services->objects->latest($object);
            if ($linked === null) {
                continue;
            }
            $seen[$object->value] = true;
            foreach ($linked->object->graph as $t) {
                $graph->add($t);
            }
            $this->addRevisions($graph, $object, $linked);
            $this->embedLinked($graph, $object, $agent, $atParameter, $depth - 1, $seen);
        }
    }

    /**
     * Links to objects on this server carry the same ?at= so a client following
     * them stays at one point in time (spec: "Retrieve a historical Logistics Object").
     */
    private function rewriteLocalLinks(Graph $graph, string $atParameter, Iri $root, Iri $historicalRoot): Graph
    {
        $rewritten = new Graph();
        foreach ($graph as $triple) {
            $subject = $triple->subject->equals($root) ? $historicalRoot : $triple->subject;
            $object = $triple->object;
            if ($object->equals($root)) {
                $object = $historicalRoot;
            }
            if ($object instanceof Iri && !LogisticsObject::isEmbeddedId($object) && $graph->about($object) === []) {
                $relative = $this->services->config->relativePath($object);
                if ($relative !== null && preg_match('#^logistics-objects/[^/]+$#', $relative) === 1) {
                    $object = new Iri($object->value . '?at=' . $atParameter);
                }
            }
            $rewritten->add(new Triple($subject, $triple->predicate, $object));
        }

        return $rewritten;
    }

    /**
     * The spec's query timestamps: YYYYMMDDThhmmssZ, with RFC 3339 accepted too.
     */
    public static function parseAt(string $value): DateTimeImmutable
    {
        $trimmed = trim($value);
        $parsed = preg_match('/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z$/', $trimmed, $m) === 1
            ? DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP', \sprintf('%s-%s-%sT%s:%s:%s+00:00', $m[1], $m[2], $m[3], $m[4], $m[5], $m[6]))
            : (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/', $trimmed) === 1 ? new DateTimeImmutable($trimmed) : false);
        if ($parsed === false) {
            throw HttpException::invalidQuery(\sprintf('"%s" is not a timestamp in the form YYYYMMDDThhmmssZ.', $trimmed), 'at');
        }

        return $parsed;
    }

    /**
     * @return array<string, string>
     */
    protected static function query(ServerRequestInterface $request): array
    {
        $out = [];
        foreach ($request->getQueryParams() as $key => $value) {
            if (\is_string($value)) {
                $out[(string) $key] = $value;
            } elseif (\is_array($value)) {
                $out[(string) $key] = implode(',', array_filter($value, is_string(...)));
            }
        }
        if ($out === [] && $request->getUri()->getQuery() !== '') {
            parse_str($request->getUri()->getQuery(), $parsed);
            foreach ($parsed as $key => $value) {
                if (\is_string($value)) {
                    $out[(string) $key] = $value;
                }
            }
        }

        return $out;
    }

    protected static function isHead(ServerRequestInterface $request): bool
    {
        return strtoupper($request->getMethod()) === 'HEAD';
    }

    protected function negotiatedOrDefault(Negotiated $negotiated): Negotiated
    {
        return $negotiated;
    }
}
