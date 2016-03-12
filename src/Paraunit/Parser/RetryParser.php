<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessResultInterface;
use Paraunit\Process\RetryAwareInterface;

/**
 * Class RetryParser
 * @package Paraunit\Parser
 */
class RetryParser implements JSONParserChainElementInterface
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
     * {@inheritdoc}
     */
    public function parsingFoundResult(ProcessResultInterface $process, \stdClass $log)
    {
        if ($process instanceof RetryAwareInterface
            && $this->isToBeRetried($log)
            && $this->hasNotExceededRetryCount($process)
        ) {
            $process->markAsToBeRetried();

            return true;
        }

        return false;
    }

    /**
     * @param \stdClass $log
     * @return bool
     */
    private function isToBeRetried(\stdClass $log)
    {
        return $log->status == 'error' && preg_match($this->regexPattern, $log->message);
    }


    private function hasNotExceededRetryCount(RetryAwareInterface $process)
    {
        return $process->getRetryCount() < $this->maxRetryCount;
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
