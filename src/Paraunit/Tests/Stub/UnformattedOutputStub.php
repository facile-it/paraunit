<?php

namespace Paraunit\Tests\Stub;

use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class UnformattedOutputStub
 * @package Paraunit\Tests\Stub
 */
class UnformattedOutputStub extends BufferedOutput
{
    /** @var string */
    protected $outputBuffer;

    public function __construct()
    {
        parent::__construct(null, false);
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        if (is_null($this->outputBuffer)) {
            $this->outputBuffer = $this->fetch();
        }

        return $this->outputBuffer;
    }
}
