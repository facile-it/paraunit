<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\JSON;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Parser\JSON\TestHook\Successful;
use PHPUnit\Framework\TestSuite;
use Tests\BaseFunctionalTestCase;

class AbstractTestHookTest extends BaseFunctionalTestCase
{
    protected function setup(): void
    {
        parent::setup();

        $this->createRandomTmpDir();
    }

    public function testWrite(): void
    {
        $testName = __CLASS__;
        $testSuite = $this->prophesize(TestSuite::class);
        $testSuite->getName()
            ->willReturn($testName);
        $testSuite->count()
            ->willReturn(1);

        putenv(EnvVariables::PROCESS_UNIQUE_ID . '=' . md5(__FILE__));
        $logFullPath = $this->getRandomTempDir() . md5(__FILE__) . '.json.log';

        $hook = new Successful();

        $hook->executeAfterSuccessfulTest('testname', 0.1);

        $content = $this->getFileContent($logFullPath);
        $this->assertJson($content);
        $decodedJson = json_decode($content, true);
        $this->assertEquals(['event' => 'suiteStart', 'suite' => $testName, 'tests' => 1], $decodedJson);
    }
}
