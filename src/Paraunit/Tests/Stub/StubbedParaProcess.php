<?php

namespace Paraunit\Tests\Stub;

use Paraunit\Process\ParaunitProcessAbstract;

/**
 * Class StubbedParaProcess
 * @package Paraunit\Tests\Stub
 */
class StubbedParaProcess extends ParaunitProcessAbstract
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
    }

    /**
     * @param bool $isToBeRetried
     */
    public function setIsToBeRetried($isToBeRetried)
    {
        $this->shouldBeRetried = $isToBeRetried;
    }

    /**
     * @return string
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
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return bool
     */
    public function isTerminated()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function start()
    {
        return;
    }

    /**
     * @return $this
     */
    public function restart()
    {
        return;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return false;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }
}
