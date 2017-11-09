<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Proxy\Coverage\CodeCoverage;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TextToConsole
 * @package Paraunit\Proxy\Coverage
 */
class TextToConsole extends AbstractText
{
    /** @var OutputInterface */
    private $output;

    /**
     * TextToConsole constructor.
     * @param OutputInterface $output
     * @param bool $showColors
     */
    public function __construct(OutputInterface $output, bool $showColors)
    {
        parent::__construct($showColors);
        $this->output = $output;
    }

    public function process(CodeCoverage $codeCoverage)
    {
        $this->output->writeln($this->getTextCoverage($codeCoverage));
    }
}
