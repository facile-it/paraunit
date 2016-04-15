+++
category = ["documentation"]
title = "Documentation"
+++

Paraunit is a tool for faster executions of PHPUnit test suites. 
It makes this possible by launching multiple test in parallel with single PHPUnit processes.

Paraunit is developed using [Symfony2 components](http://symfony.com/components).

# Requirements
Paraunit is used in conjunction with PHPUnit. IT reads PHPUnit's .xml configuration file, so it's needed to know which test to load.

If you are testing a Symfony2+Doctrine application, it's suggested to use also [facile-it/paraunit-testcase](https://github.com/facile-it/paraunit-testcase), to avoid database concurrency problems during functional testing;
also, if your want to run functional tests, remember to **warm up the cache before**. in order to avoid a mass cache miss (and relative [cache stampede](https://en.wikipedia.org/wiki/Cache_stampede)) with concurrency problems, and subsequent random failures. 

# Installation
To use this package, use [composer](https://getcomposer.org/):

 * from CLI: `composer require facile-it/paraunit`
 * or, directly in your `composer.json`:

``` 
{
    "require": {
        "facile-it/paraunit": "~0.6"
    }
}
```

# Usage
Paraunit starts as a Symfony2 console command, but it's provided through a bin launcher; you can run it like this: (assuming your composer's bin dir is `vendor/bin`)

`vendor/bin/paraunit run`

This command will launch all the tests in all your configured testsuites.

## Optional parameters

### Configuration
If your `phpunit.xml.dist` file is not in the default base dir, you can specify it by:

`vendor/bin/paraunit run --configuration=relPath/to/phpunit.xml.dist`

or with the short version:

`vendor/bin/paraunit run -c=relPath/to/phpunit.xml.dist`

Also it's possible to provide only a directory, in such case Paraunit will look for the 'phpunit.xml.dist':

`vendor/bin/paraunit run -c=relPath/to/xml/file/`

### Testsuite

You can run a single test suite (as defined in your configuration file) using:

`vendor/bin/paraunit run --testsuite=testSuiteName`

### Debug mode

If you have problem running the tests, or the execution stops before the results are printed out, you can launch Paraunit in debug mode, with:

`--debug`

It will show a verbose output with the full running test queue.

# Parsing results

Paraunit prints a parsed result from the single PHPUnit processes. 
This parsing is done (since version 0.6) using PHPUnit's JSON log output, so it's a resilient and reliable process.
It allows to be also resistent to fatal errors and other abnormal process termination.
If you are experiencing any problems, you can try the `--debug` option to identify the problematic test, and try running it alone;
if it's ok and the parser is at fault, please open an issue here on GitHub with the full single test output.

Anyhow, Paraunit doesn't rely on the parsed results to provide the final exit code; instead, it looks only to the processes' exit codes:
 **it will return a clean zero exit code only if all the PHPUnit processes gave it a zero exit code**. 
 So you can safely use it in your CI build ;)
