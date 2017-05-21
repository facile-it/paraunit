<?php

namespace Paraunit\Configuration;

use PHPUnit\Framework\BaseTestListener;

/**
 * Class StaticOutputPath
 * @package Paraunit\Configuration
 */
class StaticOutputPath extends BaseTestListener
{
    /** @var string */
    private static $path;

    /**
     * StaticOutputPath constructor.
     * @param $path
     */
    public function __construct($path)
    {
        self::$path = $path;
    }

    /**
     * @return string
     * @throws \RuntimeException If not ready
     */
    public static function getPath()
    {
        if (null === self::$path) {
            throw new \RuntimeException('Output path not received, not ready!');
        }

        return self::$path;
    }
}
