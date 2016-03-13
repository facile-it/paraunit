<?php

namespace Paraunit\Tests\Unit\Configuration;


use Paraunit\Configuration\JSONLogFilename;
use Paraunit\File\TempDirectory;
use Paraunit\Tests\Stub\StubbedParaProcess;

/**
 * Class JSONLogFilenameTest
 * @package Paraunit\Tests\Unit\Configuration
 */
class JSONLogFilenameTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $process = new StubbedParaProcess();
        $config = new TempDirectory();
        $fileName = new JSONLogFilename($config);

        $fileName = $fileName->generate($process);

        $this->assertEquals($config->getTempDirForThisExecution() . '/logs/' . $process->getUniqueId() . '.json.log', $fileName);
    }
}
