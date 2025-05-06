<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\Internal;

use Cspray\DatabaseTesting\DatabaseAwareTest;
use Cspray\DatabaseTesting\Fixture\Fixture;
use Cspray\DatabaseTesting\Fixture\LoadFixture;
use Override;
use ReflectionMethod;

/**
 * @internal
 */
final readonly class FixtureAttributeAwareDatabaseTest implements DatabaseAwareTest {

    private function __construct(
        private string $class,
        private string $method,
        /** @var Fixture */
        private array $fixtures
    ) {}

    public static function fromTestMethodWithPossibleFixtures(string $class, string $method) : DatabaseAwareTest {
        $reflection = new ReflectionMethod($class, $method);
        $loadFixtureReflections = $reflection->getAttributes(LoadFixture::class);
        $fixtures = [];
        if ($loadFixtureReflections !== []) {
            // there can only be one LoadFixture because it is not repeatable
            $fixtures = $loadFixtureReflections[0]->newInstance()->fixtures;
        }

        return new self($class, $method, $fixtures);
    }

    #[Override]
    public function class() : string {
        return $this->class;
    }

    #[Override]
    public function method() : string {
        return $this->method;
    }

    #[Override]
    public function fixtures() : array {
        return $this->fixtures;
    }
}