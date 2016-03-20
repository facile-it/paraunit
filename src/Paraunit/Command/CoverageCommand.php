<?php

namespace Paraunit\Command;

use Paraunit\Configuration\ParallelCoverageConfiguration;
use Paraunit\Lifecycle\CoverageEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CoverageCommand
 * @package Paraunit\Command
 */
class CoverageCommand extends ParallelCommand
{
    /**
     * ParallelCommand constructor.
     * @param ParallelCoverageConfiguration $configuration
     */
    public function __construct(ParallelCoverageConfiguration $configuration)
    {
        parent::__construct($configuration);
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('coverage')
            ->addOption('clover', null, InputOption::VALUE_REQUIRED, 'Output file for Clover XML coverage result')
            ->addOption('xml',    null, InputOption::VALUE_REQUIRED, 'Output dir for PHPUnit XML coverage result')
            ->addOption('html',   null, InputOption::VALUE_REQUIRED, 'Output dir for HTML coverage result');
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
        if (is_null($input->getOption('clover'))
            && is_null($input->getOption('xml'))
            && is_null($input->getOption('html'))
        ) {
            throw new \InvalidArgumentException('You should choose at least one method of coverage output, between Clover, XML or HTML');
        }

        parent::execute($input, $output);
    }
}
