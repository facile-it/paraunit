# facile-it/paraunit

![PHP Version](https://img.shields.io/badge/php-%5E7.0%7C%5E8.1-blue)
[![Stable release][Last stable image]][Packagist link]
[![Unstable release][Last unstable image]][Packagist link]

[![Build status](https://img.shields.io/github/actions/workflow/status/facile-it/paraunit/tests.yaml?branch=2.x)](https://github.com/facile-it/paraunit/actions)
[![Coverage Status][Master coverage image]][Master coverage link]

Paraunit is a tool for faster executions of PHPUnit test suites. It makes this possible by launching multiple test in parallel with single PHPUnit processes.

Paraunit is developed using Symfony components.

## Installation
To use this package, use Composer:

 * from CLI: `composer require --dev facile-it/paraunit`
 * or, directly in your `composer.json`:

```json
{
    "require-dev": {
        "facile-it/paraunit": "^2.0"
    }
}
```

### Compatibility
You can use Paraunit with many versions of PHPUnit or Symfony, following this compatibility list:

| Paraunit version | Compatible PHPUnit Version | Compatible Symfony Version |
|------------------|----------------------------|----------------------------|
| 2.0              | 10+                        | 4.4, 5, 6, 7               |
| 1.3              | 9.3+                       | 4.4, 5, 6                  |
| 1.2              | 9.3+                       | 3.4, 4, 5                  |
| 1.1              | 9.1, 9.2                   | 3.4, 4, 5                  |
| 1.0.*            | 7, 8                       | 3.4, 4, 5                  |
| 0.12.*           | 6, 7                       | 2.8, 3, 4                  |

## Usage
Paraunit starts as a Symfony console command, but it's provided through a bin launcher; you can run it like this:<br/>
(assuming your Composer's bin dir is `vendor/bin`)
```
vendor/bin/paraunit run
```
This command will launch all the tests in all your configured testsuites.

### Collect test coverage
Paraunit is also able to **collect the test coverage in parallel**, like this:
```
vendor/bin/paraunit coverage --html=./dir
```

It **automatically uses the best coverage driver available**: it tries to use [Pcov](https://github.com/krakjoe/pcov) if available (since it's the fastest), otherwise it uses [Xdebug](https://xdebug.org/). If neither are available, it should always be able to use [PHPDbg](https://www.php.net/manual/en/book.phpdbg.php), which is bundled in PHP core, so it should be always present. It can produce coverage in the same formats that PHPUnit provides: HTML, Clover, Cobertura, XML, Crap4j, PHP, text file and text to console.

## Documentation
For more details about Paraunit and its usage, see the [documentation](https://engineering.facile.it/paraunit/documentation/)

[Last stable image]: https://poser.pugx.org/facile-it/paraunit/version.svg
[Last unstable image]: https://poser.pugx.org/facile-it/paraunit/v/unstable.svg
[Master coverage image]: https://codecov.io/gh/facile-it/paraunit/branch/master/graph/badge.svg

[Packagist link]: https://packagist.org/packages/facile-it/paraunit
[Master coverage link]: https://codecov.io/gh/facile-it/paraunit
