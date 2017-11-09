<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

/**
 * Class PHPUnitBinFile
 * @package Paraunit\Configuration
 */
class PHPUnitBinFile
{
    // I'm using Paraunit as a vendor package
    const PHPUNIT_RELPATH_FOR_VENDOR = '/../../../../../phpunit/phpunit/phpunit';
    // I'm using Paraunit standalone (developing)
    const PHPUNIT_RELPATH_FOR_STANDALONE = '/../../../vendor/phpunit/phpunit/phpunit';

    /** @var string Realpath to PHPUnit bin location */
    private $phpUnitBin;

    /**
     * PHPUnitBinFile constructor.
     * @throws \RuntimeException If PHPUnit is not found
     */
    public function __construct()
    {
        if (defined('PARAUNIT_PHAR_FILE')) {
            // Paraunit is running as a standalone PHAR archive
            // PHPUnit is embedded in the archive, self execute it in special mode
            $this->phpUnitBin = PARAUNIT_PHAR_FILE . ' phpunit';

            return;
        }

        if (file_exists(__DIR__ . self::PHPUNIT_RELPATH_FOR_VENDOR)) {
            $this->phpUnitBin = realpath(__DIR__ . self::PHPUNIT_RELPATH_FOR_VENDOR);

            return;
        }

        if (file_exists(__DIR__ . self::PHPUNIT_RELPATH_FOR_STANDALONE)) {
            $this->phpUnitBin = realpath(__DIR__ . self::PHPUNIT_RELPATH_FOR_STANDALONE);

            return;
        }

        throw new \RuntimeException('PHPUnit bin not found');
    }

    public function getPhpUnitBin(): string
    {
        return $this->phpUnitBin;
    }
}
