#!/usr/bin/env bash
# Runs the compliance collection against bin/serve, once per supported API
# version. Needs PHP with the dev dependencies and Node (newman via npx).
#
#   bash tests/Compliance/run.sh [newman args]
#
# Environment: COMPLIANCE_PORT (default 8099), COMPLIANCE_VERSIONS (default
# "2.3.0 2.2.0"), COMPLIANCE_OUT (default .cache/compliance).
set -euo pipefail
cd "$(dirname "$0")/../.."

PORT="${COMPLIANCE_PORT:-8099}"
VERSIONS="${COMPLIANCE_VERSIONS:-2.3.0 2.2.0}"
OUT="${COMPLIANCE_OUT:-.cache/compliance}"
BASE="http://127.0.0.1:${PORT}"
PARTNER_AGENT="https://partner.example/logistics-objects/partner"
mkdir -p "$OUT"

php tests/Compliance/collection.php > "$OUT/collection.json"

# The bulk endpoint is optional in the spec and off by default; the collection expects it on.
php bin/serve --port="$PORT" --bulk --state-dir="$OUT/serve" > "$OUT/serve.log" 2>&1 &
SERVER=$!
# bin/serve runs php -S as a child; kill both or the port stays taken for the next run.
trap 'pkill -P "$SERVER" 2>/dev/null || true; kill "$SERVER" 2>/dev/null || true' EXIT

for _ in $(seq 1 100); do
    code=$(curl -s -o /dev/null -w '%{http_code}' "$BASE/" || true)
    if [ "$code" != "000" ] && [ -n "$code" ]; then break; fi
    sleep 0.2
done
if [ "${code:-000}" = "000" ]; then
    echo "bin/serve did not start; see $OUT/serve.log" >&2
    exit 1
fi

HOLDER_TOKEN=$(php bin/serve --port="$PORT" --state-dir="$OUT/serve" --print-token=holder)
PARTNER_TOKEN=$(php bin/serve --port="$PORT" --state-dir="$OUT/serve" --print-token=partner)

status=0
for version in $VERSIONS; do
    echo "== API ${version} =="
    if ! npx --yes newman run "$OUT/collection.json" \
        --env-var "baseUrl=$BASE" \
        --env-var "apiVersion=$version" \
        --env-var "holderToken=$HOLDER_TOKEN" \
        --env-var "partnerToken=$PARTNER_TOKEN" \
        --env-var "partnerAgent=$PARTNER_AGENT" \
        --reporters cli,junit \
        --reporter-junit-export "$OUT/report-${version}.xml" \
        --color on "$@"; then
        status=1
    fi
done
exit $status
