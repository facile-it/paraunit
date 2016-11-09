<?php

namespace Tests\Stub;

use Symfony\Component\Console\Output\Output;

/**
 * Class UnformattedOutputStub
 * This class is inspired by Symfony\Component\Console\Output\BufferedOutput, which is not available in Symfony 2.3
 * @package Tests\Stub
 */
class UnformattedOutputStub extends Output
{
    /** @var string */
    protected $buffer;

    public function __construct()
    {
        parent::__construct(null, false);
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        if ($this->buffer === null) {
            $this->buffer = $this->fetch();
        }

        return $this->buffer;
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        $this->buffer .= $message;

        if ($newline) {
            $this->buffer .= "\n";
        }
    }

    /**
     * Empties buffer and returns its content.
     *
     * @return string
     */
    public function fetch()
    {
        $content = $this->buffer;
        $this->buffer = '';

        return $content;
    }
}
