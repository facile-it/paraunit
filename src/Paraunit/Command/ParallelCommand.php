<?php

namespace Paraunit\Command;

use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Filter\Filter;
use Paraunit\Runner\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParallelCommand.
 */
class ParallelCommand extends Command
{
    /** @var ParallelConfiguration */
    protected $configuration;

    /** @var  PHPUnitOption[] */
    private $phpunitOptions;

    /**
     * ParallelCommand constructor.
     * @param ParallelConfiguration $configuration
     */
    public function __construct(ParallelConfiguration $configuration)
    {
        $this->phpunitOptions = array(
            new PHPUnitOption('filter'),
            new PHPUnitOption('testsuite'),
            new PHPUnitOption('group'),
            new PHPUnitOption('exclude-group'),
            new PHPUnitOption('test-suffix'),

            new PHPUnitOption('report-useless-tests', false),
            new PHPUnitOption('strict-global-state', false),
            new PHPUnitOption('disallow-test-output', false),
            new PHPUnitOption('enforce-time-limit', false),
            new PHPUnitOption('disallow-todo-tests', false),

            new PHPUnitOption('process-isolation', false),
            new PHPUnitOption('no-globals-backup', false),
            new PHPUnitOption('static-backup', false),

            new PHPUnitOption('loader'),
            new PHPUnitOption('repeat'),
            new PHPUnitOption('printer'),

            new PHPUnitOption('bootstrap'),
            new PHPUnitOption('no-configuration'),
            new PHPUnitOption('include-path'),
        );

        parent::__construct();
        $this->configuration = $configuration;
    }

    protected function configure()
    {
        $this->setName('run');
        $this->setDescription('Run all the requested tests in parallel');
        $this->addOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'The PHPUnit XML config filename or path');
        $this->addArgument('stringFilter', InputArgument::OPTIONAL, 'A case-insensitive string to filter tests filename');
        $this->addOption('parallel', null, InputOption::VALUE_REQUIRED, 'Number of concurrent processes to launch', 10);
        $this->addOption('debug', null, InputOption::VALUE_NONE, 'Print verbose debug output');

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
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->configuration->buildContainer($input);

        /** @var PHPUnitConfig $config */
        $config = $container->get('paraunit.configuration.phpunit_config');
        $this->addPHPUnitOptions($config, $input);

        /** @var Runner $runner */
        $runner = $container->get('paraunit.runner.runner');

        return $runner->run($output, $input->getOption('debug'));
    }

    /**
     * @param PHPUnitConfig $config
     * @param InputInterface $input
     * @return PHPUnitConfig
     */
    private function addPHPUnitOptions(PHPUnitConfig $config, InputInterface $input)
    {
        foreach ($this->phpunitOptions as $option) {
            $cliOption = $input->getOption($option->getName());
            if ($cliOption) {
                $option->setValue($cliOption);
                $config->addPhpunitOption($option);
            }
        }

        return $config;
    }
}
