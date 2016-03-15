<?php

namespace Paraunit\Process;

use Paraunit\TestResult\TestResultInterface;

/**
 * Interface RetryAwareInterface.
 */
interface ProcessResultInterface extends OutputAwareInterface
{
    /** @return bool */
    public function hasAbnormalTermination();

    public function reportAbnormalTermination(TestResultInterface $testResult);

    /** @return bool */
    public function isToBeRetried();

    /** @return string */
    public function getFilename();
}
