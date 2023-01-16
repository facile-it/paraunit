<?php

declare(strict_types=1);

namespace Paraunit\Command;

use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Coverage\Processor\Clover;
use Paraunit\Coverage\Processor\Crap4j;
use Paraunit\Coverage\Processor\Html;
use Paraunit\Coverage\Processor\Php;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Coverage\Processor\TextSummary;
use Paraunit\Coverage\Processor\Xml;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageCommand extends ParallelCommand
{
    /** @var string[] */
    private readonly array $coverageMethods;

    public function __construct(CoverageConfiguration $configuration)
    {
        parent::__construct($configuration);
        $this->coverageMethods = [
            Clover::getConsoleOptionName(),
            Html::getConsoleOptionName(),
            Xml::getConsoleOptionName(),
            Text::getConsoleOptionName(),
            TextSummary::getConsoleOptionName(),
            Crap4j::getConsoleOptionName(),
            Php::getConsoleOptionName(),
        ];
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('coverage');
        $this->setDescription('Fetch the coverage of your tests in parallel');
        $this->addOption(Clover::getConsoleOptionName(), null, InputOption::VALUE_REQUIRED, 'Output file for Clover XML coverage result');
        $this->addOption(Xml::getConsoleOptionName(), null, InputOption::VALUE_REQUIRED, 'Output dir for PHPUnit XML coverage result');
        $this->addOption(Html::getConsoleOptionName(), null, InputOption::VALUE_REQUIRED, 'Output dir for HTML coverage result');
        $this->addOption(Text::getConsoleOptionName(), null, InputOption::VALUE_OPTIONAL, 'Output coverage as text into file, by default into console', false);
        $this->addOption(TextSummary::getConsoleOptionName(), null, InputOption::VALUE_OPTIONAL, 'Output text coverage summary only', false);
        $this->addOption(Crap4j::getConsoleOptionName(), null, InputOption::VALUE_REQUIRED, 'Output file for Crap4j coverage result');
        $this->addOption(Php::getConsoleOptionName(), null, InputOption::VALUE_REQUIRED, 'Output file for PHP coverage result');
    }

    /**
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        if (! $this->hasChosenCoverageMethod($input)) {
            $coverageMethods = implode(', ', $this->coverageMethods);

            throw new \InvalidArgumentException('You should choose at least one method of coverage output between ' . $coverageMethods);
        }

        return parent::execute($input, $output);
    }

    private function hasChosenCoverageMethod(InputInterface $input): bool
    {
        foreach ($this->coverageMethods as $coverageMethod) {
            if ($input->hasParameterOption('--' . $coverageMethod)) {
                return true;
            }
        }

        return false;
    }
}
