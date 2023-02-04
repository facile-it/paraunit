<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\ValueObject;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestMethod;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class LogDataTest extends TestCase
{
    use ProphecyTrait;

    public function testParseEmptyLog(): void
    {
        $parsedResult = LogData::parse('');

        $this->assertEmpty($parsedResult);
    }

    public function testLogEnding(): void
    {
        $logData = new LogData(LogStatus::Started, new Test('Foo'), '1');

        $parsedResult = LogData::parse(json_encode($logData, JSON_THROW_ON_ERROR));

        $this->assertCount(2, $parsedResult);
        $logStubEntry = $parsedResult[0];
        $this->assertEquals($logData, $logStubEntry);
        $logEndingEntry = $parsedResult[1];
        $this->assertInstanceOf(LogData::class, $logEndingEntry);
        $this->assertEquals($logData->test, $logEndingEntry->test);
        $this->assertEquals(LogStatus::LogTerminated, $logEndingEntry->status);
        $this->assertNull($logEndingEntry->message);
    }

    public function testNameWithClass(): void
    {
        $logData = new LogData(LogStatus::Passed, new TestMethod(self::class, 'testMethod'), 'Test message');

        $this->assertSame(self::class . '::testMethod', $logData->test->name);
    }

    public function testSerialization(): void
    {
        $logData = new LogData(LogStatus::Passed, new Test('Foo'), 'Test message');

        $parsedResult = LogData::parse(json_encode($logData, JSON_THROW_ON_ERROR));

        $this->assertCount(2, $parsedResult);
        $this->assertInstanceOf(LogData::class, $parsedResult[0]);
        $this->assertEquals($logData, $parsedResult[0]);
        $this->assertSame('Foo', $parsedResult[0]->test->name);
    }

    public function testSerializationError(): void
    {
        $parsedResult = LogData::parse('{}');

        $this->assertCount(2, $parsedResult);
        $this->assertInstanceOf(LogData::class, $parsedResult[0]);
        $this->assertEquals(LogStatus::Unknown, $parsedResult[0]->status);
        $this->assertEquals(Test::unknown(), $parsedResult[0]->test);
        $this->assertIsString($parsedResult[0]->message);
        $this->assertStringStartsWith('Error while parsing Paraunit logs: ', $parsedResult[0]->message);
    }
}
