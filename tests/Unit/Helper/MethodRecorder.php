<?php

namespace Cspray\DatabaseTestCase\Tests\Unit\Helper;

class MethodRecorder {

    private array $recordedCalls = [];

    public function getRecordedCalls() : array {
        return $this->recordedCalls;
    }

    public function record(string $method, array $args) : void {
        $this->recordedCalls[] = [$method, $args];
    }

}
