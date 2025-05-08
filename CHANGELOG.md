# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased Changes

## [0.5.0](https://github.com/cspray/database-testing/releases/tag/0.5.0)

This version represents a MAJOR change to the API and design of this library.
You will NOT be able to simply upgrade to this version and keep everything working. 
It is HIGHLY recommended that you review the README for this version, which discusses 
the new design and how to get started.

### Added

- Created a new `Cspray\DatabaseTestin\DatabaseCleanup\CleanupStrategy` interface to allow more thorough control over how your database is prepared for tests.
- Implemented a thorough "truncate tables" strategy, in addition to the existing "transaction with rollback".

### Changed

- Renamed the namespace from `Cspray\DatabaseTestCase` to `Cspray\DatabaseTesting`.
- Updated the `ConnectionAdapter` interface to be more feature complete, to allow interacting with the database without assumption to the connection type.

### Removed

- All concrete `ConnectionAdapter` have been removed. Adapter-specific library will be provided and should be used instead.
- The PHPUnit-supported `DatabaseTestCase` has been removed. Testing framework-specific library will be provided and should be used instead.

## [0.4.0](https://github.com/cspray/database-testing/releases/tag/0.4.0)

### Added

- Allow all implemented database adapters to provide an existing connection.

## [0.3.0](https://github.com/cspray/database-testing/releases/tag/0.3.0)

### Added

- Allows the `AmpPostgresConnectionAdapter` to use an existing connection

## [0.2.1](https://github.com/cspray/database-testing/releases/tag/0.2.1)

### Changed

- `Cspray\DatabaseTestCase\AmpPostgresConnectionAdapter` no longer prepares and 
  executes insert statements in multiple steps. Makes direct use of `PostgresConnection::execute`

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