<?php

namespace Paraunit\Tests\Stub\PHPUnitOutput\JSONLogs;

/**
 * Class JSONLogStub
 * @package Paraunit\Tests\Stub\PHPUnitOutput\JSONLogs
 */
class JSONLogStub
{
    /**
     * @param string $jsonString The dirty output
     * @return string            The normalized log, as an array of JSON objects
     */
    public static function cleanLog($jsonString)
    {
        $splitted = preg_replace('/\}\{/', '},{', $jsonString);

        return '[' . $splitted . ']';
    }

    public static function get2Errors2Failures()
    {
        return self::getOutputFileContent('2Errors2Failures');
    }

    public static function getAllGreen()
    {
        return self::getOutputFileContent('AllGreen');
    }

    public static function getFatalError()
    {
        return self::getOutputFileContent('FatalError');
    }

    public static function getSegFault()
    {
        return self::getOutputFileContent('SegFault');
    }

    public static function getSingleError()
    {
        return self::getOutputFileContent('SingleError');
    }

    public static function getSingleIncomplete()
    {
        return self::getOutputFileContent('SingleIncomplete');
    }

    public static function getSingleRisky()
    {
        return self::getOutputFileContent('SingleRisky');
    }

    public static function getSingleSkip()
    {
        return self::getOutputFileContent('SingleSkip');
    }

    public static function getSingleWarning()
    {
        return self::getOutputFileContent('SingleWarning');
    }

    /**
     * @param $filename
     *
     * @return string
     */
    protected static function getOutputFileContent($filename)
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $filename . '.json');
    }
}
