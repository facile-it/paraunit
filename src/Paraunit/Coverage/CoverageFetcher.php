<?php

namespace Paraunit\Coverage;

use Paraunit\Configuration\TempFileNameFactory;
use Paraunit\Process\AbstractParaunitProcess;

/**
 * Class CoverageFetcher
 * @package Paraunit\Coverage
 */
class CoverageFetcher
{
    /** @var  TempFileNameFactory */
    private $tempFilenameFactory;

    /**
     * CoverageFetcher constructor.
     * @param TempFileNameFactory $tempFilenameFactory
     */
    public function __construct(TempFileNameFactory $tempFilenameFactory)
    {
        $this->tempFilenameFactory = $tempFilenameFactory;
    }

    /**
     * @param AbstractParaunitProcess $process
     * @return \PHP_CodeCoverage
     */
    public function fetch(AbstractParaunitProcess $process)
    {
        // TODO
    }
}
