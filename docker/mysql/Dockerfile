FROM mysql:8-debian

COPY /resources/schemas/mysql.sql /docker-entrypoint-initdb.d/

HEALTHCHECK --interval=5s --start-period=7s --retries=5 --timeout=5s CMD mysqladmin ping -h localhost

USER mysql
