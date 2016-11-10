<?php

namespace Paraunit\Command;

use Paraunit\Configuration\PHPUnitConfig;
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

    /**
     * @param Filter $filter
     * @param Runner $runner
     * @param string $name
     */
    public function __construct(Filter $filter, Runner $runner, $name = 'Paraunit')
    {
        parent::__construct($name);

        $this->filter = $filter;
        $this->runner = $runner;
    }

    protected function configure()
    {
        $this
            ->setName('run')
            ->addOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'The PHPUnit XML config file', PHPUnitConfig::DEFAULT_FILE_NAME)
            ->addOption('testsuite', null, InputOption::VALUE_REQUIRED, 'Choice a specific testsuite from your XML config file')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Print verbose debug output');
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

        $configOption = $input->getOption('configuration');
        $config = new PHPUnitConfig($configOption);

        $testArray = $this->filter->filterTestFiles($config, $testsuite);

        return $this->runner->run($testArray, $output, $config, $input->getOption('debug'));
    }
}
