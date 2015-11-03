<?php

$configuration = [

    /**
     * PhpUnit xml configuration file path
     */
    'PARAUNIT_PHPUNIT_XML_PATH' => getenv('PARAUNIT_PHPUNIT_XML_PATH'),

    /**
     * Max number of processes that paraunit will swarm
     */
    'PARAUNIT_MAX_PROCESS_NUMBER' => (int)getenv('PARAUNIT_MAX_PROCESS_NUMBER') ?: 10,

];