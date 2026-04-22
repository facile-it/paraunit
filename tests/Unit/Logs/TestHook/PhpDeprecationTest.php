<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\PhpDeprecation;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Code\IssueTrigger\Code;
use PHPUnit\Event\Code\IssueTrigger\IssueTrigger;
use PHPUnit\Event\Test\PhpDeprecationTriggered;

/**
 * @template-extends AbstractTestHookTestCase<PhpDeprecation, PhpDeprecationTriggered>
 */
class PhpDeprecationTest extends AbstractTestHookTestCase
{
    public function testIgnoredByTest(): void
    {
        $this->createSubscriber()->notify($this->createEvent(ignoredByTest: true));

        $logData = $this->getDeserializedLogData();
        $this->assertTrue($logData->isIgnoredByTest(), 'ignoredByTest flag should be true');
    }

    public function testIgnoredByBaseline(): void
    {
        $this->createSubscriber()->notify($this->createEvent(ignoredByBaseline: true));

        $logData = $this->getDeserializedLogData();
        $this->assertTrue($logData->isIgnoredByBaseline(), 'ignoredByBaseline flag should be true');
    }

    protected function createSubscriber(): PhpDeprecation
    {
        return new PhpDeprecation();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Deprecation;
    }

    protected function createEvent(bool $ignoredByBaseline = false, bool $ignoredByTest = false): PhpDeprecationTriggered
    {
        $args = [
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            $this->getExpectedMessage(),
            'testFile.php',
            123,
            false,
            $ignoredByBaseline,
            $ignoredByTest,
            IssueTrigger::from(Code::FirstParty, Code::FirstParty),
        ];

        return new PhpDeprecationTriggered(...$args);
    }

    /**
     * @return non-empty-string
     */
    protected function getExpectedMessage(): string
    {
        return 'test message';
    }
}
