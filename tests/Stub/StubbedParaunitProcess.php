<?php

namespace Tests\Stub;

use Paraunit\Process\AbstractParaunitProcess;

/**
 * Class StubbedParaunitProcess
 * @package Tests\Stub
 */
class StubbedParaunitProcess extends AbstractParaunitProcess
{
    /**
     * @var string
     */
    protected $output;

    /**
     * @var string
     */
    protected $commandLine;

    /**
     * @var int
     */
    protected $exitCode = 0;

    /**
     * {@inheritdoc}
     */
    public function __construct($commandLine = 'testCommandLine', $uniqueId = null)
    {
        if (is_null($uniqueId)) {
            $uniqueId = md5($commandLine);
        }

        parent::__construct($commandLine, $uniqueId);

        $this->commandLine = $commandLine;
        $this->filename = 'Test.php';
    }

    /**
     * @param bool $isToBeRetried
     */
    public function setIsToBeRetried($isToBeRetried)
    {
        $this->shouldBeRetried = $isToBeRetried;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandLine()
    {
        return $this->commandLine;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @param int $exitCode
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function isTerminated()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function restart()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }
}
