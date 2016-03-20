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
            && is_null($input->getOption('coverage-html'))
        ) {
            throw new \InvalidArgumentException('You should choose at least one method of coverage output, between Clover, XML or HTML');
        }

        parent::execute($input, $output);
    }
}
