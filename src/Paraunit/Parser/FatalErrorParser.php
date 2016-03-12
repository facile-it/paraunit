<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class FatalErrorParser extends AbstractParser implements JSONParserChainElementInterface
{
    const TAG = 'fatal';
    const TITLE = 'Fatal Errors';
    const PARSING_REGEX = '/Fatal error(?s:.)*/';

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parsingFoundResult(ProcessResultInterface $process)
    {
        if ($this->parsingFoundSomething($process)) {
            $process->reportFatalError();

            return false;
        }

        return true;
    }
}
