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
    protected function createSubscriber(): PhpDeprecation
    {
        return new PhpDeprecation();
    }

    protected function getExpectedStatus(): LogStatus
    {
        return LogStatus::Deprecation;
    }

    protected function createEvent(): PhpDeprecationTriggered
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
            if (method_exists(IssueTrigger::class, 'from') && class_exists(Code::class)) {
                // trigger of issue categorized by callee & caller from 11.5.54 etc.
                // see https://github.com/sebastianbergmann/phpunit/issues/6434
                $args[] = IssueTrigger::from(Code::FirstParty, Code::FirstParty);
            } elseif (method_exists(IssueTrigger::class, 'unknown')) {
                $args[] = IssueTrigger::unknown();
            } else {
                $this->fail('Unable to work with IssueTrigger, did PHPUnit change something?');
            }
        }

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
