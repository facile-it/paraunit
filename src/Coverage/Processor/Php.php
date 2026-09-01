<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Serialization\Serializer;

class Php implements CoverageProcessorInterface
{
    private readonly Serializer $phpSerializer;

    public function __construct(
        private readonly OutputFile $targetFile,
    ) {
        $this->phpSerializer = new Serializer();
    }

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        $this->phpSerializer->serialize($this->targetFile->getFilePath(), $codeCoverage);
    }

    public static function getConsoleOptionName(): string
    {
        return 'php';
    }
}
