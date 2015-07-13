<?php

namespace Paraunit\Command;

use Paraunit\Filter\Filter;
use Paraunit\Runner\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParallelCommand
 * @package Paraunit\CommandDeadLockTestStub.php
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
    function __construct(Filter $filter, Runner $runner, $name = 'Paraunit')
    {

        parent::__construct($name);

        $this->filter = $filter;
        $this->runner = $runner;
    }

    protected function configure()
    {
        $this
            ->setName('run')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, '', 'phpunit.xml.dist')
            ->addOption('testsuite', null, InputOption::VALUE_REQUIRED, 'Functional|Unit|All')
            ->addOption('debug', null, InputOption::VALUE_REQUIRED, 'Functional|Unit|All');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $testsuite = null;

        if ($input->getOption('testsuite')) {
            $testsuite = $input->getOption('testsuite');
        }

        $config = $input->getOption('config');

        $testArray = $this->filter->filterTestFiles($config, $testsuite);

        return $this->runner->run($testArray, $output);
    }

}