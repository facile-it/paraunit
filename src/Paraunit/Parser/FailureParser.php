<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class FailureParser implements ProcessOutputParserChainElementInterface
{
    const FAILURE_REGEX = '/(?:There (?:was|were) \d+ failures?:\n\n)((?:.|\n)+)(?=\nFAILURES)/';

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        $failuresBlob = array();
        preg_match(self::FAILURE_REGEX, $process->getOutput(), $failuresBlob);
        if (isset($failuresBlob[1])) {
            $failures = preg_split('/^\d+\) /m', $failuresBlob[1]);
            // il primo è sempre vuoto a causa dello split
            unset($failures[0]);

            foreach ($failures as $singleFailure) {
                $process->addFailure(trim($singleFailure));
            }
        }

        return true;
    }
}
