<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\TestResult\TestResultList;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractFinalPrinter extends AbstractPrinter
{
    public function __construct(
        protected TestResultList $testResultList,
        OutputInterface $output,
        protected ChunkSize $chunkSize
    ) {
        parent::__construct($output);
    }

    abstract public function onEngineEnd(): void;
}
