<?php

namespace spec\Paraunit\Runner;

use Paraunit\Runner\RetryManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**+
 * Class RetryManagerSpec
 * @package spec\src\Runner
 */
class RetryManagerSpec extends ObjectBehavior
{

    const CLASS_NAME = '\Paraunit\Runner\RetryManager';

    function it_is_initializable()
    {
        $this->shouldHaveType(self::CLASS_NAME);
    }

    /**
     * @param \Paraunit\Process\SymfonyProcessWrapper $process
     */
    public function it_should_not_retry_if_calls_exceed_max_retry_count($process){

        $maxRetryCount = 3;

        $this->beConstructedWith($maxRetryCount);

        $process->getOutput()->willReturn(RetryManager::MYSQL_LOCK_EXCEPTION);
        $process->getRetryCount()->willReturn($maxRetryCount + 1);

        $this->setRetryStatus($process)->shouldReturn(false);

    }

    /**
     * @param \Paraunit\Process\SymfonyProcessWrapper $process
     */
    public function it_should_retry_if_calls_are_lower_or_equal_to_max_retry_count($process){

        $maxRetryCount = 3;

        $this->beConstructedWith($maxRetryCount);

        $process->getOutput()->willReturn(RetryManager::MYSQL_LOCK_EXCEPTION);

        $process->getRetryCount()->willReturn($maxRetryCount);
        $process->markAsToBeRetried()->shouldBeCalled();
        $process->increaseRetryCount()->shouldBeCalled();
        $process->isToBeRetried()->willReturn(true)->shouldBeCalled();

        $this->setRetryStatus($process)->shouldReturn(true);

    }
}

