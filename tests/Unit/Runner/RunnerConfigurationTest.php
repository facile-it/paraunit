<?php

declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Runner\RunnerConfiguration;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUnitTestCase;

class RunnerConfigurationTest extends BaseUnitTestCase
{
    /**
     * @return \Generator<string, array{TestIssue}>
     */
    public static function issueCasesProvider(): \Generator
    {
        foreach (TestIssue::cases() as $case) {
            yield $case->name => [$case];
        }
    }

    /**
     * @return \Generator<string, array{TestOutcome}>
     */
    public static function outcomeCasesProvider(): \Generator
    {
        foreach (TestOutcome::cases() as $case) {
            yield $case->name => [$case];
        }
    }

    #[DataProvider('issueCasesProvider')]
    #[DataProvider('outcomeCasesProvider')]
    public function testShouldStopOnIsFalseByDefault(TestOutcome|TestIssue $result): void
    {
        $config = new RunnerConfiguration();

        $this->assertFalse($config->shouldStopOn($result));
    }

    #[DataProvider('issueCasesProvider')]
    #[DataProvider('outcomeCasesProvider')]
    public function testSetStopOnEnablesOnlyThatTestOutcome(TestOutcome|TestIssue $result): void
    {
        $config = new RunnerConfiguration();
        $config->setStopOn($result);

        $this->assertTrue($config->shouldStopOn($result));

        foreach ([...TestOutcome::cases(), ...TestIssue::cases()] as $other) {
            $this->assertSame($other === $result, $config->shouldStopOn($other));
        }
    }
}
