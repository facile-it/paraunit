--TEST--
Execute the coverage command
--FILE--
<?php

require_once __DIR__ . '/../../src/Bin/paraunit';
--ARGS--
coverage FakeDriverTest --text --no-ansi
--EXPECTF--
PARAUNIT v%s (PHPUnit v%s)
by Francesco Panina, Alessandro Lai & Shark Dev Team @ Facile.it
Coverage driver in use: %s
... %w 3


Execution time -- 00:00:%d

Executed: 1 test classes, 3 tests


Code Coverage Report:      
  %d-%d-%d %d:%d:%d      
                           
 Summary:                  
  Classes:  %s   
  Methods:  %s 
  Lines:    %d.%d% %s

Paraunit\%a
