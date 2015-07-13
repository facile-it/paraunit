<?php

namespace Paraunit\Parser;


use Paraunit\Process\ProcessResultInterface;

interface ProcessOutputParserChainElementInterface
{
    /**
     * @param ProcessResultInterface $process
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process);
}