#facile-it/paraunit

[![Stable release][Last stable image]][Packagist link]
[![Unstable release][Last unstable image]][Packagist link]
[![Build status][Master build image]][Master build link]
[![Coverage Status][Master coverage image]][Master coverage link]
[![Scrutinizer][Master scrutinizer image]][Master scrutinizer link]
[![Code Climate][Master climate image]][Master climate link]
[![SL Insight][SL Insight image]][SL Insight link]

Paraunit is a tool for faster executions of PHPUnit test suites. It makes this possible by launching multiple test in parallel with single PHPUnit processes.

Paraunit is developed using Symfony2 components.

## Installation
To use this package, use composer:

 * from CLI: `composer require facile-it/paraunit`
 * or, directly in your `composer.json`:

``` 
{
    "require": {
        "facile-it/paraunit": "~0.4"
    }
}
```

## Usage
Paraunit starts as a Symfony2 console command, but it's provided through a bin launcher; you can run it like this: (assuming your composer's bin dir is `vendor/bin`)

`vendor/bin/paraunit run`

This command will launch all the tests in all your configured testsuites.

## Documentation
For more details about Paraunit and its usage, see the [documentation](http://facile-it.github.io/paraunit/documentation/)

[Last stable image]: https://poser.pugx.org/facile-it/paraunit/version.svg
[Last unstable image]: https://poser.pugx.org/facile-it/paraunit/v/unstable.svg
[Master build image]: https://travis-ci.org/facile-it/paraunit.svg
[Master climate image]: https://codeclimate.com/github/facile-it/paraunit/badges/gpa.svg
[Master scrutinizer image]: https://scrutinizer-ci.com/g/facile-it/paraunit/badges/quality-score.png?b=master
[Master coverage image]: https://coveralls.io/repos/facile-it/paraunit/badge.svg?branch=master&service=github
[SL Insight image]: https://insight.sensiolabs.com/projects/6571b482-6e1d-4e0c-b215-94d757909b20/mini.png

[Packagist link]: https://packagist.org/packages/facile-it/paraunit
[Master build link]: https://travis-ci.org/facile-it/paraunit
[Master climate link]: https://codeclimate.com/github/facile-it/paraunit
[Master scrutinizer link]: https://scrutinizer-ci.com/g/facile-it/paraunit/?branch=master
[Master coverage link]: https://coveralls.io/github/facile-it/paraunit?branch=master
[SL Insight link]: https://insight.sensiolabs.com/projects/6571b482-6e1d-4e0c-b215-94d757909b20
