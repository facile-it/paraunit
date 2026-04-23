<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;

class PrinterConfiguration
{
    final public const PRINT_ORDER = [
        TestOutcome::AbnormalTermination,
        TestIssue::CoverageFailure,
        TestOutcome::Error,
        TestOutcome::Failure,
        TestIssue::Warning,
        TestIssue::Deprecation,
        TestIssue::PHPUnitDeprecation,
        TestOutcome::NoTestExecuted,
        TestIssue::Risky,
        TestIssue::Notice,
        TestOutcome::Retry,
    ];

    /** @var array<value-of<TestIssue>, bool> */
    private array $shouldPrint;

    public function __construct()
    {
        $this->shouldPrint  = [
            TestIssue::CoverageFailure->value => true,
            TestIssue::Warning->value => false,
            TestIssue::Deprecation->value => false,
            TestIssue::PHPUnitDeprecation->value => false,
            TestIssue::Risky->value => false,
            TestIssue::Notice->value => false,
        ];
    }

    public function shouldPrint(TestIssue $testIssue): bool
    {
        return $this->shouldPrint[$testIssue->value];
    }

    public function setShouldPrint(TestIssue $testIssue, bool $shouldPrint): void
    {
        $this->shouldPrint[$testIssue->value] = $shouldPrint;
    }
}
