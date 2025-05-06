<?php declare(strict_types=1);

namespace Cspray\DatabaseTesting\DatabaseCleanup;

#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class ForceTruncateAfterFixtureTables {

    public function __construct(
        public TablesToTruncate $tablesToTruncate,
    ) {}

}