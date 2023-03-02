<?php

declare(strict_types=1);

namespace Paraunit\Command;

use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Runner\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ParallelCommand extends Command
{
    /** @var PHPUnitOption[] */
    private readonly array $phpunitOptions;

    public function __construct(protected ParallelConfiguration $configuration)
    {
        $this->phpunitOptions = [
            new PHPUnitOption('whitelist'),
            new PHPUnitOption('disable-coverage-ignore', false),
            new PHPUnitOption('no-coverage', false),

            new PHPUnitOption('filter'),
            new PHPUnitOption('testsuite'),
            new PHPUnitOption('group'),
            new PHPUnitOption('exclude-group'),
            new PHPUnitOption('test-suffix'),

            new PHPUnitOption('dont-report-useless-tests', false),
            new PHPUnitOption('strict-coverage', false),
            new PHPUnitOption('strict-global-state', false),
            new PHPUnitOption('disallow-test-output', false),
            new PHPUnitOption('disallow-resource-usage', false),
            new PHPUnitOption('enforce-time-limit', false),
            new PHPUnitOption('disallow-todo-tests', false),

            new PHPUnitOption('fail-on-warning', false),
            new PHPUnitOption('fail-on-risky', false),

            new PHPUnitOption('process-isolation', false),
            new PHPUnitOption('globals-backup', false),
            new PHPUnitOption('static-backup', false),

            new PHPUnitOption('loader'),
            new PHPUnitOption('repeat'),
            new PHPUnitOption('printer'),

            new PHPUnitOption('do-not-cache-result', false),

            new PHPUnitOption('prepend'),
            new PHPUnitOption('bootstrap'),
            new PHPUnitOption('no-configuration', false),
            new PHPUnitOption('no-logging', false),
            new PHPUnitOption('no-extensions', false),
            new PHPUnitOption('include-path'),
            new PHPUnitOption('stderr', false),
        ];

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('run');
        $this->setDescription('Run all the requested tests in parallel');
        $this->addOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'The PHPUnit XML config filename or path');
        $this->addArgument('stringFilter', InputArgument::OPTIONAL, 'A case-insensitive string to filter tests filename');
        $this->addOption('parallel', null, InputOption::VALUE_REQUIRED, 'Number of concurrent processes to launch', 10);
        $this->addOption('chunk-size', null, InputOption::VALUE_REQUIRED, 'Number of test files in chunk', 1);
        $this->addOption('debug', null, InputOption::VALUE_NONE, 'Print verbose debug output');
        $this->addOption('logo', null, InputOption::VALUE_NONE, 'Print the Shark logo at the top');
        $this->addOption('pass-through', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Inject options to be passed directly to the underlying PHPUnit processes');

        foreach ($this->phpunitOptions as $option) {
            $this->addOption(
                $option->getName(),
                $option->getShortName(),
                $option->hasValue() ? InputOption::VALUE_OPTIONAL : InputOption::VALUE_NONE,
                'Option carried over to every single PHPUnit process, see PHPUnit docs for usage'
            );
        }
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $container = $this->configuration->buildContainer($input, $output);

        /** @var PHPUnitConfig $config */
        $config = $container->get(PHPUnitConfig::class);
        $this->checkForExtension($config, $input, $output);
        $this->assertExtensionIsInstalled($config, $input);
        $this->addPHPUnitOptions($config, $input);

        /** @var Runner $runner */
        $runner = $container->get(Runner::class);

        return $runner->run();
    }

    private function checkForExtension(PHPUnitConfig $config, InputInterface $input, OutputInterface $output): void
    {
        if ($config->isParaunitExtensionRegistered()) {
            return;
        }

        $formatter = $this->getHelper('formatter');
        if (! $formatter instanceof FormatterHelper) {
            throw new \InvalidArgumentException();
        }

        $output->writeln(
            $formatter->formatBlock([
                'Paraunit extension is not registered in the current PHPUnit configuration',
                'Configuration in use: ' . $config->getFileFullPath(),
                '',
                'Do you want to update your configuration automatically? [y/N] ',
            ], 'question', true)
        );

        $question = new ConfirmationQuestion('> ', false);
        $questionHelper = $this->getHelper('question');
        if (! $questionHelper instanceof QuestionHelper) {
            throw new \InvalidArgumentException();
        }

        if ($questionHelper->ask($input, $output, $question)) {
            $config->installExtension();

            $output->writeln(
                $formatter->formatBlock('Configuration updated successfully', 'info', true)
            );
        }
    }

    private function addPHPUnitOptions(PHPUnitConfig $config, InputInterface $input): PHPUnitConfig
    {
        foreach ($this->phpunitOptions as $option) {
            $cliOption = $input->getOption($option->getName());

            if (\is_bool($cliOption)) {
                $cliOption = null;
            }

            if (null !== $cliOption && ! \is_string($cliOption)) {
                throw new \InvalidArgumentException('Invalid option format for CLI option ' . $option->getName() . ': ' . gettype($cliOption));
            }

            if ($this->setOptionValue($option, $cliOption)) {
                $config->addPhpunitOption($option);
            }
        }

        return $config;
    }

    private function setOptionValue(PHPUnitOption $option, ?string $cliOption): bool
    {
        if (! $cliOption) {
            return false;
        }
        if ($option->hasValue()) {
            $option->setValue($cliOption);
        }

        return true;
    }

    protected function assertExtensionIsInstalled(PHPUnitConfig $config, InputInterface $input): void
    {
        if (! $config->isParaunitExtensionRegistered()) {
            $errorMessage = 'Paraunit extension is not registered, unable to proceed';

            if (! $input->isInteractive()) {
                $errorMessage .= PHP_EOL . 'Re-run command in interactive mode to install it with an automatic procedure';
            }

            throw new \RuntimeException($errorMessage);
        }
    }
}
