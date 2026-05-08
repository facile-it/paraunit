<?php

declare(strict_types=1);

namespace Tests\Functional\Configuration;

use Paraunit\Command\CoverageCommand;
use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Configuration\PassThrough;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Facade;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\TextUI\Help;
use Symfony\Component\Console\Input\InputOption;
use Tests\BaseFunctionalTestCase;

class PassThroughTest extends BaseFunctionalTestCase
{
    #[DataProvider('disallowedOptionsDataProvider')]
    public function testDisallowedOptions(string $disallowedOption): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($disallowedOption);

        new PassThrough([$disallowedOption]);
    }

    public function testWarnAboutNewUnsupportedOptions(): void
    {
        $remaining = array_values(array_diff(
            $this->getAllPHPUnitOptions(),
            array_map(static fn(array $value): string => $value[0], self::allowedOptionsDataProvider()),
            array_map(static fn(array $value): string => $value[0], self::disallowedOptionsDataProvider()),
            $this->getAlreadySupportedOptions(),
            $this->getPossibleFutureOptions(),
        ));

        if ($remaining !== []) {
            Facade::emitter()->testTriggeredWarning(
                $this->createPHPUnitTestMethod(),
                count($remaining) . ' unhandled new PHPUnit options: ' . print_r($remaining, true),
                __FILE__,
                __LINE__,
                false,
                false,
            );
        }
    }

    public function testAvoidOverlaps(): void
    {
        $overlapFutureOptions = array_intersect(
            $this->getAlreadySupportedOptions(),
            $this->getPossibleFutureOptions(),
        );

        $this->assertEmpty($overlapFutureOptions, 'Future option has been implemented: ' . print_r($overlapFutureOptions, true));
    }

    /**
     * @return non-empty-list<string>
     */
    private function getAllPHPUnitOptions(): array
    {
        $helpText = (new Help(null, false))->generate();
        preg_match_all('/--[\w-]+/', $helpText, $options);
        $this->assertNotEmpty($options[0]);

        return $options[0];
    }

    /**
     * @return non-empty-list<string>
     */
    private function getAlreadySupportedOptions(): array
    {
        $coverageCommand = new CoverageCommand($this->prophesize(CoverageConfiguration::class)->reveal());

        $supportedOptions = array_map(
            static fn(InputOption $option): string => '--' . $option->getName(),
            $coverageCommand->getDefinition()->getOptions(),
        );

        $supportedOptions[] = '--filter';
        $supportedOptions[] = '--help';
        $supportedOptions[] = '--version';
        // TODO - map coverage modes automatically
        $supportedOptions[] = '--coverage-clover';
        $supportedOptions[] = '--coverage-html';
        $supportedOptions[] = '--coverage-xml';
        $supportedOptions[] = '--coverage-text';
        $supportedOptions[] = '--coverage-crap4j';
        $supportedOptions[] = '--coverage-cobertura';

        return array_values($supportedOptions);
    }

    /**
     * @return array{string}[]
     */
    public static function disallowedOptionsDataProvider(): array
    {
        return [
            // should be impossible? Paraunit can't identify tests in this way
            ['--no-configuration'],
            // Paraunit breaks if you use these
            ['--extension'],
            ['--no-extensions'],
            ['--no-logging'],
            ['--coverage-php'],
            ['--all'],
            // not useful - they do not produce meaningful changes
            ['--teamcity'],
            ['--testdox'],
            // not useful - they skip test execution
            ['--atleast-version'],
            ['--check-version'],
            ['--generate-configuration'],
            ['--migrate-configuration'],
            ['--list-suites'],
            ['--list-groups'],
            ['--list-tests'],
            ['--list-tests-xml'],
            ['--check-php-configuration'],
            // not useful - baseline cannot be merged
            ['--generate-baseline'],
            // not useful - files recap is always useful
            ['--display-incomplete'],
            ['--display-skipped'],
            // not useful - seems noop on PHPUnit too
            ['--display-errors'],
        ];
    }

    /**
     * @return array{0: string, 1?: string}[]
     */
    public static function allowedOptionsDataProvider(): array
    {
        return [
            ['--bootstrap', '<file>'],
            ['--include-path <path(s)>'],
            ['-d <key[=value]>'],
            ['--cache-directory', '<dir>'],
            ['--testsuite', '<name>'],
            ['--group', '<name>'],
            ['--exclude-group', '<name>'],
            ['--covers', '<name>'],
            ['--uses', '<name>'],
            ['--process-isolation'],
            ['--globals-backup'],
            ['--static-backup'],
            ['--strict-coverage'],
            ['--strict-global-state'],
            ['--disallow-test-output'],
            ['--enforce-time-limit'],
            ['--default-time-limit', '<sec>'],
            ['--cache-result'],
            ['--do-not-cache-result'],
            ['--colors', '<flag>'],
            ['--stderr'],
            ['--no-progress'],
            ['--no-output'],
            ['--log-junit', '<file>'],
            ['--log-teamcity', '<file>'],
            ['--testdox-html', '<file>'],
            ['--testdox-text', '<file>'],
            ['--testdox-summary'],
            ['--log-events-text', '<file>'],
            ['--log-events-verbose-text', '<file>'],
            ['--coverage-filter', '<filter>'],
            ['--disable-coverage-ignore'],
            ['--path-coverage'],
            ['--no-coverage'],
            ['--include-path'],
            ['--requires-php-extension'],
            // TODO - add dedicated tests?
            ['--fail-on-incomplete'],
            ['--fail-on-risky'],
            ['--fail-on-skipped'],
            ['--fail-on-warning'],
            ['--fail-on-deprecation'],
            ['--fail-on-notice'],
            ['--dont-report-useless-tests'],
            ['--use-baseline'],
            ['--ignore-baseline'],
            ['--test-suffix'],
            ['--exclude-testsuite'],
        ];
    }

    /**
     * @return list<string>
     */
    private function getPossibleFutureOptions(): array
    {
        return [
            '--exclude-filter',
            '--columns',
            '--display-phpunit-notices', // TODO - map issue
            '--fail-on-all-issues',
            '--fail-on-empty-test-suite',
            '--fail-on-phpunit-deprecation',
            '--fail-on-phpunit-notice',
            '--list-test-files',
            '--reverse-list',
            '--no-results',
            '--warm-coverage-cache',
            '--order-by',
            '--random-order-seed',
            '--only-summary-for-coverage-text',
            '--show-uncovered-for-coverage-text',
            '--with-telemetry',
            '--log-otr',
            '--coverage-openclover',
            '--do-not-report-useless-tests',
            '--fail-on-phpunit-warning',
            '--do-not-fail-on-empty-test-suite',
            '--do-not-fail-on-warning',
            '--do-not-fail-on-risky',
            '--do-not-fail-on-deprecation',
            '--do-not-fail-on-phpunit-deprecation',
            '--do-not-fail-on-phpunit-notice',
            '--do-not-fail-on-phpunit-warning',
            '--do-not-fail-on-notice',
            '--do-not-fail-on-skipped',
            '--do-not-fail-on-incomplete',
            '--include-git-information',
            '--exclude-source-from-xml-coverage',
            '--test-files-file',
            '--resolve-dependencies',
            '--ignore-dependencies',
            '--random-order',
            '--reverse-order',
        ];
    }
}
