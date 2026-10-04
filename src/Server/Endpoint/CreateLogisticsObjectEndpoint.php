<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Endpoint;

use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\ModelException;
use LambdaTwelve\OneRecord\Server\Event\LogisticsObjectCreated;
use LambdaTwelve\OneRecord\Server\Http\HttpException;
use LambdaTwelve\OneRecord\Server\Http\Negotiated;
use LambdaTwelve\OneRecord\Server\Notification\Fanout;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * POST /logistics-objects. The spec marks it "internal only": it exists for
 * hosts that create objects over HTTP from their own systems, so the access
 * policy must say yes (it says no by default). A body with an @id under this
 * server is honoured (predefined URIs, as NE:ONE allows); any other @id is
 * refused; without one the server mints a URI.
 */
final class CreateLogisticsObjectEndpoint extends AbstractEndpoint
{
    public function handle(ServerRequestInterface $request, Agent $agent, Negotiated $negotiated, array $parameters): ResponseInterface
    {
        $this->decide($agent, Action::CreateLogisticsObject, null);
        (new \LambdaTwelve\OneRecord\Server\Http\ContentNegotiation($this->services->config))->bodyVersion($request, $negotiated);
        $json = $this->services->body->json($request);
        if (\array_key_exists('@graph', $json)) {
            throw HttpException::badRequest('The body must not contain @graph; a single logistics object is expected.', '@graph');
        }

        $declared = $json['@id'] ?? null;
        $iri = null;
        if (\is_string($declared) && $declared !== '') {
            $relative = $this->services->config->relativePath(new \LambdaTwelve\OneRecord\Rdf\Iri(trim($declared)));
            if ($relative === null || preg_match('#^logistics-objects/[^/?]+$#', $relative) !== 1) {
                throw HttpException::badRequest('An @id must be a logistics object URI under this server, or be left out so the server assigns one.', '@id');
            }
            $iri = $this->services->config->logisticsObjectIri(substr($relative, \strlen('logistics-objects/')));
        }
        $iri ??= $this->services->config->logisticsObjectIri($this->services->ids->next());
        unset($json['@id']);

        try {
            $object = LogisticsObject::fromJsonLd($json, $iri)->withEmbeddedIds($this->services->embeddedIds);
        } catch (JsonLdException|ModelException $e) {
            throw HttpException::badRequest($e->getMessage());
        }
        $this->validate($object);

        try {
            $stored = $this->services->objects->create($object, $this->services->clock->now());
        } catch (StoreException $e) {
            throw HttpException::conflict('A logistics object with this URI already exists.', $iri->value);
        }
        \LambdaTwelve\OneRecord\Server\Deprecations::log($stored->object->graph, $stored->object->iri, $this->services->vocabulary, $this->services->logger);
        $this->services->dispatcher->dispatch(new LogisticsObjectCreated($stored, $agent->iri));
        // No action request causes a creation; fan-out then names the subscription request (AR-021).
        (new Fanout($this->services))->logisticsObjectCreated($stored);

        return $this->services->responder->empty(201, $negotiated, ['Location' => $iri->value, 'Type' => $stored->object->mostSpecificType($this->services->vocabulary) ?? 'https://onerecord.iata.org/ns/cargo#LogisticsObject']);
    }

    /**
     * Model validation per the spec's implementation guidelines: the object
     * must be typed with a logistics object class; properties the ontology does
     * not know are refused (a 400), properties the class does not accept too.
     */
    private function validate(LogisticsObject $object): void
    {
        $types = $object->types();
        if ($types === []) {
            throw HttpException::badRequest('A logistics object must declare its @type.', '@type', 'Invalid resource');
        }
        $vocabulary = $this->services->vocabulary;
        if (array_filter($types, static fn(string $t): bool => $vocabulary->isLogisticsObjectClass($t)) === []) {
            throw HttpException::badRequest(\sprintf('%s is not a logistics object class.', implode(', ', $types)), '@type', 'Invalid resource');
        }
        // Root and every embedded node, properties, kinds, ranges and grammars: the same validator that
        // judges a change or a posted event (R10-004).
        // A nested logistics object (the spec's example A2 posts a Company with a Person inside) is kept as
        // an embedded node rather than refused or split into objects of its own: spec question 33.
        $violations = (new \LambdaTwelve\OneRecord\Model\GraphValidator($vocabulary))->validate($object->graph, $object->iri, nestedLogisticsObjects: true);
        if ($violations !== []) {
            throw HttpException::badRequest($violations[0]->message, $violations[0]->property, 'Invalid resource');
        }
    }
}
