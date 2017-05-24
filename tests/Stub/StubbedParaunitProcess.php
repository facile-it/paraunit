<?php

namespace Tests\Stub;

use Paraunit\Process\AbstractParaunitProcess;

/**
 * Class StubbedParaunitProcess
 * @package Tests\Stub
 */
class StubbedParaunitProcess extends AbstractParaunitProcess
{
    /** @var string */
    private $output;

    /** @var string */
    private $commandLine;

    /** @var int */
    private $exitCode;

    /**
     * StubbedParaunitProcess constructor.
     * @param string $filePath
     * @param string $commandLine
     * @param string|null $uniqueId
     */
    public function __construct(
        string $filePath = 'Test.php',
        string $commandLine = 'phpunit Test.php',
        string $uniqueId = null
    ) {
        if (null === $uniqueId) {
            $uniqueId = md5($filePath);
        }

        parent::__construct($filePath, $uniqueId);

        $this->commandLine = $commandLine;
        $this->filename = 'Test.php';
        $this->exitCode = 0;
    }

    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }

    public function setIsToBeRetried(bool $isToBeRetried)
    {
        $this->shouldBeRetried = $isToBeRetried;
    }

    public function getCommandLine(): string
    {
        return $this->commandLine;
    }

    public function setOutput(string $output)
    {
        $this->output = $output;
    }

    public function setExitCode(int $exitCode)
    {
        $this->exitCode = $exitCode;
    }

    public function getOutput(): string
    {
        return $this->output ?? '';
    }

    public function isTerminated(): bool
    {
        return true;
    }

    public function start()
    {
    }

    public function restart()
    {
    }

    public function isRunning(): bool
    {
        return false;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }
}
