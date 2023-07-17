--TEST--
Execute the run command with the --pass-through option
--FILE--
<?php

require_once __DIR__ . '/../../src/Bin/paraunit';
--ARGS--
run IntentionalWarningTestStub -c tests/Stub/phpunit_for_stubs.xml --pass-through=--stop-on-warning
--EXPECTF--
PARAUNIT v%s (PHPUnit v%s)
by Francesco Panina, Alessandro Lai & Shark Dev Team @ Facile.it
W %w 1


Execution time -- 00:00:%d

Executed: 1 test classes, 1 tests

Warnings output:

1) Tests\Stub\IntentionalWarningTestStub::testWithIntentionalWarning
This is an intentional warning

1 files with WARNINGS:
 Tests\Stub\IntentionalWarningTestStub::testWithIntentionalWarning
