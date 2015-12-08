<?php

namespace Paraunit\Tests\Stub;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class QueueConsoleOutput.
 */
class ConsoleOutputStub extends ConsoleOutput implements OutputInterface
{
    protected $outputBuffer;

    public function __construct()
    {
        $this->outputBuffer = '';
    }

    /**
     * @param array|string $messages
     * @param int          $type
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        $this->outputBuffer .= "\n".$messages;
    }

    /**
     * @param array|string $messages
     * @param bool         $newline
     * @param int          $type
     */
    public function write($messages, $newline = false, $type = 0)
    {
        $this->outputBuffer .= $messages;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->outputBuffer;
    }
}
