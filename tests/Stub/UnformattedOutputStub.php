<?php

declare(strict_types=1);

namespace Tests\Stub;

use Symfony\Component\Console\Output\Output;

class UnformattedOutputStub extends Output
{
    private string $buffer = '';

    public function getOutput(): string
    {
        return $this->buffer;
    }

    /**
     * @inheritDoc
     */
    protected function doWrite($message, $newline)
    {
        $this->buffer .= $message;

        if ($newline) {
            $this->buffer .= "\n";
        }
    }
}
