<?php

namespace Paraunit\Command;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Filter\Filter;
use Paraunit\Runner\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParallelCommand.
 */
class ParallelCommand extends Command
{
    /** @var Filter */
    protected $filter;

    /** @var Runner */
    protected $runner;

    /** @var  PHPUnitOption[] */
    private $phpunitOptions;

    /**
     * @param Filter $filter
     * @param Runner $runner
     * @param string $name
     */
    public function __construct(Filter $filter, Runner $runner, $name = 'Paraunit')
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
            new PHPUnitOption('configuration', true, 'c'),
            new PHPUnitOption('no-configuration'),
            new PHPUnitOption('include-path'),
        );

        parent::__construct($name);

        $this->filter = $filter;
        $this->runner = $runner;
    }

    protected function configure()
    {
        $this
            ->setName('run')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Print verbose debug output');

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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $testsuite = null;

        if ($input->getOption('testsuite')) {
            $testsuite = $input->getOption('testsuite');
        }

        $config = $this->createConfig($input);

        $testArray = $this->filter->filterTestFiles($config, $testsuite);

        return $this->runner->run($testArray, $output, $config, $input->getOption('debug'));
    }

    /**
     * @param InputInterface $input
     * @return PHPUnitConfig
     * @throws \InvalidArgumentException
     */
    private function createConfig(InputInterface $input)
    {
        $config = new PHPUnitConfig($input->getOption('configuration'));

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
