<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class ErrorParser implements ProcessOutputParserChainElementInterface
{
    const ERROR_REGEX = '/(?:There (?:was|were) \d+ errors?:\n\n)((?:.|\n)+)(?:\n--|FAILURES)/U';

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        $errorsBlob = array();
        preg_match(self::ERROR_REGEX, $process->getOutput(), $errorsBlob);

        if (isset($errorsBlob[1])) {
            $errors = preg_split('/^\d+\) /m', $errorsBlob[1]);
            // il primo è sempre vuoto a causa dello split
            unset($errors[0]);

            foreach ($errors as $singleError) {
                $process->addError(trim($singleError));
            }
        }

        return true;
    }
}
