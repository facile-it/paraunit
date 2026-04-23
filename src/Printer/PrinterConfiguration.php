<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\PHPUnitConfig;
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

    public function __construct(PHPUnitConfig $config)
    {
        $this->shouldPrint  = [
            TestIssue::CoverageFailure->value => true,
            TestIssue::Warning->value => false,
            TestIssue::Deprecation->value => false,
            TestIssue::PHPUnitDeprecation->value => false,
            TestIssue::Risky->value => false,
            TestIssue::Notice->value => false,
        ];

        $this->importSettingsFrom($config);
    }

    public function setAllShouldPrint(): void
    {
        foreach (TestIssue::cases() as $testIssue) {
            $this->setShouldPrint($testIssue, true);
        }
    }

    public function shouldPrint(TestIssue $testIssue): bool
    {
        return $this->shouldPrint[$testIssue->value];
    }

    public function setShouldPrint(TestIssue $testIssue, bool $shouldPrint): void
    {
        $this->shouldPrint[$testIssue->value] = $shouldPrint;
    }

    private function importSettingsFrom(PHPUnitConfig $config): void
    {
        $valueFromConfig = filter_var($config->getRootAttributeValue('displayDetailsOnAllIssues'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($valueFromConfig === true) {
            $this->setAllShouldPrint();

            return;
        }

        $this->applyFrom($config, TestIssue::Deprecation, 'displayDetailsOnTestsThatTriggerDeprecations');
        $this->applyFrom($config, TestIssue::PHPUnitDeprecation, 'displayDetailsOnPhpunitDeprecations');
        $this->applyFrom($config, TestIssue::Notice, 'displayDetailsOnTestsThatTriggerNotices');
        $this->applyFrom($config, TestIssue::Warning, 'displayDetailsOnTestsThatTriggerWarnings');
    }

    private function applyFrom(PHPUnitConfig $config, TestIssue $issue, string $rootAttribute): void
    {
        $valueFromConfig = filter_var($config->getRootAttributeValue($rootAttribute), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (is_bool($valueFromConfig)) {
            $this->setShouldPrint($issue, $valueFromConfig);
        }
    }
}
