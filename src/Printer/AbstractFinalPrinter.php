<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\TestResult\TestResultList;
use Paraunit\Util\Log\JUnit\JUnit;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractFinalPrinter extends AbstractPrinter
{
    /** @var TestResultList */
    protected $testResultList;

    /** @var ChunkSize */
    protected $chunkSize;

    /** @var JUnit  */
    protected $log;


    public function __construct(
        TestResultList $testResultList,
        OutputInterface $output,
        ChunkSize $chunkSize,
        JUnit $log
    ) {
        parent::__construct($output);
        $this->testResultList = $testResultList;
        $this->chunkSize = $chunkSize;
        $this->log = $log;
    }

    abstract public function onEngineEnd(): void;
}
