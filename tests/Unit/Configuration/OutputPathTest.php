<?php
declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\OutputPath;
use Tests\BaseUnitTestCase;

/**
 * Class OutputPathTest
 * @package Tests\Unit\Configuration
 */
class OutputPathTest extends BaseUnitTestCase
{
    public function testConstruct()
    {
        $outputPath = new OutputPath('sub/dir/from/relpath');

        $this->assertEquals('sub/dir/from/relpath', $outputPath->getPath());
    }

    public function testWithNull()
    {
        $this->expectException(\Throwable::class);

        new OutputPath(null);
    }

    public function testWithEmptyString()
    {
        $this->expectException(\Throwable::class);

        $outputPath = new OutputPath('');
        $outputPath->getPath();
    }
}
