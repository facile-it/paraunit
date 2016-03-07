<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class SegmentationFaultParser extends AbstractParser implements ProcessOutputParserChainElementInterface
{
    const TAG = 'segfault';
    const TITLE = 'Segmentation Faults';
    const PARSING_REGEX = '/segmentation fault \(core dumped\)/i';

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
