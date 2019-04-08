<?php

declare(strict_types=1);

namespace Paraunit\Command;

use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Runner\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParallelCommand extends Command
{
    /** @var ParallelConfiguration */
    protected $configuration;

    /** @var PHPUnitOption[] */
    private $phpunitOptions;

    /**
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(ParallelConfiguration $configuration)
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
        ];

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
        $this->addOption('logo', null, InputOption::VALUE_NONE, 'Print the Shark logo at the top');

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
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->configuration->buildContainer($input, $output);

        /** @var PHPUnitConfig $config */
        $config = $container->get(PHPUnitConfig::class);
        $this->addPHPUnitOptions($config, $input);

        /** @var Runner $runner */
        $runner = $container->get(Runner::class);

        return $runner->run();
    }

    private function addPHPUnitOptions(PHPUnitConfig $config, InputInterface $input): PHPUnitConfig
    {
        foreach ($this->phpunitOptions as $option) {
            $cliOption = $input->getOption($option->getName());
            if ($this->setOptionValue($option, $cliOption)) {
                $config->addPhpunitOption($option);
            }
        }

        return $config;
    }

    private function setOptionValue(PHPUnitOption $option, $cliOption): bool
    {
        if (! $cliOption) {
            return false;
        }
        if ($option->hasValue()) {
            $option->setValue($cliOption);
        }

        return true;
    }
}
