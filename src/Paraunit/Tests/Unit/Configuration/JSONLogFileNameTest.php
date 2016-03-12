<?php

namespace Paraunit\Tests\Unit\Configuration;


use Paraunit\Configuration\JSONLogFilename;
use Paraunit\Tests\Stub\StubbedParaProcess;

/**
 * Class JSONLogFileNameTest
 * @package Paraunit\Tests\Unit\Configuration
 */
class JSONLogFileNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $process = new StubbedParaProcess();
        $fileName = new JSONLogFilename();

        $fileName = $fileName->generate($process);

        $this->assertEquals('/dev/shm/paraunit/logs/' . $process->getUniqueId() . '.json.log', $fileName);
    }
}
