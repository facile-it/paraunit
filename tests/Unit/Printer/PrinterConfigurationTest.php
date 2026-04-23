<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Printer\PrinterConfiguration;
use Paraunit\TestResult\ValueObject\TestIssue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PrinterConfigurationTest extends TestCase
{
    use ProphecyTrait;

    public function testSetShouldPrintWithAllCases(): void
    {
        $printerConfiguration = new PrinterConfiguration(
            $this->prophesize(PHPUnitConfig::class)->reveal()
        );

        foreach (TestIssue::cases() as $testIssue) {
            $defaultValue = $printerConfiguration->shouldPrint($testIssue);

            $printerConfiguration->setShouldPrint($testIssue, ! $defaultValue);

            $this->assertSame(! $defaultValue, $printerConfiguration->shouldPrint($testIssue));
        }
    }

    public function testSetAllShouldPrint(): void
    {
        $printerConfiguration = new PrinterConfiguration(
            $this->prophesize(PHPUnitConfig::class)->reveal()
        );

        $printerConfiguration->setAllShouldPrint();

        foreach (TestIssue::cases() as $testIssue) {
            $this->assertTrue($printerConfiguration->shouldPrint($testIssue), 'setAllShouldPrint did not set true for ' . $testIssue->name);
        }
    }

    #[DataProvider('defaultSettingsDataProvider')]
    public function testDefaultSettings(bool $expected, TestIssue $testIssue): void
    {
        $printerConfiguration = new PrinterConfiguration(
            $this->prophesize(PHPUnitConfig::class)->reveal()
        );

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

    #[DataProvider('issueTruthyConfigOptionDataProvider')]
    public function testAppliesTruthyPHPUnitConfiguration(TestIssue $issue, string $configOptionName, mixed $configValue): void
    {
        $phpunitConfiguration = $this->prophesize(PHPUnitConfig::class);
        $phpunitConfiguration->getRootAttributeValue(Argument::cetera())
            ->willReturn(null);
        $phpunitConfiguration->getRootAttributeValue($configOptionName)
            ->shouldBeCalled()
            ->willReturn($configValue);
        $printerConfiguration = new PrinterConfiguration(
            $phpunitConfiguration->reveal()
        );

        $this->assertTrue($printerConfiguration->shouldPrint($issue));
    }

    /**
     * @return \Generator<array{TestIssue, non-empty-string, mixed}>
     */
    public static function issueTruthyConfigOptionDataProvider(): \Generator
    {
        foreach (self::getConfigAttributesNames() as $attributeName => $issue) {
            foreach (['true', true, 1] as $truthyValue) {
                yield [$issue, $attributeName, $truthyValue];
            }
        }
    }

    #[DataProvider('issueFalseyConfigOptionDataProvider')]
    public function testAppliesFalseyPHPUnitConfiguration(TestIssue $issue, string $configOptionName, mixed $configValue): void
    {
        $phpunitConfiguration = $this->prophesize(PHPUnitConfig::class);
        $phpunitConfiguration->getRootAttributeValue(Argument::cetera())
            ->willReturn(null);
        $phpunitConfiguration->getRootAttributeValue($configOptionName)
            ->shouldBeCalled()
            ->willReturn($configValue);
        $printerConfiguration = new PrinterConfiguration(
            $phpunitConfiguration->reveal()
        );

        $this->assertFalse($printerConfiguration->shouldPrint($issue));
    }

    /**
     * @return \Generator<array{TestIssue, non-empty-string, mixed}>
     */
    public static function issueFalseyConfigOptionDataProvider(): \Generator
    {
        foreach (self::getConfigAttributesNames() as $attributeName => $issue) {
            foreach (['false', false, 0] as $falseyValue) {
                yield [$issue, $attributeName, $falseyValue];
            }
        }
    }

    public function testAllUsedOptionsAreMappedInPHPUnitXsd(): void
    {
        $xsdPath = dirname(__DIR__, 3) . '/vendor/phpunit/phpunit/phpunit.xsd';
        $this->assertFileExists($xsdPath);

        $document = new \DOMDocument();
        $this->assertTrue($document->load($xsdPath), 'Failed to load PHPUnit XSD');

        $xpath = new \DOMXPath($document);
        foreach (array_keys(self::getConfigAttributesNames()) as $attributeName) {
            $nodes = $xpath->query(
                "//xs:attributeGroup[@name='configAttributeGroup']/xs:attribute[@name='" . $attributeName . "']"
            );

            $this->assertNotFalse($nodes);
            $this->assertCount(1, $nodes, sprintf(
                'Attribute %s is not defined as a root phpunit attribute in vendor/phpunit/phpunit/phpunit.xsd (configAttributeGroup)',
                $attributeName,
            ));

            $attributeElement = $nodes->item(0);
            $this->assertInstanceOf(\DOMElement::class, $attributeElement);
            $this->assertSame('xs:boolean', $attributeElement->getAttribute('type'));
        }
    }

    /**
     * @return array<non-empty-string, TestIssue>
     */
    private static function getConfigAttributesNames(): array
    {
        return [
            'displayDetailsOnTestsThatTriggerDeprecations' => TestIssue::Deprecation,
            'displayDetailsOnPhpunitDeprecations' => TestIssue::PHPUnitDeprecation,
            'displayDetailsOnTestsThatTriggerNotices' => TestIssue::Notice,
            'displayDetailsOnTestsThatTriggerWarnings' => TestIssue::Warning,
        ];
    }
}
