<?php

namespace Paraunit\Command;

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

    /** @var array */
    protected $configuration;

    /**
     * @param Filter $filter
     * @param Runner $runner
     * @param array $configuration
     * @param string $name
     */
    public function __construct(Filter $filter, Runner $runner, array $configuration, $name = 'Paraunit')
    {
        parent::__construct($name);

        $this->filter = $filter;
        $this->runner = $runner;
        $this->configuration = $configuration;
    }

    protected function configure()
    {
        $this
            ->setName('run')
            ->addOption('testsuite', 't', InputOption::VALUE_OPTIONAL, 'Choice a specific testsuite from your XML config file', null)
            ->addOption('configuration', 'c', InputOption::VALUE_OPTIONAL, 'The PHPUnit XML config file', false)
            ->addOption('processes', 'p', InputOption::VALUE_OPTIONAL, 'The number of processes swarmed by paraunit', false)
            ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Print verbose debug output');
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

        $config = $this->configuration['PARAUNIT_PHPUNIT_XML_PATH'];
        if ($input->getOption('configuration')) {
            $config = $input->getOption('configuration');
        }

        $pNumber = $this->configuration['PARAUNIT_MAX_PROCESS_NUMBER'];
        if ($input->getOption('configuration')) {
            $pNumber = $input->getOption('configuration');
        }

        $testArray = $this->filter->filterTestFiles($config, $testsuite);

        $this->runner->setMaxProcessNumber($pNumber);
        $this->runner->setPhpunitConfigFile($config);

        if ($input->getOption('debug')) {
            $this->runner->enableDebugMode();
        }

        return $this->runner->run($testArray, $output);
    }
}
