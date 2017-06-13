<?php
declare(strict_types=1);

namespace Paraunit\Configuration;

use PHPUnit\Framework\BaseTestListener;

/**
 * @deprecated TODO delete
 * Class StaticOutputPath
 * @package Paraunit\Configuration
 */
class StaticOutputPath extends BaseTestListener
{
    /** @var string */
    private static $path;

    /**
     * StaticOutputPath constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        self::$path = $path;
    }

    /**
     * @return string
     * @throws \RuntimeException If not ready
     */
    public static function getPath(): string
    {
        if (null === self::$path) {
            throw new \RuntimeException('Output path not received, not ready!');
        }

        return self::$path;
    }
}
