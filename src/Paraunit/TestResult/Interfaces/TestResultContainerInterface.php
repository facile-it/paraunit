<?php

namespace Paraunit\TestResult\Interfaces;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult\Interfaces
 */
interface TestResultContainerInterface
{
    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults();
}
