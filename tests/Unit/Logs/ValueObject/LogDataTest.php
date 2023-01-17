<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\ValueObject;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Code\Phpt;
use PHPUnit\Event\Code\TestDox;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\TestData\TestDataCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\MetadataCollection;
use Prophecy\PhpUnit\ProphecyTrait;

class LogDataTest extends TestCase
{
    use ProphecyTrait;

    public function testLogEnding(): void
    {
        $parsedResult = LogData::parse('');

        $this->assertCount(1, $parsedResult);
        $logEndingEntry = $parsedResult[0];
        $this->assertInstanceOf(LogData::class, $logEndingEntry);
        $this->assertEquals(Test::unknown(), $logEndingEntry->test);
        $this->assertEquals(TestStatus::LogTerminated, $logEndingEntry->status);
        $this->assertNull($logEndingEntry->message);
    }

    public function testNameWithClass(): void
    {
        $logData = new LogData(TestStatus::Passed, $this->createTestMethod(), 'Test message');

        $this->assertSame(self::class . '::testMethod', $logData->test->name);
    }

    public function testSerialization(): void
    {
        $logData = new LogData(TestStatus::Passed, new Phpt('some/test.phpt'), 'Test message');

        $parsedResult = LogData::parse(json_encode($logData, JSON_THROW_ON_ERROR));

        $this->assertCount(2, $parsedResult);
        $this->assertInstanceOf(LogData::class, $parsedResult[0]);
        $this->assertEquals($logData, $parsedResult[0]);
        $this->assertSame('some/test.phpt', $parsedResult[0]->test->name);
    }

    public function testSerializationError(): void
    {
        $parsedResult = LogData::parse('{}');

        $this->assertCount(2, $parsedResult);
        $this->assertInstanceOf(LogData::class, $parsedResult[0]);
        $this->assertEquals(TestStatus::Unknown, $parsedResult[0]->status);
        $this->assertEquals(Test::unknown(), $parsedResult[0]->test);
        $this->assertIsString($parsedResult[0]->message);
        $this->assertStringStartsWith('Error while parsing Paraunit logs: ', $parsedResult[0]->message);
    }

    private function createTestMethod(): TestMethod
    {
        return new TestMethod(
            self::class,
            'testMethod',
            __FILE__,
            __LINE__,
            TestDox::fromClassNameAndMethodName(self::class, __METHOD__),
            MetadataCollection::fromArray([]),
            TestDataCollection::fromArray([]),
        );
    }
}
