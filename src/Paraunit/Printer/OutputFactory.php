<?php

namespace Paraunit\Printer;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OutputFactory
 * @package Paraunit\Printer
 */
class OutputFactory
{
    /** @var OutputInterface */
    private static $output;

    /**
     * @return OutputInterface
     * @throws \RuntimeException
     */
    public static function getOutput()
    {
        if (self::$output === null) {
            throw new \RuntimeException('Output instance missing');
        }

        return self::$output;
    }

    /**
     * @param OutputInterface $output
     */
    public static function setOutput(OutputInterface $output)
    {
        self::$output = $output;
    }
}
