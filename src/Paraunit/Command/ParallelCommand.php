<?php

namespace Paraunit\Command;

use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\PHPUnitConfigFile;
use Symfony\Component\Console\Command\Command;
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

    /**
     * ParallelCommand constructor.
     * @param ParallelConfiguration $configuration
     */
    public function __construct(ParallelConfiguration $configuration)
    {
        parent::__construct();
        $this->configuration = $configuration;
    }

    protected function configure()
    {
        $this
            ->setName('run')
            ->addOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'The PHPUnit XML config file', PHPUnitConfigFile::DEFAULT_FILE_NAME)
            ->addOption('testsuite', null, InputOption::VALUE_REQUIRED, 'Choice a specific testsuite from your XML config file')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Print verbose debug output');
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
        $testsuite = null;

        if ($input->getOption('testsuite')) {
            $testsuite = $input->getOption('testsuite');
        }

        $configOption = $input->getOption('configuration');
        $config = new PHPUnitConfigFile($configOption);

        $container = $this->configuration->buildContainer();

        $filter = $container->get('paraunit.filter.filter');
        $testArray = $filter->filterTestFiles($config, $testsuite);
        $runner = $container->get('paraunit.runner.runner');

        return $runner->run($testArray, $output, $config, $input->getOption('debug'));
    }
}
