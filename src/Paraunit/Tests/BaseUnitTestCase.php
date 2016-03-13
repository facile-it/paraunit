<?php

namespace Paraunit\Tests;
use Paraunit\Tests\Stub\PHPUnitOutput\JSONLogs\JSONLogStub;

/**
 * Class BaseUnitTestCase
 * @package Paraunit\Tests
 */
abstract class BaseUnitTestCase extends BaseTestCase
{
    /**
     * @param $testOutput
     * @return \stdClass
     */
    protected function getLogWithStatus($status, $testOutput = null)
    {
        $jsonLogs = JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR);
        $logs = json_decode($jsonLogs);
        foreach ($logs as $log) {
            if ($log->event == 'test' && $log->status == $status) {
                if ($testOutput) {
                    $log->message = $testOutput;
                }

                return $log;
            }
        }

        $this->fail('Feasible log message not found for test');
    }
}
