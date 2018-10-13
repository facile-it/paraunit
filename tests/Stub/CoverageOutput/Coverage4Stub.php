<?php

declare(strict_types=1);
$coverage = new SebastianBergmann\CodeCoverage\CodeCoverage();
$coverage->setData([
    '/home/paraunit/projects/src/Paraunit/Process/RetryAwareInterface.php' => [
    ],
    '/home/paraunit/projects/src/Paraunit/Process/AbstractParaunitProcess.php' => [
        37 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        39 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        40 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        41 => [
        ],
        42 => [
        ],
        44 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        45 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        46 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        53 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        54 => null,
        61 => [
        ],
        62 => null,
        68 => [
        ],
        69 => [
        ],
        73 => [
        ],
        74 => [
        ],
        75 => [
        ],
        82 => [
        ],
        83 => null,
        90 => [
        ],
        91 => [
        ],
        98 => [
        ],
        99 => null,
        106 => [
        ],
        107 => null,
        114 => [
        ],
        115 => [
        ],
        116 => [
        ],
        123 => [
        ],
        124 => null,
        131 => [
        ],
        132 => null,
        139 => [
        ],
        140 => [
        ],
    ],
    '/home/paraunit/projects/src/Paraunit/File/TempDirectory.php' => [
        24 => [
        ],
        25 => [
        ],
        32 => [
        ],
        33 => [
        ],
        34 => [
        ],
        35 => [
        ],
        37 => [
        ],
        38 => null,
        45 => [
        ],
        46 => [
        ],
        47 => [
        ],
        48 => [
        ],
        50 => [
        ],
        51 => null,
        52 => [
        ],
        54 => [
        ],
        55 => null,
        62 => [
        ],
        63 => [
        ],
        64 => [
        ],
        65 => [
        ],
    ],
    '/home/paraunit/projects/src/Paraunit/Process/ParaunitProcessInterface.php' => [
    ],
    '/home/paraunit/projects/src/Paraunit/Coverage/CoverageFetcher.php' => [
        23 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        24 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        32 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        36 => [
            0 => 'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch',
        ],
        37 => null,
    ],
    '/home/paraunit/projects/src/Paraunit/Configuration/TempFilenameFactory.php' => [
        23 => [
        ],
        24 => [
        ],
        32 => [
        ],
        34 => [
        ],
        35 => [
        ],
        36 => null,
        44 => [
        ],
        45 => null,
    ],
    '/home/paraunit/projects/src/Paraunit/Process/OutputAwareInterface.php' => [
    ],
    '/home/paraunit/projects/src/Paraunit/Process/ProcessWithResultsInterface.php' => [
    ],
    '/home/paraunit/projects/src/Paraunit/TestResult/Interfaces/TestResultContainerInterface.php' => [
    ],
]);
$coverage->setTests([
    'Tests\\Unit\\Coverage\\CoverageFetcherTest::testFetch' => [
        'size' => 'unknown',
        'status' => 0,
    ],
]);

$filter = $coverage->filter();
$filter->setWhitelistedFiles([
    '/home/paraunit/projects/src/Paraunit/Command/CoverageCommand.php' => true,
    '/home/paraunit/projects/src/Paraunit/Command/ParallelCommand.php' => true,
    '/home/paraunit/projects/src/Paraunit/Configuration/PHPDbgBinFile.php' => true,
    '/home/paraunit/projects/src/Paraunit/Configuration/PHPUnitBinFile.php' => true,
    '/home/paraunit/projects/src/Paraunit/Configuration/PHPUnitConfig.php' => true,
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
]);

return $coverage;
