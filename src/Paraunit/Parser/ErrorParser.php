<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class ErrorParser extends AbstractParser implements ProcessOutputParserChainElementInterface
{
    const TAG = 'error';
    const TITLE = 'Errors';
    const PARSING_REGEX = '/(?:There (?:was|were) \d+ errors?:\n\n)((?:.|\n)+)(?:\n--|FAILURES)/U';

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        $this->storeParsedBlocks($process);

        return true;
    }
}
