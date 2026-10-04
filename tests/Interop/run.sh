#!/usr/bin/env bash
# Runs the interoperability suite against NE:ONE in Docker.
#
#   bash tests/Interop/run.sh [phpunit args]
#
# Generates an RS256 key pair (once), starts NE:ONE trusting its public half,
# waits for it, runs the PHPUnit "interop" group against it and stops it.
#
# Environment:
#   NEONE_IMAGE        use this prebuilt image instead of building from source
#   NEONE_PORT         host port (default 18080)
#   NEONE_PUBLIC_HOST  host name NE:ONE mints URIs with and the tests connect to
#                      (default: host.docker.internal, right for tests run inside DDEV;
#                      use localhost when PHP runs on the Docker host, as in CI)
#   PHPUNIT            how to run PHPUnit (default: vendor/bin/phpunit; DDEV users
#                      run the suite through `ddev interop` instead)
#   KEEP_NEONE=1       leave the container running afterwards
set -euo pipefail
cd "$(dirname "$0")/../.."

export NEONE_PORT="${NEONE_PORT:-18080}"
export NEONE_PUBLIC_HOST="${NEONE_PUBLIC_HOST:-host.docker.internal}"
export NEONE_ISSUER="${NEONE_ISSUER:-https://interop.one-record.test}"
KEYS="$(pwd)/.cache/interop/keys"
export NEONE_KEYS_DIR="$KEYS"
# Run from a DDEV host command, Compose would inherit DDEV's project variables and act on the wrong project.
unset COMPOSE_FILE COMPOSE_PROJECT_NAME COMPOSE_PROFILES
COMPOSE=(docker compose -p one-record-interop -f tests/Interop/neone/docker-compose.yml)

mkdir -p "$KEYS"
if [ ! -f "$KEYS/private.pem" ]; then
    openssl genpkey -algorithm RSA -pkeyopt rsa_keygen_bits:2048 -out "$KEYS/private.pem" 2>/dev/null
    openssl pkey -in "$KEYS/private.pem" -pubout -out "$KEYS/public.pem" 2>/dev/null
    chmod 644 "$KEYS/public.pem"
fi

if [ -n "${NEONE_IMAGE:-}" ]; then
    "${COMPOSE[@]}" up -d --wait
else
    "${COMPOSE[@]}" up -d --build --wait
fi
# On failure, NE:ONE's log is the evidence; print it before the container goes.
cleanup() {
    status=$?
    if [ "$status" -ne 0 ]; then
        echo "--- NE:ONE log (last 200 lines) ---"
        "${COMPOSE[@]}" logs --no-color 2>/dev/null | tail -200 || true
    fi
    if [ "${KEEP_NEONE:-0}" != "1" ]; then
        "${COMPOSE[@]}" down -v >/dev/null 2>&1 || true
    fi
}
trap cleanup EXIT

export ONE_RECORD_NEONE_URL="http://${NEONE_PUBLIC_HOST}:${NEONE_PORT}"
export ONE_RECORD_NEONE_ISSUER="$NEONE_ISSUER"
export ONE_RECORD_NEONE_PRIVATE_KEY="$KEYS/private.pem"

# Compose's --wait returns when Quarkus reports ready, which can be before the RDF store
# answers requests: the first calls then fail with 500. Wait until the API itself answers
# (any status below 500 will do; without a token that is 401) before running the suite.
ready_url="http://127.0.0.1:${NEONE_PORT}/"
for attempt in $(seq 1 60); do
    code="$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 -H 'Accept: application/ld+json' "$ready_url" || echo 000)"
    if [ "$code" != "000" ] && [ "$code" -lt 500 ]; then
        echo "NE:ONE answers ($code) after $attempt attempt(s)"
        break
    fi
    if [ "$attempt" -eq 60 ]; then
        echo "NE:ONE did not answer below 500 within 60 attempts (last: $code)" >&2
        exit 1
    fi
    sleep 2
done

echo "NE:ONE at $ONE_RECORD_NEONE_URL"
${PHPUNIT:-vendor/bin/phpunit} --group interop --no-coverage "$@"
