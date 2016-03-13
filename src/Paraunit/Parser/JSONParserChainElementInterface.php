<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

interface JSONParserChainElementInterface
{
    /**
     * @param ProcessResultInterface $process
     * @param \stdClass $log
     * @return bool True if the parser found a definitive result of a test execution
     */
    public function parsingFoundResult(ProcessResultInterface $process, \stdClass $log);
}
