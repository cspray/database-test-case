# cspray/database-testing

A low-level, framework-agnostic library for setting up a database suitable 
for automated tests. This library provides the following features:

- The `Cspray\DatabaseTesting\ConnectionAdapter` interface that defines how this library,
  and those that extend it, interact with your database.
- Comprehensive, customizable strategies for cleaning up your database to ensure each 
  test works with a known state.
- Declare fixtures, sets of database records, that are loaded for each test.
- A simple interface to easily introspect the contents of a database
  table in your test suite.
- Database and testing-framework agnostic approach

It cannot be emphasized enough; this library does not provide a turn-key, usable solution 
out-of-the-box. If you use this library directly, instead of one of the extensions that 
targets a specific database connection and testing framework, you'll need to ensure the 
appropriate concrete implementations and framework integration points are created.

## Complete Libraries

Instead of installing this library directly, we recommend that you install one of the 
available options from the "Connection Adapter" and "Testing Extension" list. Chances are, 
you'll need both. If you don't see your database connection type or testing framework
listed, please submit an issue to this repository!

### Connection Adapter

- [cspray/database-testing-pdo](https://github.com/cspray/database-testing-pdo)

### Testing Extension

- [cspray/database-testing-phpunit](https://github.com/cspray/database-testing-phpunit)

## Quick Example

This example is intended to reflect what should be capable with this library. We're going to 
use [cspray/database-testing-phpunit]() as our testing extension, it is ubiquitous and likely the framework you'll start off using with this library.

```php
<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Demo;

use Cspray\DatabaseTesting\DatabaseCleanup\TransactionWithRollback;
use Cspray\DatabaseTesting\Fixture\LoadFixture;
use Cspray\DatabaseTesting\Fixture\SingleRecordFixture;
use Cspray\DatabaseTesting\TestDatabase;
use Cspray\DatabaseTesting\PhpUnit\RequiresTestDatabase;
use PHPUnit\Framework\TestCase;
use PDO;

#[RequiresTestDatabase(
    // this should be implemented by you or provided by an extension to this library
    new MyPdoConnectionAdapterFactory(),
    
    // you could also use Cspray\DatabaseTesting\DatabaseCleanup\TruncateTables
    // or implement your own Cspray\DatabaseTesting\DatabaseCleanup\CleanupStrategy
    new TransactionWithRollback()
)]
final class RepositoryTest extends TestCase {

    private PDO $pdo;
    private MyRepository $myRepository;

    protected function setUp() : void {
        // be sure to use the connection from TestDatabase! depending on CleanupStrategy,
        // using a different connection could wind up with a dirty database state
        $this->pdo = TestDatabase::connection();
        $this->myRepository = new MyRepository($this->pdo);
    }
    
    // populate with more appropriate data. recommended to implement your own 
    // Cspray\DatabaseTesting\Fixture\Fixture to reuse datasets across tests
    #[LoadFixture(
        new SingleRecordFixture('my_table', [
            'name' => 'cspray',
            'website' => 'https://cspray.io'
        ])
    )]
    public function testTableHasCorrectlyLoadedFixtures() : void {
        $table = TestDatabase::table('my_table');
        
        self::assertCount(1, $table);
        
        self::assertSame('cspray', $table->row(0)->get('name'))
        self::assertSame('website', $table->row(0)->get('website'));
    }
    
    public function testTableCanBeReloadedToGetNewlyInsertedRecords() : void {
        $table = TestDatabase::table('my_table');
        
        self::assertCount(0, $table);
        
        $this->myRepository->save(new MyEntity());
    
        $table->reload();
        
        self::assertCount(1, $table);
    }

}
```


