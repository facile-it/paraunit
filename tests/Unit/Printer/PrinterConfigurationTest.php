<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Printer\PrinterConfiguration;
use Paraunit\TestResult\ValueObject\TestIssue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PrinterConfigurationTest extends TestCase
{
    public function testSetShouldPrintWithAllCases(): void
    {
        $printerConfiguration = new PrinterConfiguration();

        foreach (TestIssue::cases() as $testIssue) {
            $defaultValue = $printerConfiguration->shouldPrint($testIssue);

            $printerConfiguration->setShouldPrint($testIssue, ! $defaultValue);

            $this->assertSame(! $defaultValue, $printerConfiguration->shouldPrint($testIssue));
        }
    }

    #[DataProvider('defaultSettingsDataProvider')]
    public function testDefaultSettings(bool $expected, TestIssue $testIssue): void
    {
        $printerConfiguration = new PrinterConfiguration();

        $this->assertSame($expected, $printerConfiguration->shouldPrint($testIssue));
    }

    /**
     * @return array{bool, TestIssue}[]
     */
    public static function defaultSettingsDataProvider(): array
    {
        return [
            [true, TestIssue::CoverageFailure],
            [false, TestIssue::Deprecation],
            [false, TestIssue::PHPUnitDeprecation],
            [false, TestIssue::Risky],
            [false, TestIssue::Warning],
            [false, TestIssue::Notice],
        ];
    }
}
