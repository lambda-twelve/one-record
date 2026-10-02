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
if [ "${KEEP_NEONE:-0}" != "1" ]; then
    trap '"${COMPOSE[@]}" down -v >/dev/null 2>&1 || true' EXIT
fi

export ONE_RECORD_NEONE_URL="http://${NEONE_PUBLIC_HOST}:${NEONE_PORT}"
export ONE_RECORD_NEONE_ISSUER="$NEONE_ISSUER"
export ONE_RECORD_NEONE_PRIVATE_KEY="$KEYS/private.pem"
echo "NE:ONE at $ONE_RECORD_NEONE_URL"
${PHPUNIT:-vendor/bin/phpunit} --group interop --no-coverage "$@"
