<?php

namespace Paraunit\Parser;


use Paraunit\Process\ProcessResultInterface;

class RetryParser implements ProcessOutputParserChainElementInterface
{
    /**
     * @param ProcessResultInterface $process
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        if ($process->isToBeRetried()) {
            $process->setTestResults(array('R'));

            return false;
        } else {
            return true;
        }
    }
}