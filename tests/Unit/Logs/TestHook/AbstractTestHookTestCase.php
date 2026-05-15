<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Configuration\EnvVariables;
use Paraunit\Logs\TestHook\AbstractTestHook;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestMethod;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry\GarbageCollectorStatus;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Telemetry\MemoryUsage;
use PHPUnit\Event\Telemetry\Snapshot;
use PHPUnit\Event\Telemetry\SystemGarbageCollectorStatusProvider;
use Tests\BaseUnitTestCase;
use Tests\Stub\TestHookStub;

/**
 * @template T of AbstractTestHook
 * @template E of Event
 */
abstract class AbstractTestHookTestCase extends BaseUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createRandomTmpDir();
        $this->assertTrue(putenv(EnvVariables::PROCESS_UNIQUE_ID . '=123'), 'putenv failed');
    }

    protected function tearDown(): void
    {
        TestHookStub::reset();
        putenv(EnvVariables::PROCESS_UNIQUE_ID . '=');
        parent::tearDown();
    }

    /**
     * @return T<E>
     */
    abstract protected function createSubscriber(): AbstractTestHook;

    abstract protected function createEvent(): Event;

    abstract protected function getExpectedStatus(): LogStatus;

    /**
     * @return non-empty-string|non-empty-string[]|null
     */
    abstract protected function getExpectedMessage(): string|array|null;

    public function testNotify(): void
    {
        $subscriber = $this->createSubscriber();
        $this->assertTrue(method_exists($subscriber, 'notify'), 'notify method missing on ' . $subscriber::class);

        $subscriber->notify($this->createEvent());

        $deserializedlogData = $this->getDeserializedLogData();
        $this->assertEquals($this->getExpectedStatus(), $deserializedlogData->status);

        if ($this->updatesLastTest()) {
            $this->assertEquals($this->getExpectedTestObject(), $deserializedlogData->test);
        } else {
            $this->assertEquals(Test::unknown(), $deserializedlogData->test);
        }

        $expectedMessage = $this->getExpectedMessage();
        if (is_array($expectedMessage)) {
            $this->assertNotEmpty($expectedMessage, 'Wrong data provider, empty expected message list');
            $this->assertNotNull($deserializedlogData->message, 'Missing log message');

            foreach ($expectedMessage as $substring) {
                $this->assertStringContainsString($substring, $deserializedlogData->message);
            }
        } else {
            $this->assertEquals($expectedMessage, $deserializedlogData->message);
        }

        $this->assertFalse($deserializedlogData->isIgnoredByTest(), 'IgnoredByTest flag should be false by default');
        $this->assertFalse($deserializedlogData->isIgnoredByBaseline(), 'IgnoredByBaseline flag should be false by default');
    }

    public function testLogDirNotWritable(): void
    {
        putenv(EnvVariables::LOG_DIR . '=/fake/dir');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot create folder');

        $this->createSubscriber();
    }

    public function testMissingLogDir(): void
    {
        putenv(EnvVariables::LOG_DIR);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('environment variable not set');

        $this->createSubscriber();
    }

    public function testMissingProcessId(): void
    {
        putenv(EnvVariables::PROCESS_UNIQUE_ID);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('environment variable not set');

        $this->createSubscriber();
    }

    final protected function createTelemetryInfo(): Info
    {
        $memoryUsage = MemoryUsage::fromBytes(0);
        $current = new Snapshot(
            HRTime::fromSecondsAndNanoseconds(1, 0),
            $memoryUsage,
            $memoryUsage,
            $this->createGarbageCollectorStatus(),
        );
        $duration = $current->time()->duration($current->time());

        return new Info(
            $current,
            $duration,
            $memoryUsage,
            $duration,
            $memoryUsage,
        );
    }

    protected function updatesLastTest(): bool
    {
        return true;
    }

    private function createGarbageCollectorStatus(): GarbageCollectorStatus
    {
        static $factory;

        $factory ??= new SystemGarbageCollectorStatusProvider();

        return $factory->status();
    }

    protected function getExpectedTestObject(): Test
    {
        return new TestMethod(static::class, 'testNotify');
    }

    protected function getDeserializedLogData(): LogData
    {
        $logFile = $this->getRandomTempDir() . '123.json.log';
        $this->assertFileExists($logFile);
        $logContent = file_get_contents($logFile);
        $this->assertNotFalse($logContent);

        $logData = LogData::parse($logContent);
        $this->assertCount(2, $logData);

        return $logData[0];
    }
}
