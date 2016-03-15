<?php

namespace Unit\Printer;

use Paraunit\Output\OutputContainer;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class OutputContainerTest
 * @package Unit\Printer
 */
class OutputContainerTest extends BaseUnitTestCase
{
    /**
     * @dataProvider messagesProvider
     */
    public function testAddToOutputBuffer($message)
    {
        $container = new OutputContainer('', '', '');

        $container->addToOutputBuffer(new StubbedParaunitProcess(), $message);

        $this->assertEquals(1, $container->countMessages());
        $this->assertEquals(1, $container->countFiles());
    }

    public function messagesProvider()
    {
        return array(
            array('a'),
            array('Skipped Test: a'),
            array('Incomplete Test: a'),
        );
    }

    /**
     * @dataProvider emptyMessagesProvider
     */
    public function testAddToOutputBufferWithPreexistentMessages($emptyMessage)
    {
        $container = new OutputContainer('', '', '');
        $process = new StubbedParaunitProcess();

        $container->addToOutputBuffer($process, 'Not empty message');
        $container->addToOutputBuffer($process, $emptyMessage);
        $container->addToOutputBuffer($process, 'Not empty message');
        $container->addToOutputBuffer($process, $emptyMessage);

        $this->assertEquals(2, $container->countMessages());
        $this->assertEquals(1, $container->countFiles());
    }

    /**
     * @dataProvider emptyMessagesProvider
     */
    public function testAddToOutputBufferShouldIgnoreEmptyMessages($emptyMessage)
    {
        $container = new OutputContainer('', '', '');

        $container->addToOutputBuffer(new StubbedParaunitProcess(), $emptyMessage);

        $this->assertEquals(0, $container->countMessages());
        $this->assertEquals(1, $container->countFiles());
    }

    public function emptyMessagesProvider()
    {
        return array(
            array(null),
            array(''),
            array('Skipped Test: '),
            array('Incomplete Test: '),
        );
    }
}
