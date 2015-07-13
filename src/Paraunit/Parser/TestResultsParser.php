<?php

namespace Paraunit\Parser;


use Paraunit\Process\ProcessResultInterface;

class TestResultsParser implements ProcessOutputParserChainElementInterface
{
    const PHPUNIT_TEST_RESULTS_REGEX = '/(?<=by Sebastian Bergmann and contributors.\n\n)(.*?)+(?=\n\nTime:)/s';
    const PHPUNIT_TEST_SINGLE_RESULTS_REGEX = '/[FIES.]+/';
    const PHPUNIT_CONFIG_LOAD = 'Configuration read from ';

    /**
     * @param ProcessResultInterface $process
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        $results = $this->getResultsFromOutput($process->getOutput());

        if ($results != '') {
            $cleanResults = preg_replace('/[^FIES.]+/', '', $results);
            $process->setTestResults(str_split($cleanResults));
        }

        return true;
    }

    /**
     * @param string $output
     * @return string
     */
    private function getResultsFromOutput($output)
    {
        $matches = [];
        preg_match(self::PHPUNIT_TEST_RESULTS_REGEX, $output, $matches);

        $results = '';

        if (!empty($matches)) {
            // fix per PHPUNIT < 4.7, strip della riga della config
            if (substr($matches[0], 0, 24) == $this::PHPUNIT_CONFIG_LOAD) {
                $results = preg_replace('/^'.preg_quote($this::PHPUNIT_CONFIG_LOAD).'.*\n\n/', '', $matches[0]);
            } else {
                $results = $matches[0];
            }
        }

        return $results;
    }
}
