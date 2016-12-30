+++
category = ["documentation"]
title = "Documentation"
toc = true
+++

Paraunit is a tool for faster executions of PHPUnit test suites. 
It makes this possible by launching multiple test in parallel with single PHPUnit processes.

Paraunit is developed using [Symfony components](http://symfony.com/components).

# Requirements
Paraunit is used in conjunction with PHPUnit. It reads PHPUnit's .xml configuration file, so it's needed to know which test to load.

If you are testing a Symfony+Doctrine application, it's suggested to use also [facile-it/paraunit-testcase](https://github.com/facile-it/paraunit-testcase), to avoid database concurrency problems during functional testing;
also, if your want to run functional tests, remember to **warm up the cache before**, in order to avoid a mass cache miss (and relative [cache stampede](https://en.wikipedia.org/wiki/Cache_stampede)) with concurrency problems, and subsequent random failures. 

## Installation
### From Composer
To use this package, use Composer:

 * from CLI: `composer require --dev facile-it/paraunit`
 * or, directly in your `composer.json`:

``` 
{
    "require-dev": {
        "facile-it/paraunit": "~0.7"
    }
}
```

### PHAR
If you prefer you can directly download the latest version in **PHAR format**, from the [lastest GitHub release page](https://github.com/facile-it/paraunit/releases/latest), starting from 0.7.3. In this case, you need to replace `vendor/bin/paraunit` with `./paraunit.phar` in all the following examples.

#### Verify the GPG signature
All the Paraunit PHAR releases are signed with GPG. To verify the signature:
 * Download the PHAR
 * Download the associated GPG signature (the `.asc` file)
 * Use the GPG tool to verify
```
gpg --verify paraunit-x.y.phar.asc paraunit.phar
```

# Usage
## `run` command
The `run` command is the main functionality of Paraunit; it launches all the tests in all your configured testsuites in parallel; you can run it like this: (assuming your composer's bin dir is `vendor/bin`)

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
`--text=filename.txt` | Coverage in text format, with the specified filename
`--text-to-console` | Coverage in text format, printed directly in the console, at the end of the process

Example:

```bash
vendor/bin/paraunit coverage --html=./coverage
```

Paraunit detects automatically if the [PHPDBG](http://phpdbg.com/) binary is available, at it uses that as a preferred coverage driver, since it's a lot faster and uses less memory. If it's not available, it falls back to use xDebug. If you want to use PHPDBG, you are **highly encouraged to disable xDebug**, since Paraunit can't do it on its own, and using PHPDBG with xDebug enabled can lead to an excessive memory consumption, up to the point of crashing your machine.

## Optional parameters

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

You can choose how many concurrent processes you want to spawn at the same time, using the `--parallel` option. The default value is `10`:

```bash
vendor/bin/paraunit run --parallel=5
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
  * `report-useless-tests`
  * `strict-global-state`
  * `disallow-test-output`
  * `enforce-time-limit`
  * `disallow-todo-tests`
  * `process-isolation`
  * `no-globals-backup`
  * `static-backup`
  * `loader`
  * `repeat`
  * `printer`
  * `bootstrap`
  * `no-configuration`
  * `include-path`


### Debug mode

If you have problem running the tests, or the execution stops before the results are printed out, you can launch Paraunit in debug mode, with:

```bash
vendor/bin/paraunit run --debug
```

It will show a verbose output with the full running test queue.

# Parsing results

Paraunit prints a parsed result from the single PHPUnit processes. This parsing is done using PHPUnit's JSON log output, so it's a resilient and reliable process; it allows to be also resistent to fatal errors and other abnormal process termination.

Anyhow, Paraunit doesn't rely on the parsed results to provide the final exit code; instead, it looks only to the processes' exit codes:
 **it will return a clean zero exit code only if all the PHPUnit processes gave it a zero exit code**. 
 So you can safely use it in your CI build ;)

Side note: if you are using [Symfony's PHPUnit bridge](http://symfony.com/doc/current/components/phpunit_bridge.html) to spot deprecations (or any other plugin that outputs something) you will notice that the bridge list of deprecations will be lost through Paraunit; the tests will be shown as passing, but **the Paraunit process will still fail as expected if a deprecation is encountered**, due to the aforementioned exit code being considered.

# Troubleshooting

If you are experiencing any problems, you can try the `--debug` option to identify the problematic test, and try running it alone; if failures seems to appear at randoms during Paraunit runs, check for concurrency problem, like database access; otherwise, please open an issue [here on GitHub](https://github.com/facile-it/paraunit/issues).
