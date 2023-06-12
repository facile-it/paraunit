<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\TestResultList;
use Paraunit\TestResult\TestResultWithSymbolFormat;

class SingleResultFormatter
{
    /** @var array<string, string> */
    private $tagMap;

    /** @var bool */
    private $printFailedEarly;

    public function __construct(
        TestResultList $testResultList,
        bool $printFailedEarly
    )
    {
        $this->printFailedEarly = $printFailedEarly;
        $this->tagMap = [];

        foreach ($testResultList->getTestResultContainers() as $parser) {
            $format = $parser->getTestResultFormat();
            if ($format instanceof TestResultWithSymbolFormat) {
                $this->addToMap($format);
            }
        }
    }

    public function formatSingleResult(PrintableTestResultInterface $singleResult): string
    {
        $format = $singleResult->getTestResultFormat();

        if (! $format instanceof TestResultWithSymbolFormat) {
            return '';
        }

        $resultSymbol = $format->getTestResultSymbol();
        $tag = $this->tagMap[$resultSymbol];

        return sprintf('<%s>%s</%s>', $tag, $resultSymbol, $tag);
    }

    private function addToMap(TestResultWithSymbolFormat $format): void
    {
        $this->tagMap[$format->getTestResultSymbol()] = $format->getTag();
    }

    public function shouldPrintFailedEarly(): bool
    {
        return $this->printFailedEarly;
    }
}
