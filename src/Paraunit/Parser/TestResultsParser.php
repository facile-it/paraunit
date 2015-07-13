<?php

namespace Paraunit\Parser;


use Paraunit\Process\ProcessResultInterface;

class TestResultsParser implements ProcessOutputParserChainElementInterface
{
    const PHPUNIT_TEST_RESULTS_REGEX = '/(?<=dist\n\n)(.*?)+(?=\s+Time)/s';
    const PHPUNIT_TEST_SINGLE_RESULTS_REGEX = '/[FIES.]+/';

    /**
     * @param ProcessResultInterface $process
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        $results = [];
        preg_match(self::PHPUNIT_TEST_RESULTS_REGEX, $process->getOutput(), $results);

        if (!empty($results)) {
            $cleanResults = preg_replace('/[^FIES.]+/', '', $results[0]);
            $process->setTestResults(str_split($cleanResults));
        }

        return true;
    }
}
