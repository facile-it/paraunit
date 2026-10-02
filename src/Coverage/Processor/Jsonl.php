<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Facade;

class Jsonl implements CoverageProcessorInterface
{
    public function __construct(
        private readonly OutputPath $targetPath,
    ) {}

    /**
     * @throws \RuntimeException
     */
    public function process(CodeCoverage $codeCoverage): void
    {
        if (! method_exists(Facade::class, 'renderJsonl')) {
            throw new \RuntimeException('The --jsonl coverage format requires phpunit/php-code-coverage 14.4 or newer');
        }

        Facade::fromObject($codeCoverage)->renderJsonl($this->targetPath->getPath());
    }

    public static function getConsoleOptionName(): string
    {
        return 'jsonl';
    }
}
