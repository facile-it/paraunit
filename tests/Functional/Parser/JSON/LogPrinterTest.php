<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\JSON;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Parser\JSON\LogPrinter;
use PHPUnit\Framework\TestSuite;
use Tests\BaseFunctionalTestCase;

/**
 * Class LogPrinterTest
 * @package Tests\Functional\Parser\JSON
 */
class LogPrinterTest extends BaseFunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createRandomTmpDir();
    }

    public function testWrite()
    {
        $testName = __CLASS__;
        $testSuite = $this->prophesize(TestSuite::class);
        $testSuite->getName()
            ->willReturn($testName);
        $testSuite->count()
            ->willReturn(1);

        putenv(EnvVariables::PROCESS_UNIQUE_ID . '=' . md5(__FILE__));
        $logFullPath = $this->getRandomTempDir() . md5(__FILE__) . '.json.log';

        $printer = new LogPrinter();

        $printer->startTestSuite($testSuite->reveal());

        $content = $this->getFileContent($logFullPath);
        $this->assertJson($content);
        $decodedJson = json_decode($content, true);
        $this->assertEquals(['event' => 'suiteStart', 'suite' => $testName, 'tests' => 1], $decodedJson);
    }
}
