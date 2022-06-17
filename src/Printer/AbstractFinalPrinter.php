<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\TestResult\TestResultList;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractFinalPrinter extends AbstractPrinter
{
    /** @var TestResultList */
    protected $testResultList;

    /** @var ChunkSize */
    protected $chunkSize;

    public function __construct(
        TestResultList $testResultList,
        OutputInterface $output,
        ChunkSize $chunkSize
    ) {
        parent::__construct($output);
        $this->testResultList = $testResultList;
        $this->chunkSize = $chunkSize;
    }

    abstract public function onEngineEnd(): void;
}
