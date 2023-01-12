<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\ValueObject;

use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\TestStatus;
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

    public function testNameWithClass(): void
    {
        $logData = new LogData(TestStatus::Passed, $this->createTestMethod('FooTest::testMethod'), 'Test message');

        $this->assertSame('Foo::testMethod', $logData->test->name);
    }

    public function testSerialization(): void
    {
        $logData = new LogData(TestStatus::Passed, new Phpt('some/test.phpt'), 'Test message');

        $parsedResult = LogData::parse(json_encode($logData));

        $this->assertCount(1, $parsedResult);
        $this->assertInstanceOf(LogData::class, $parsedResult[0]);
        $this->assertEquals($logData, $parsedResult[0]);
        $this->assertSame('some/test.phpt', $parsedResult[0]->test->name);
    }

    /**
     * @return TestMethod|\Prophecy\Prophecy\ObjectProphecy
     */
    protected function createTestMethod(string $name): TestMethod
    {
        return new TestMethod(
            'Foo',
            'testMethod',
            __FILE__,
            __LINE__,
            TestDox::fromClassNameAndMethodName(self::class, __METHOD__),
            MetadataCollection::fromArray([]),
            TestDataCollection::fromArray([]),
        );
    }
}
