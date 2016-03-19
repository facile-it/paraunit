<?php

namespace Tests\Unit\Configuration;


use Paraunit\Configuration\TempFileNameFactory;
use Paraunit\File\TempDirectory;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class TempFilenameFactoryTest
 * @package Tests\Unit\Configuration
 */
class TempFilenameFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFilenameForLog()
    {
        $processUniqueId = 'asdasdasdasd';
        $tempDir = new TempDirectory();
        $tempFileNameFactory = new TempFileNameFactory($tempDir);

        $tempFileNameFactory = $tempFileNameFactory->getFilenameForLog($processUniqueId);

        $expected = $tempDir->getTempDirForThisExecution() . '/logs/' . $processUniqueId . '.json.log';
        $this->assertEquals($expected, $tempFileNameFactory);
    }
}
