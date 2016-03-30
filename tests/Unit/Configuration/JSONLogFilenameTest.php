<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\JSONLogFilename;
use Paraunit\File\TempDirectory;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class JSONLogFilenameTest
 * @package Tests\Unit\Configuration
 */
class JSONLogFilenameTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $process = new StubbedParaunitProcess();
        $config = new TempDirectory();
        $fileName = new JSONLogFilename($config);

        $fileName = $fileName->generate($process);

        $this->assertEquals($config->getTempDirForThisExecution() . '/logs/' . $process->getUniqueId() . '.json.log', $fileName);
    }
}
