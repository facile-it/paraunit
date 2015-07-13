<?php

namespace Paraunit\Parser;


use Paraunit\Process\ProcessResultInterface;

class FatalErrorParser implements ProcessOutputParserChainElementInterface
{
    const FATAL_ERROR_REGEX = '/Fatal error(?s:.)*/';

    /**
     * @param ProcessResultInterface $process
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        $fatalError = array();
        preg_match(self::FATAL_ERROR_REGEX, $process->getOutput(), $fatalError);

        if (!empty($fatalError)) {
            $process->addFatalError($fatalError[0]);

            return false;
        } else {
            return true;
        }
    }
}