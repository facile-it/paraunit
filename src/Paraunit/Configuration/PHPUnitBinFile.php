<?php

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
     */
    public function __construct()
    {
        if (file_exists(__DIR__.self::PHPUNIT_RELPATH_FOR_VENDOR)) {
            $this->phpUnitBin = __DIR__.self::PHPUNIT_RELPATH_FOR_VENDOR;
        } elseif (file_exists(__DIR__.self::PHPUNIT_RELPATH_FOR_STANDALONE)) {
            $this->phpUnitBin = __DIR__.self::PHPUNIT_RELPATH_FOR_STANDALONE;
        } else {
            throw new \Exception('PHPUnit bin not found');
        }

        $this->phpUnitBin = realpath($this->phpUnitBin);
    }

    /**
     * @return string
     */
    public function getPhpUnitBin()
    {
        return $this->phpUnitBin;
    }
}
