<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;
use Paraunit\Process\RetryAwareInterface;

/**
 * Class RetryParser
 * @package Paraunit\Parser
 */
class RetryParser implements ProcessOutputParserChainElementInterface
{
    /** @var  int */
    private $maxRetryCount;

    /** @var  string */
    private $regexPattern;

    /**
     * RetryParser constructor.
     * @param int $maxRetryCount
     */
    public function __construct($maxRetryCount = 3)
    {
        $this->maxRetryCount = $maxRetryCount;

        $patterns = array(
            'The EntityManager is closed',
            // MySQL
            'Deadlock found',
            'Lock wait timeout exceeded',
            // SQLite
            'General error: 5 database is locked',
        );

        $this->regexPattern = $this->buildRegexPattern($patterns);
    }

    /**
     * @param ProcessResultInterface $process
     *
     * @return bool True if chain should continue
     */
    public function parseAndContinue(ProcessResultInterface $process)
    {
        if ($process instanceof RetryAwareInterface && $this->isToBeRetried($process)) {
            $process->markAsToBeRetried();

            return false;
        }

        return true;
    }

    /**
     * @param RetryAwareInterface $process
     * @return bool
     */
    private function isToBeRetried(RetryAwareInterface $process)
    {
        return $process->getRetryCount() < $this->maxRetryCount && preg_match($this->regexPattern, $process->getOutput());
    }

    /**
     * @param array | string[] $patterns
     * @return string
     */
    private function buildRegexPattern(array $patterns)
    {
        $regex = implode('|', $patterns);

        return '/' . $regex . '/';
    }
}
