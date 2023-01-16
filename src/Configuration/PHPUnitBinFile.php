<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class PHPUnitBinFile
{
    // I'm using Paraunit as a vendor package
    private const PHPUNIT_REALPATH_FOR_VENDOR = '/../../../../phpunit/phpunit/phpunit';

    // I'm using Paraunit standalone (developing)
    private const PHPUNIT_REALPATH_FOR_STANDALONE = '/../../vendor/phpunit/phpunit/phpunit';

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?string $phpUnitBin = null;

    /**
     * @throws \RuntimeException If PHPUnit is not found
     */
    public function __construct()
    {
        if (\defined('PARAUNIT_PHAR_FILE')) {
            // Paraunit is running as a standalone PHAR archive
            // PHPUnit is embedded in the archive, self execute it in special mode
            $this->phpUnitBin = PARAUNIT_PHAR_FILE . ' phpunit';

            return;
        }

        if (file_exists(__DIR__ . self::PHPUNIT_REALPATH_FOR_VENDOR)) {
            $this->setPhpUnitBin(__DIR__ . self::PHPUNIT_REALPATH_FOR_VENDOR);

            return;
        }

        if (file_exists(__DIR__ . self::PHPUNIT_REALPATH_FOR_STANDALONE)) {
            $this->setPhpUnitBin(__DIR__ . self::PHPUNIT_REALPATH_FOR_STANDALONE);

            return;
        }

        throw new \RuntimeException('PHPUnit bin not found');
    }

    public function getPhpUnitBin(): string
    {
        return $this->phpUnitBin;
    }

    private function setPhpUnitBin(string $phpUnitBin): void
    {
        $realpath = realpath($phpUnitBin);
        if (! $realpath) {
            throw new \RuntimeException('Unable set PHPUnit binary real path');
        }

        $this->phpUnitBin = $realpath;
    }
}
