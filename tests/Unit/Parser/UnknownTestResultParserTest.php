<?php

namespace Tests\Unit\Parser;


use Paraunit\Parser\UnknownTestResultParser;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;

class UnknownTestResultParserTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider statusesProvider
     */
    public function testHandleLogItemShouldCatchAnything($statuses)
    {
        $log = new \stdClass();
        $log->status = $statuses;
        $log->message = 'message';
        $parser = new UnknownTestResultParser();

        $parser->handleLogItem(new StubbedParaunitProcess(), $log);
    }

    public function statusesProvider()
    {
        return array(
            array('pass'),
            array('error'),
            array('fail'),
            array('pass'),
            array('testStart'),
            array('suiteStart'),
            array('qwerty'),
            array('trollingYou'),
        );
    }
}
