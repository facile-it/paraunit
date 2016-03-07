<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class WarningParser implements ProcessOutputParserChainElementInterface
{
    const WARNING_REGEX = '/(?:There (?:was|were) \d+ warnings?:\n\n)((?:.|\n)+)(?:\n--|FAILURES|WARNINGS)/U';

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        $warningsBlob = array();
        preg_match(self::WARNING_REGEX, $process->getOutput(), $warningsBlob);

        if (isset($warningsBlob[1])) {
            $warnings = preg_split('/^\d+\) /m', $warningsBlob[1]);
            // il primo è sempre vuoto a causa dello split
            unset($warnings[0]);

            foreach ($warnings as $singleWarning) {
                $process->addWarning(trim($singleWarning));
            }
        }

        return true;
    }
}
