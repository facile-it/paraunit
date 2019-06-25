<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\TestResult\TestResultList;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractFinalPrinter extends AbstractPrinter
{
    /** @var TestResultList */
    protected $testResultList;

    public function __construct(TestResultList $testResultList, OutputInterface $output)
    {
        parent::__construct($output);
        $this->testResultList = $testResultList;
    }

    abstract public function onEngineEnd(): void;
}
