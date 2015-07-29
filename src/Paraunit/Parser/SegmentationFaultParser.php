<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;

class SegmentationFaultParser implements ProcessOutputParserChainElementInterface
{
    const SEGFAULT_REGEX = '/segmentation fault \(core dumped\)/i';

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        $output = $process->getOutput();

        // SEGFAULT
        $segFault = array();
        preg_match(self::SEGFAULT_REGEX, $output, $segFault);
        if (!empty($segFault)) {
            $process->addSegmentationFault($process->getFilename().' - '.$segFault[0]);

            return false;
        }

        return true;
    }
}
