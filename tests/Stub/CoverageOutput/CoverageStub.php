<?php
$coverage = new PHP_CodeCoverage;
$coverage->setData(array (
  '/home/paraunit/projects/src/Paraunit/Process/RetryAwareInterface.php' => 
  array (
  ),
  '/home/paraunit/projects/src/Paraunit/Process/AbstractParaunitProcess.php' => 
  array (
    37 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    39 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    40 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    41 => 
    array (
    ),
    42 => 
    array (
    ),
    44 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    45 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    46 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    53 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    54 => NULL,
    61 => 
    array (
    ),
    62 => NULL,
    68 => 
    array (
    ),
    69 => 
    array (
    ),
    73 => 
    array (
    ),
    74 => 
    array (
    ),
    75 => 
    array (
    ),
    82 => 
    array (
    ),
    83 => NULL,
    90 => 
    array (
    ),
    91 => 
    array (
    ),
    98 => 
    array (
    ),
    99 => NULL,
    106 => 
    array (
    ),
    107 => NULL,
    114 => 
    array (
    ),
    115 => 
    array (
    ),
    116 => 
    array (
    ),
    123 => 
    array (
    ),
    124 => NULL,
    131 => 
    array (
    ),
    132 => NULL,
    139 => 
    array (
    ),
    140 => 
    array (
    ),
  ),
  '/home/paraunit/projects/src/Paraunit/File/TempDirectory.php' => 
  array (
    24 => 
    array (
    ),
    25 => 
    array (
    ),
    32 => 
    array (
    ),
    33 => 
    array (
    ),
    34 => 
    array (
    ),
    35 => 
    array (
    ),
    37 => 
    array (
    ),
    38 => NULL,
    45 => 
    array (
    ),
    46 => 
    array (
    ),
    47 => 
    array (
    ),
    48 => 
    array (
    ),
    50 => 
    array (
    ),
    51 => NULL,
    52 => 
    array (
    ),
    54 => 
    array (
    ),
    55 => NULL,
    62 => 
    array (
    ),
    63 => 
    array (
    ),
    64 => 
    array (
    ),
    65 => 
    array (
    ),
  ),
  '/home/paraunit/projects/src/Paraunit/Process/ParaunitProcessInterface.php' => 
  array (
  ),
  '/home/paraunit/projects/src/Paraunit/Coverage/CoverageFetcher.php' => 
  array (
    23 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    24 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    32 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    36 => 
    array (
      0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
    ),
    37 => NULL,
  ),
  '/home/paraunit/projects/src/Paraunit/Configuration/TempFilenameFactory.php' => 
  array (
    23 => 
    array (
    ),
    24 => 
    array (
    ),
    32 => 
    array (
    ),
    34 => 
    array (
    ),
    35 => 
    array (
    ),
    36 => NULL,
    44 => 
    array (
    ),
    45 => NULL,
  ),
  '/home/paraunit/projects/src/Paraunit/Process/OutputAwareInterface.php' => 
  array (
  ),
  '/home/paraunit/projects/src/Paraunit/Process/ProcessWithResultsInterface.php' => 
  array (
  ),
  '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/TestResultContainerInterface.php' => 
  array (
  ),
));
$coverage->setTests(array (
  'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch' => 
  array (
    'size' => 'unknown',
    'status' => 0,
  ),
));

$filter = $coverage->filter();
$filter->setWhitelistedFiles(array (
  '/home/paraunit/projects/src/Paraunit/Command/CoverageCommand.php' => true,
  '/home/paraunit/projects/src/Paraunit/Command/ParallelCommand.php' => true,
  '/home/paraunit/projects/src/Paraunit/Configuration/PHPDbgBinFile.php' => true,
  '/home/paraunit/projects/src/Paraunit/Configuration/PHPUnitBinFile.php' => true,
  '/home/paraunit/projects/src/Paraunit/Configuration/PHPUnitConfigFile.php' => true,
  '/home/paraunit/projects/src/Paraunit/Configuration/Paraunit.php' => true,
  '/home/paraunit/projects/src/Paraunit/Configuration/TempFilenameFactory.php' => true,
  '/home/paraunit/projects/src/Paraunit/Coverage/CoverageFetcher.php' => true,
  '/home/paraunit/projects/src/Paraunit/Coverage/CoverageMerger.php' => true,
  '/home/paraunit/projects/src/Paraunit/File/Cleaner.php' => true,
  '/home/paraunit/projects/src/Paraunit/File/TempDirectory.php' => true,
  '/home/paraunit/projects/src/Paraunit/Filter/Filter.php' => true,
  '/home/paraunit/projects/src/Paraunit/Filter/FilterInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/Lifecycle/AbstractEvent.php' => true,
  '/home/paraunit/projects/src/Paraunit/Lifecycle/EngineEvent.php' => true,
  '/home/paraunit/projects/src/Paraunit/Lifecycle/ProcessEvent.php' => true,
  '/home/paraunit/projects/src/Paraunit/Parser/AbnormalTerminatedParser.php' => true,
  '/home/paraunit/projects/src/Paraunit/Parser/AbstractParser.php' => true,
  '/home/paraunit/projects/src/Paraunit/Parser/JSONLogFetcher.php' => true,
  '/home/paraunit/projects/src/Paraunit/Parser/JSONLogParser.php' => true,
  '/home/paraunit/projects/src/Paraunit/Parser/JSONParserChainElementInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/Parser/RetryParser.php' => true,
  '/home/paraunit/projects/src/Paraunit/Parser/TestStartParser.php' => true,
  '/home/paraunit/projects/src/Paraunit/Parser/UnknownResultParser.php' => true,
  '/home/paraunit/projects/src/Paraunit/Printer/ConsoleFormatter.php' => true,
  '/home/paraunit/projects/src/Paraunit/Printer/DebugPrinter.php' => true,
  '/home/paraunit/projects/src/Paraunit/Printer/FinalPrinter.php' => true,
  '/home/paraunit/projects/src/Paraunit/Printer/ProcessPrinter.php' => true,
  '/home/paraunit/projects/src/Paraunit/Printer/SharkPrinter.php' => true,
  '/home/paraunit/projects/src/Paraunit/Printer/SingleResultFormatter.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/AbstractParaunitProcess.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/CliCommandInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/OutputAwareInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/ParaunitProcessInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/ProcessFactory.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/ProcessWithResultsInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/RetryAwareInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/SymfonyProcessWrapper.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/TestCliCommand.php' => true,
  '/home/paraunit/projects/src/Paraunit/Process/TestWithCoverageCliCommand.php' => true,
  '/home/paraunit/projects/src/Paraunit/Proxy/PHPUnitUtilXMLProxy.php' => true,
  '/home/paraunit/projects/src/Paraunit/Runner/Runner.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/FullTestResult.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/FailureMessageInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/FunctionNameInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/PrintableTestResultInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/StackTraceInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/TestResultContainerBearerInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/TestResultContainerInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/TestResultInterface.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/MuteTestResult.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/NullTestResult.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/TestResultContainer.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/TestResultFactory.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/TestResultFormat.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/TestResultWithAbnormalTermination.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/TestResultWithMessage.php' => true,
  '/home/paraunit/projects/src/Paraunit/TestResult/TraceStep.php' => true,
));

return $coverage;