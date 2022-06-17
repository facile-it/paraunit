<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;

class Log
{
    public function __construct(
        public readonly TestStatus $status,
        public readonly Test $test,
        public readonly ?string $message,
    ) {
    }
}
