<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\TestHook;

use Paraunit\Logs\TestHook\Deprecation;
use Paraunit\Logs\ValueObject\LogStatus;
use PHPUnit\Event\Code\IssueTrigger\Code;
use PHPUnit\Event\Code\IssueTrigger\IssueTrigger;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Framework\Assert;

/**
 * @template-extends AbstractTestHookTestCase<Deprecation, DeprecationTriggered>
 */
class DeprecationTest extends AbstractTestHookTestCase
{
    protected function createSubscriber(): Deprecation
    {
        return new Deprecation();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Deprecation;
    }

    protected function createEvent(): DeprecationTriggered
    {
        $args = [
            $this->createTelemetryInfo(),
            $this->createPHPUnitTestMethod(),
            $this->getExpectedMessage(),
            'testFile.php',
            123,
            false,
            false,
            false,
        ];

        if (class_exists(IssueTrigger::class)) {
            if (method_exists(IssueTrigger::class, 'from')) {
                // trigger of issue categorized by callee & caller from 11.5.54 etc.
                // see https://github.com/sebastianbergmann/phpunit/issues/6434
                $args[] = IssueTrigger::from(Code::FirstParty, Code::FirstParty);
            } else {
                $args[] = IssueTrigger::unknown();
            }

            if (method_exists(Assert::class, 'assertContainsOnlyArray')) {
                // $stacktrace arg added in 11.5.0
                $args[] = '\fake\stacktrace:123';
            }
        }

        return new DeprecationTriggered(...$args);
    }

    /**
     * @return non-empty-string
     */
    protected function getExpectedMessage(): string
    {
        return 'test message';
    }
}
