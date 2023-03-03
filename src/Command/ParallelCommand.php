<?php

declare(strict_types=1);

namespace Paraunit\Command;

use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
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
    public function __construct(protected ParallelConfiguration $configuration)
    {
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
        $this->addOption('testsuite', null, InputOption::VALUE_REQUIRED, 'Only run tests from the specified test suite');
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
