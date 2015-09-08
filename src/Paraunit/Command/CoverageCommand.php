<?php

namespace Paraunit\Command;

use Paraunit\Filter\Filter;
use Paraunit\Lifecycle\CoverageEvent;
use Paraunit\Runner\Runner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CoverageCommand
 * @package Paraunit\Command
 */
class CoverageCommand extends ParallelCommand
{
    /** @var  EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param Filter $filter
     * @param Runner $runner
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $name
     */
    public function __construct(Filter $filter, Runner $runner, EventDispatcherInterface $eventDispatcher, $name = 'Paraunit with Coverage')
    {
        parent::__construct($filter, $runner, $name);

        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('coverage')
            ->addOption('coverage-clover', 'clover', InputOption::VALUE_REQUIRED, 'Output file for Clover XML coverage result')
            ->addOption('coverage-xml', 'xml', InputOption::VALUE_REQUIRED, 'Output dir for PHPUnit XML coverage result')
            ->addOption('coverage-html', 'html', InputOption::VALUE_REQUIRED, 'Output dir for HTML coverage result');
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
        if (is_null($input->getOption('coverage-clover'))
            && is_null($input->getOption('coverage-xml'))
            && is_null($input->getOption('coverage-html')))
        {
            throw new \InvalidArgumentException('You should choose at least one method of coverage output, between Clover, XML or HTML');
        }

        parent::execute($input, $output);
    }
}
