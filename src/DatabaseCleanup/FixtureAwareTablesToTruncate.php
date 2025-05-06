<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\DatabaseCleanup;

use Cspray\DatabaseTesting\DatabaseAwareTest;
use Override;
use ReflectionMethod;

/**
 * @api
 */
final class FixtureAwareTablesToTruncate implements TablesToTruncate {

    #[Override]
    public function tables(DatabaseAwareTest $test) : array {
        $reflection = new ReflectionMethod($test->class(), $test->method());

        return [
            ...$this->beforeFixtureTables($test, $reflection),
            ...$this->fixtureTables($test),
            ...$this->afterFixtureTables($test, $reflection)
        ];
    }

    private function beforeFixtureTables(DatabaseAwareTest $test, ReflectionMethod $reflection) : array {
        $forceTruncateBeforeAttributes = $reflection->getAttributes(ForceTruncateBeforeFixtureTables::class);

        $beforeFixtureTables = [];
        if ($forceTruncateBeforeAttributes !== []) {
            $beforeFixtureTables = $forceTruncateBeforeAttributes[0]->newInstance()->tablesToTruncate->tables($test);
        }

        return $beforeFixtureTables;
    }

    private function fixtureTables(DatabaseAwareTest $test) : array {
        $fixtureTables = [];
        foreach ($test->fixtures() as $fixture) {
            foreach ($fixture->records() as $fixtureRecord) {
                $fixtureTables[] = $fixtureRecord->table;
            }
        }
        return array_reverse(array_unique($fixtureTables));
    }

    private function afterFixtureTables(DatabaseAwareTest $test, ReflectionMethod $reflection) : array {
        $forceTruncateAfterAttributes = $reflection->getAttributes(ForceTruncateAfterFixtureTables::class);

        $afterFixtureTables = [];
        if ($forceTruncateAfterAttributes !== []) {
            $afterFixtureTables = $forceTruncateAfterAttributes[0]->newInstance()->tablesToTruncate->tables($test);
        }

        return $afterFixtureTables;
    }

}
