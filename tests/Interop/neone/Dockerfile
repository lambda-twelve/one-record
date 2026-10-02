# syntax=docker/dockerfile:1
#
# NE:ONE (https://ne-one.de), the reference ONE Record server, built from
# source at a pinned commit for the interoperability suite (tests/Interop).
# It is the comparison oracle: the same graph goes to NE:ONE and to this
# package's server, and the two answers are compared as RDF.
#
# Built from source rather than pulled, so what runs is exactly the code at
# NEONE_COMMIT. Bumping NE:ONE is a reviewed change of this ARG.
#
# Persistence `rdf4j-native`: the RDF4J store runs inside the JVM (LMDB on
# one volume), so there is nothing else to run. NE:ONE classes it as its test
# tier, which is what this is.
#
# NE:ONE is licensed under the Open Logistics Foundation License 1.3; the
# licence and our NOTICE travel inside the image (/licenses). These build
# files are Lambda Twelve's (Apache-2.0); no NE:ONE file is modified.

# main at 2026-10-02: ONE Record API 2.2.0, data model 3.2.0. The last tag
# (v2.1.0-alpha) predates both, so a commit is pinned rather than a tag.
ARG NEONE_COMMIT=9d485f63d3d39560837db385cae3ec1f236610b0
ARG NEONE_REPO=https://git.openlogisticsfoundation.org/wg-digitalaircargo/ne-one.git

FROM eclipse-temurin:25-jdk-noble AS build
ARG NEONE_COMMIT
ARG NEONE_REPO
RUN apt-get update -qq && apt-get install -y -qq --no-install-recommends git >/dev/null && rm -rf /var/lib/apt/lists/*
WORKDIR /src
RUN git init -q . && git remote add origin "$NEONE_REPO" \
    && git fetch -q --depth 1 origin "$NEONE_COMMIT" && git checkout -q FETCH_HEAD \
    && test "$(git rev-parse HEAD)" = "$NEONE_COMMIT"
# The service only; tests are upstream's to run. fast-jar is Quarkus' default
# JVM packaging (build/quarkus-app).
RUN --mount=type=cache,target=/root/.gradle \
    ./gradlew --no-daemon -q :ne-one-service:quarkusBuild -Ppersistence-arch=rdf4j-native -x test

# LMDB is native code (LWJGL). LWJGL's default is to unpack its libraries into
# a writable home directory at startup; this container's filesystem is read
# only, so they are unpacked here, at build time, for the target architecture.
FROM build AS natives
ARG TARGETARCH
RUN apt-get update -qq && apt-get install -y -qq --no-install-recommends unzip >/dev/null \
    && case "$TARGETARCH" in amd64) suffix=linux; dir=linux/x64 ;; arm64) suffix=linux-arm64; dir=linux/arm64 ;; *) echo "unsupported $TARGETARCH" >&2; exit 1 ;; esac \
    && mkdir -p /natives \
    && for jar in /src/ne-one-service/build/quarkus-app/lib/main/org.lwjgl.lwjgl*-natives-$suffix.jar; do unzip -q -o -j "$jar" "$dir/*" -d /natives; done \
    && ls /natives | grep -q liblwjgl.so && ls /natives | grep -q liblwjgl_lmdb.so

FROM eclipse-temurin:25-jre-noble
ARG NEONE_COMMIT
LABEL org.opencontainers.image.title="one-record-interop-neone" \
      org.opencontainers.image.source="https://git.openlogisticsfoundation.org/wg-digitalaircargo/ne-one" \
      org.opencontainers.image.revision="${NEONE_COMMIT}" \
      org.opencontainers.image.licenses="OLFL-1.3"
# A fixed, unprivileged user; the store lives on the one writable volume.
RUN groupadd --system --gid 10001 neone && useradd --system --uid 10001 --gid neone --home-dir /nonexistent neone \
    && mkdir -p /var/lib/neone/rdf4j /licenses && chown -R neone:neone /var/lib/neone
COPY --from=build --chown=root:root /src/ne-one-service/build/quarkus-app /app
COPY --from=natives --chown=root:root /natives /opt/lwjgl
COPY --from=build /src/LICENSE /licenses/NE-ONE-LICENSE-OLFL-1.3
COPY NOTICE /licenses/NOTICE
COPY application.properties /app/config/application.properties
ENV RDF4J_IN_MEMORY=false \
    RDF4J_LOCATION=/var/lib/neone/rdf4j \
    JAVA_TOOL_OPTIONS="-XX:MaxRAMPercentage=75 -Djava.io.tmpdir=/tmp -Djava.library.path=/opt/lwjgl -Dorg.lwjgl.librarypath=/opt/lwjgl -Djava.util.logging.manager=org.jboss.logmanager.LogManager"
USER neone:neone
WORKDIR /app
EXPOSE 8080
ENTRYPOINT ["java", "-jar", "/app/quarkus-run.jar"]
