<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\ChunkSize;
use Tests\BaseUnitTestCase;

class ChunkSizeTest extends BaseUnitTestCase
{
    public function testValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be 1 or greater');

        new ChunkSize(0);
    }
}
