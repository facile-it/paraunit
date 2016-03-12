<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class WarningParser extends AbstractParser implements JSONParserChainElementInterface
{
    const TAG = 'warning';
    const TITLE = 'Warnings';
    const PARSING_REGEX = '/(?:There (?:was|were) \d+ warnings?:\n\n)((?:.|\n)+)(?:\n--|FAILURES|WARNINGS)/U';

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parsingFoundResult(ProcessResultInterface $process)
    {
        $this->storeParsedBlocks($process);

        return true;
    }
}
