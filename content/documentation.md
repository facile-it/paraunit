+++
category = ["documentation"]
title = "Documentation"
toc = true
+++

Paraunit is a tool for faster executions of PHPUnit test suites. 
It makes this possible by launching multiple test in parallel with single PHPUnit processes.

Paraunit is developed using [Symfony components](https://symfony.com/components).

# Requirements
Paraunit is used in conjunction with PHPUnit. It reads PHPUnit's .xml configuration file, so it's needed to know which test to load.

If you are testing a Symfony+Doctrine application, it's suggested to use also [dama/doctrine-test-bundle](https://github.com/dmaicher/doctrine-test-bundle), to avoid database concurrency problems during functional testing;
also, if your want to run functional tests, remember to **warm up the cache before**, in order to avoid a mass cache miss (and relative [cache stampede](https://en.wikipedia.org/wiki/Cache_stampede)) with concurrency problems, and subsequent random failures. 

# Installation
To use this package, use Composer:

 * from CLI: `composer require --dev facile-it/paraunit`
 * or, directly in your `composer.json`:

``` 
{
    "require-dev": {
        "facile-it/paraunit": "^1.1"
    }
}
```

# Usage
## `run` command
The `run` command is the main functionality of Paraunit; it launches all the tests in all your configured test suites in parallel; you can run it like this: (assuming your composer's bin dir is `vendor/bin`)

```bash
vendor/bin/paraunit run
```

This is possible because Paraunit starts as a Symfony console command, and it’s provided through a bin launcher.

## `coverage` command
The `coverage` command is used to generate the test coverage in parallel. It supports all the same options of the `run` command (documented below) but it requires **at least one of those options** to choose the coverage output format:
 
 Option | Description
 -------|------------
`--html=dir` | Coverage in HTML format, inside the specified directory
`--clover=filename.xml` | Coverage in XML-clover format, with the specified filename
`--xml=dir` | Coverage in PHPUnit XML format, inside the specified directory
`--text=filename.txt` | Coverage in text format, into the specified filename
`--text` | Coverage in text format, printed directly in the console, at the end of the process
`--text-summary=filename.txt` | Coverage summary in text format, into the specified filename
`--text-summary` | Coverage in text format, printed directly in the console, at the end of the process

Example:

```bash
vendor/bin/paraunit coverage --html=./coverage
```

Paraunit detects automatically which coverage driver can use to fetch test coverage data; supported drivers are [ext-pcov](https://github.com/krakjoe/pcov) (only since [1.0.0-beta2](https://github.com/facile-it/paraunit/pull/146) and in conjunction with PHPUnit 8), [xDebug](https://xdebug.org/) and [PHPDBG](https://www.php.net/manual/en/book.phpdbg.php). 

Paraunit checks if `ext-pcov` is installed and uses it as the preferred driver, since it's the fastest; the extensions can remain installed but disabled (`pcov.enabled=0`), and Paraunit will take care of enabling it when launching PHPUnit processes.
 
If that's not available, it will try to detect the presence of Xdebug; as a last resource, it will use PHPDbg, which should be always available since it's built into PHP core since 5.6.

If you have issues or random failures when using the `coverage` command, you can try to use the `--parallel 1` option: this executes just one test at a time, but you will still benefit from the process splitting, that will avoid any memory issue.

## The pipelines

Since version 0.9, Paraunit executes the tests using a **pipeline logic**: this means that if we ask to run 10 tests in parallel at the same time, Paraunit will instantiate 10 pipeline to do it, and each pipeline will be numbered, from 1 to 10.

The only perceivable difference to the user is the **environment variable**, called `PARAUNIT_PIPELINE_NUMBER`, which is injected in every test process; this variable contains the number of the pipeline. This number can be easily retrieved in your tests, and it can be used to access without concurrency issues to a diverse copy of a resource, i.e. a database, like in this little example:

```php
<?php

use Paraunit\Configuration\EnvVariables;
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
    protected function setup(): void
    {
        $pipelineNumber = getenv(EnvVariables::PARAUNIT_PIPELINE_NUMBER);
        $this->databaseName = 'db_test_' . $pipelineNumber;
        // ...
    }
}
```

This little piece of code will obtain `db_test_1`, `db_test_2` etc. as a value for the `databaseName` property, achieving actual separation when accessing the test fixtures in the database. The setup and cleanup of the fixtures after each test is still up to the developer, obviously.

As the snippet shows, the name of the environment variables are available as constants in the `Paraunit\Configuration\EnvVariables` class.

## Optional arguments and parameters

### String filter
Like with PHPUnit, you can run a subset of your tests passing a path as the first argument of the command:

```bash
vendor/bin/paraunit run path/to/my/tests
```
In Paraunit this functionality is more powerful, since:

 * it's case insensitive
 * it works in combination with `--testsuite` (PHPUnit ignores that if the argument is provided)
 * it searches a match everywhere in the filename, so it doesn't have to be a full or relative path

Let's use an example to show how powerful this feature is. You are working on the `MyApp\SpecialPanel\SomeClass` class, and you want to run all the tests of the `MyApp\SpecialPanel` namespace. Those tests are in the `tests/Unit/SpecialPanel/` and `tests/Functional/SpecialPanel/` directories. You can run both dir at the same time with

```bash
vendor/bin/paraunit run specialpanel
```

You don't have to bother about the fact that the tests are splitted into different subdirectories, and about the uppercase letters too.

### Configuration
If your `phpunit.xml.dist` file is not in the default base dir, you can specify it by:

```bash
vendor/bin/paraunit run --configuration=relPath/to/phpunit.xml.dist
```

or with the short version:

```bash
vendor/bin/paraunit run -c=relPath/to/phpunit.xml.dist
```

Also it's possible to provide only a directory, in such case Paraunit will look a file with the default name, `phpunit.xml.dist`:

```bash
vendor/bin/paraunit run -c=relPath/to/xml/file/
```

### Parallel

You can choose how many concurrent processes (pipelines) you want to spawn at the same time, using the `--parallel` option. The default value is `10`:

```bash
vendor/bin/paraunit run --parallel=5
```

### Chunk size

**NEW**: introduced in 1.3.0.

You can choose how many test classes should be executed inside a single concurrent process (pipeline). The default value is `1`, so each pipeline runs a single test class; higher values could benefit in terms of total execution time if your tests have a complex and slow class setup.

```bash
vendor/bin/paraunit run --chunk-size=3
```

### Testsuite

You can run a single test suite (as defined in your configuration file) using:

```bash
vendor/bin/paraunit run --testsuite=testSuiteName
```

### PHPUnit inherited options

A large number of PHPUnit options (apart from the aforementioned `--testsuite`) are compatible with Paraunit, and they will be passed along to each single PHPUnit spawned process. For a more complete documentation of those options' behavior, see the [PHPUnit CLI documentation](https://phpunit.de/manual/current/en/textui.html#textui.clioptions).

This is the complete list of supported options:

  * `filter`
  * `group`
  * `exclude-group`
  * `test-suffix`
  * `dont-report-useless-tests`
  * `strict-coverage`
  * `strict-global-state`
  * `disallow-test-output`
  * `disallow-resource-usage`
  * `enforce-time-limit`
  * `disallow-todo-tests`
  * `process-isolation`
  * `globals-backup`
  * `static-backup`
  * `loader`
  * `repeat`
  * `printer`
  * `bootstrap`
  * `no-configuration`
  * `no-coverage`
  * `no-extensions`
  * `include-path`

### Debug mode

If you have problem running the tests, or the execution stops before the results are printed out, you can launch Paraunit in debug mode, with:

```bash
vendor/bin/paraunit run --debug
```

It will show a verbose output with the full running test queue.

# Parsing results

Paraunit prints a parsed result from the single PHPUnit processes. This parsing is done hooking into PHPUnit, so it's a resilient and reliable process; it allows to be also resilient to fatal errors and other abnormal process termination.

Anyhow, Paraunit doesn't rely on the parsed results to provide the final exit code; instead, it looks only to the processes' exit codes:
 **it will return a clean zero exit code only if all the PHPUnit processes gave it a zero exit code**. 
 So you can safely use it in your CI build ;)

Side note: if you are using [Symfony's PHPUnit bridge](https://symfony.com/doc/current/components/phpunit_bridge.html) to spot deprecations (or any other plugin that outputs something), you will be able to detect test failures due to deprecations since version 0.11.

# Troubleshooting
If you are experiencing any problems, you can try the `--debug` option to identify the problematic test, and try running it alone; if failures seems to appear at randoms during Paraunit runs, check for concurrency problem, like database access; otherwise, please open an issue [here on GitHub](https://github.com/facile-it/paraunit/issues).
