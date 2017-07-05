<?php
declare(strict_types=1);

namespace Paraunit\Command;

use Paraunit\Configuration\CoverageConfiguration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CoverageCommand
 * @package Paraunit\Command
 */
class CoverageCommand extends ParallelCommand
{
    const COVERAGE_METHODS = [
        'clover',
        'html',
        'xml',
        'text',
        'text-to-console',
        'crap4j',
        'php',
    ];

    /**
     * ParallelCommand constructor.
     * @param CoverageConfiguration $configuration
     */
    public function __construct(CoverageConfiguration $configuration)
    {
        parent::__construct($configuration);
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('coverage');
        $this->setDescription('Fetch the coverage of your tests in parallel');
        $this->addOption('clover', null, InputOption::VALUE_REQUIRED, 'Output file for Clover XML coverage result');
        $this->addOption('xml', null, InputOption::VALUE_REQUIRED, 'Output dir for PHPUnit XML coverage result');
        $this->addOption('html', null, InputOption::VALUE_REQUIRED, 'Output dir for HTML coverage result');
        $this->addOption('text', null, InputOption::VALUE_REQUIRED, 'Output file for text coverage result');
        $this->addOption('text-to-console', null, InputOption::VALUE_NONE, 'Output text coverage directly to console');
        $this->addOption('crap4j', null, InputOption::VALUE_REQUIRED, 'Output file for Crap4j coverage result');
        $this->addOption('php', null, InputOption::VALUE_REQUIRED, 'Output file for PHP coverage result');
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
        if (! $this->hasChosenCoverageMethod($input)) {
            $coverageMethods = implode(self::COVERAGE_METHODS, ', ');

            throw new \InvalidArgumentException('You should choose at least one method of coverage output between ' . $coverageMethods);
        }

        return parent::execute($input, $output);
    }

    private function hasChosenCoverageMethod(InputInterface $input): bool
    {
        foreach (self::COVERAGE_METHODS as $coverageMethod) {
            if ($input->getOption($coverageMethod)) {
                return true;
            }
        }

        return false;
    }
}
