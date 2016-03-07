<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class FatalErrorParser extends AbstractParser implements ProcessOutputParserChainElementInterface
{
    const TAG = 'fatal';
    const TITLE = 'Fatal Errors';
    const PARSING_REGEX = '/Fatal error(?s:.)*/';

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        return ! $this->parsingFoundSomething($process);
    }
}
