# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased Changes

## [0.2.0](https://github.com/cspray/database-test-case/releases/tag/0.2.0) - 2023-03-02

### Added

- Introduces a `Cspray\DatabaseTestCase\AbstractConnectionAdapter` for implementing functionality common across all `Cspray\DatabaseTestCase\ConnectionAdapter` implementations.
- Provides the `Cspray\DatabaseTestCase\AmpPostgresConnectionAdapter` for working with the amphp/postgres library.
- Adds support for MySQL in `Cspray\DatabaseTestCase\PdoConnecitonAdapter`. The enum `Cspray\DatabaseTestCase\PdoDriver` has been updated to include this new option.

### Changed

- The `Cspray\DatabaseTestCase\PdoConnectionAdapter` now extends the new `AbstractConnectionAdapter` and has been simplified.
- Added `declare(strict_types=1)` in all files it was missing.

## [0.1.0](https://github.com/cspray/database-test-case/releases/tag/0.1.0) - 2023-03-02

### Added

- Adds a `Cspray\DatabaseTestCase\DatabaseTestCase` that allows for testing database interactions.
- Adds a `Cspray\DatabaseTestCase\ConnectionAdapter` interface that is responsible for actual calls to an underlying connection.
- Provides a `Cspray\DatabaseTestCase\PdoConnectionAdapter` with support for PostgreSQL databases.
- Provides a mechanism for loading fixtures per test, using the Attribute `#[LoadFixture]` and providing an implementation of `Cspray\DatabaseTestCase\Fixture`.
- Provides a mechanism for retrieving the state of a database table at a given point in time with `Cspray\DatabaseTestCase\DatabaseRepresentation\Table`.