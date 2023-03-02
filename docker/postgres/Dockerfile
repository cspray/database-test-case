FROM postgres:15-bullseye

COPY /resources/schemas/postgres.sql /docker-entrypoint-initdb.d/

HEALTHCHECK --interval=5s --start-period=7s --retries=5 --timeout=5s CMD pg_isready -d postgres

USER postgres