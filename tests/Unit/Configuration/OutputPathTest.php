<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\OutputPath;
use Tests\BaseUnitTestCase;

class OutputPathTest extends BaseUnitTestCase
{
    public function testConstruct(): void
    {
        $outputPath = new OutputPath('sub/dir/from/relpath');

        $this->assertEquals('sub/dir/from/relpath', $outputPath->getPath());
    }

    public function testWithEmptyString(): void
    {
        $this->expectException(\Throwable::class);

        $outputPath = new OutputPath('');
        $outputPath->getPath();
    }
}
