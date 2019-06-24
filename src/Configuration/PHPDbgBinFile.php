<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Symfony\Component\Process\Process;

class PHPDbgBinFile
{
    /** @var string Realpath to the PHPDbg bin location */
    private $phpDbgBin;

    public function __construct()
    {
        $this->phpDbgBin = $this->getPhpDbgBinLocation();
    }

    /**
     * @throws \RuntimeException When PHPDBG is not available
     */
    public function getPhpDbgBin(): string
    {
        if (! $this->isAvailable()) {
            throw new \RuntimeException('PHPDbg is not available!');
        }

        return $this->phpDbgBin;
    }

    public function isAvailable(): bool
    {
        return $this->phpDbgBin !== '';
    }

    private function getPhpDbgBinLocation(): string
    {
        $checkInPath = new Process(['phpdbg', '--version']);
        $checkInPath->run();

        if ($checkInPath->getExitCode() === 0) {
            return 'phpdbg';
        }

        $locator = new Process(['command', '-v', 'phpdbg']);
        $locator->run();

        $trimmed = preg_replace('/\s/', '', $locator->getOutput());
        if (null === $trimmed) {
            throw new \RuntimeException('Preg replace failed');
        }

        return $trimmed;
    }
}
