# DatabaseTestCase

A library to facilitate testing database interactions using PHPUnit 10+.

Features this library currently provides:

- Handles typical database setup and teardown
- Simple representation of a table's rows
- Mechanism for loading fixture data specific to each test

Features this library **does not** currently provide, but plans to:

- Semantic assertions on the state of a database
- Representation for the information schema of a given table

The rest of this document details how to install this library, make use of its `TestCase`, and what database 
connection objects are supported out-of-the-box.

## Installation

[Composer](https://getcomposer.org/) is the only supported method for installing this library.

```
composer require --dev cspray/database-test-case
```

## Usage Guide

Using this library starts by creating a PHPUnit test that extends `Cspray\DatabaseTestCase\DatabaseTestCase`. This class 
overrides various setup and teardown functions provided by PHPUnit to ensure that a database connection is established 
and that database interactions happen against a known state. The `DatabaseTestCase` requires implementations 
to provide a `Cspray\DatabaseTestCase\ConnectionAdapter`. This implementation is ultimately responsible for calls to the 
database required by the testing framework. The `ConnectionAdapter` also provides access to the underlying connection, 
for example a `PDO` instance, that you can use in your code under test. Check out the section titled "Database Connections" 
for `ConnectionAdapter` instances supported out-of-the-box and how you could implement your own.

In our example, going to assume that you have a PostgreSQL database with a table that has 
the following DDL:

```postgresql
CREATE TABLE my_table (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    username VARCHAR(255),
    email VARCHAR(255),
    is_active BOOLEAN
)
```

Now, we can write a series of tests that interact with the database.

```php
<?php declare(strict_types=1);

namespace Cspray\DatabaseTestCase\Demo;

use Cspray\DatabaseTestCase\DatabaseRepresentation\Row;use Cspray\DatabaseTestCase\DatabaseTestCase;
use Cspray\DatabaseTestCase\LoadFixture;use Cspray\DatabaseTestCase\SingleRecordFixture;use PDO;

class MyDemoTest extends DatabaseTestCase {

    // Generally speaking you shouldn't call this method yourself!
    protected static function getConnectionAdapter() : ConnectionAdapter {
        // Be sure to change these configuration values to match your test setup!
        return new PdoConnectionAdapter(
            new ConnectionAdapterConfig(
                database: 'postgres',
                host: 'localhost',
                port: 5432,
                user: 'postgres',
                password: 'postgres'
            ),
            PdoDriver::Postgresql
        );
    }
    
    public function testUnderlyingConnection() : void {
        // You'd pass the value of this method into your code under test
        // Use a different ConnectionAdapter if you aren't working with PDO!
        self::assertInstanceOf(PDO::class, self::getUnderlyingConnection());
    }
    
    public function testShowEmptyTable() : void {
        // DatabaseTestCase provides a method to get a representation of a database table
        $table = $this->getTable('my_table');
        
        // The $table is Countable, the count represents the number of rows in the table
        self::assertCount(0, $table);
        
        // The $table is iterable, each iteration yields a Row, but our database is empty!
        self::assertSame([], iterator_to_array($table));
    }
    
    // Pass any number of Fixture to have corresponding FixtureRecords inserted into 
    // the database before your test starts
    #[LoadFixture(
        new SingleRecordFixture('my_table', ['username' => 'cspray', 'email' => 'cspray@example.com', 'is_active' => true]),
        new SingleRecordFixture('my_table', ['username' => 'dyana', 'email' => 'dyana@example.com', 'is_active' => true])
    )]
    public function testLoadingFixtures() : void {
        $table = $this->getTable('my_table');
        
        self::assertCount(2, $table);
        self::assertContainsOnlyInstancesOf(Row::class, iterator_to_array($table));
        self::assertSame('cspray', $table->getRow(0)->get('username'));
        self::assertSame('dyana@example.com', $table->getRow(1)->get('email'));
        self::assertNull($table->getRow(2));
    }
    
}
```

### TestCase Hooks

There are several critical things the `DatabaseTestCase` must take care of for database tests to work properly. To do that 
we must do something in all the normally used PHPUnit `TestCase` hooks. To be clear those methods are:

- `TestCase::setUpBeforeClass`
- `TestCase::setUp`
- `TestCase::tearDown`
- `TestCase::tearDownAfterClass`

To make sure that `DatabaseTestCase` processes these hooks correctly they have been marked as `final`. There are new 
methods that have been provided that allow for the same effective hooks.

| Old Hook | New Hook                       |
| --- |--------------------------------|
| `TestCase::setUpBeforeClass` | `DatabaseTestCase::beforeAll`  |
| `TestCase::setUp` | `DatabaseTestCase::beforeEach` |
| `TestCase::tearDown` |  `DatabaseTestCase::afterEach` |
| `TestCase::tearDownAfterClass` | `DatabaseTestCase::afterAll`   |

## Database Connections

| Connection Adapter                                     | Connection Instance         | Library                           | Database  | Implemented | 
|--------------------------------------------------------|-----------------------------|-----------------------------------|-----------|------------|
| `Cspray\DatabaseTestCase\PdoConnectionAdapter`         | `PDO`                       | [PHP PDO][pdo]                    | PostgreSQL | :white_check_mark: |
| `Cspray\DatabaseTestCase\PdoConnectionAdapter`         | `PDO`                       | [PHP PDO][pdo]                    | MySQL     | :white_check_mark: |
| `Cspray\DatabaseTestCase\AmpPostgresConnectionAdapter` | `Amp\Postgres\PostgresLink` | [amphp/postgres@^2][amp-postgres] | PostgreSQL | :white_check_mark: | 
| |  `Amp\Mysql\MysqlLink`      | [amphp/mysql@^3][amp-mysql]  | MySQL | :x:                |

[amp-mysql]: https://github.com/amphp/mysql
[amp-postgres]: https://github.com/amphp/postgres
[pdo]: https://php.net/pdo