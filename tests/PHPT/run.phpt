--TEST--
Execute the run command
--FILE--
<?php

require_once __DIR__ . '/../../src/Bin/paraunit';
--ARGS--
run FakeDriverTest --no-ansi
--EXPECTF--
PARAUNIT v%s
by Francesco Panina, Alessandro Lai & Shark Dev Team @ Facile.it
... %w 3


Execution time -- 00:00:%d

Executed: 1 test classes, 3 tests
