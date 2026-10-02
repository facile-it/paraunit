<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use Paraunit\Coverage\Processor\Jsonl;
use SebastianBergmann\CodeCoverage\Report\Facade;
use Tests\BaseUnitTestCase;

class JsonlTest extends BaseUnitTestCase
{
    public function testWriteToFile(): void
    {
        $targetPath = new OutputPath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'jsonl');
        $jsonl = new Jsonl($targetPath);

        $this->assertDirectoryDoesNotExist($targetPath->getPath());

        if (! in_array('renderJsonl', get_class_methods(Facade::class), true)) {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('The --jsonl coverage format requires phpunit/php-code-coverage 14.4 or newer');
        }

        $jsonl->process($this->createCodeCoverage());

        $this->assertDirectoryExists($targetPath->getPath());
        $coverageFile = $targetPath->getPath() . DIRECTORY_SEPARATOR . 'coverage.jsonl';
        $metaFile = $targetPath->getPath() . DIRECTORY_SEPARATOR . 'meta.json';
        $this->assertFileExists($coverageFile);
        $metaContent = $this->getFileContent($metaFile);
        $this->removeDirectory($targetPath->getPath());

        $this->assertStringContainsString('"schemaVersion"', $metaContent);
    }
}
