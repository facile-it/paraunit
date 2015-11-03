#facile-it/paraunit

[![Stable release][Last stable image]][Packagist link]
[![Unstable release][Last unstable image]][Packagist link]
[![Build status][Master build image]][Master build link]
[![Coverage Status][Master coverage image]][Master coverage link]
[![Code Climate][Master climate image]][Master climate link]

Paraunit is a tool for faster executions of PHPUnit test suites. It makes this possible by launching multiple test in parallel with single PHPUnit processes.

Paraunit is developed using Symfony2 components.

## Requirements
Paraunit reads PHPUnit's .xml configuration file, so it's needed to know which test to load.

If you are testing a Symfony2+Doctrine application, it's suggested to use also [facile-it/paraunit-testcase](https://github.com/facile-it/paraunit-testcase), to avoid database concurrency problems during functional testing;
also, if your want to run functional tests, remember to **warm up the cache before**. in order to avoid a mass cache miss with concurrency problems, and subsequent random failures. 

## Installation
To use this package, use composer:

 * from CLI: `composer require facile-it/paraunit`
 * or, directly in your `composer.json`:

``` 
{
    "require": {
        "facile-it/paraunit": "dev-master"
    }
}
```

## Usage
Paraunit starts as a Symfony2 console command, but it's provided through a bin launcher; you can run it like this: (assuming your composer's bin dir is `vendor/bin`)

`vendor/bin/paraunit run`

This command will launch all the tests in all your configured testsuites.

###Optional parameters
If your `phpunit.xml.dist` file is not in the default base dir, you can specify it by:

`--configuration=relPath/to/phpunit.xml.dist`

You can run a single test suite using:

`--testsuite=testSuiteName`

If you have problem running the tests, or the execution stops before the results are printed out, you can launch paraunit in debug mode, with:

`--debug`

It will show a verbose output with the full running test queue.

###Environment Variables
Paraunit supports environment variables to setup it's functionalities between different environments like local development machine and remote CI tools.
With this method you can also avoid writing very long commands on your console or your CI script.
If yuo can't access env variables on your systems, all you need is an ".env" file placed at your project root, that contains some configuration parameters.

The ".env" file is usefull when you can't modify environment variables, but if you can please use them instead of the file.

#####Supported Paramenters
```
PARAUNIT_PHPUNIT_XML_PATH -> sets the phpunit configuration file path
PARAUNIT_MAX_PROCESS_NUMBER -> sets the number of processes that parauni will use to run your tests
```

## Parsing results

Paraunit prints a parsed result from the single PHPUnit processes. This parsing is a delicate process, and it could sometimes fail; if you are expiriencing any problems, try the `--debug` option, identify the problematic test, and try running it alone; if it's ok and the parser is at fault, please open an issue here on GitHub with the full single test output.


Anyhow, Paraunit doesn't rely on the parser's results to provide the final exit code; instead, it looks only to the processes' exit codes: **it will return a clean zero exit code only if all the PHPUnit processes gave it a zero exit code**. So you can safely use it in your CI build ;)

[Last stable image]: https://poser.pugx.org/facile-it/paraunit/version.svg
[Last unstable image]: https://poser.pugx.org/facile-it/paraunit/v/unstable.svg
[Master build image]: https://travis-ci.org/facile-it/paraunit.svg
[Master climate image]: https://codeclimate.com/github/facile-it/paraunit/badges/gpa.svg
[Master coverage image]: https://coveralls.io/repos/facile-it/paraunit/badge.svg?branch=master&service=github

[Packagist link]: https://packagist.org/packages/facile-it/paraunit
[Master build link]: https://travis-ci.org/facile-it/paraunit
[Master climate link]: https://codeclimate.com/github/facile-it/paraunit
[Master coverage link]: https://coveralls.io/github/facile-it/paraunit?branch=master
