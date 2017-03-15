# facile-it/paraunit

[![Stable release][Last stable image]][Packagist link]
[![Unstable release][Last unstable image]][Packagist link]
[![Build status][Master build image]][Master build link]
[![Appveyor build status][Appveyor build image]][Appveyor build link]

[![Coverage Status][Master coverage image]][Master coverage link]
[![Scrutinizer][Master scrutinizer image]][Master scrutinizer link]
[![Code Climate][Master climate image]][Master climate link]
[![SL Insight][SL Insight image]][SL Insight link]

Paraunit is a tool for faster executions of PHPUnit test suites. It makes this possible by launching multiple test in parallel with single PHPUnit processes.

Paraunit is developed using Symfony components.

## Installation
### From Composer
To use this package, use Composer:

 * from CLI: `composer require --dev facile-it/paraunit`
 * or, directly in your `composer.json`:

```json
{
    "require-dev": {
        "facile-it/paraunit": "~0.8"
    }
}
```

### PHAR
If you prefer you can directly download the latest version in **PHAR format**, from the [latest GitHub release page](https://github.com/facile-it/paraunit/releases/latest), starting from 0.7.3. In this case, you need to replace `vendor/bin/paraunit` with `./paraunit.phar`.

#### Verify the GPG signature
All the Paraunit PHAR releases are signed with GPG. To verify the signature:
 * Download the PHAR
 * Download the associated GPG signature (the `.asc` file)
 * Use the GPG tool to verify
```
gpg --verify paraunit-x.y.phar.asc paraunit.phar
```

## Usage
Paraunit starts as a Symfony console command, but it's provided through a bin launcher; you can run it like this:<br/>
(assuming your composer's bin dir is `vendor/bin`)
```
vendor/bin/paraunit run
```
This command will launch all the tests in all your configured testsuites.

Paraunit is also able to **collect the test coverage in parallel**, like this:
```
vendor/bin/paraunit coverage --html=./dir
```

It automatically **uses PHPDBG** if available, and it can produce coverage in the same formats that PHPUnit provides: HTML, Clover, XML, Crap4j, PHP, text file and text to console.

## Documentation
For more details about Paraunit and its usage, see the [documentation](http://facile-it.github.io/paraunit/documentation/)

[Last stable image]: https://poser.pugx.org/facile-it/paraunit/version.svg
[Last unstable image]: https://poser.pugx.org/facile-it/paraunit/v/unstable.svg
[Master build image]: https://travis-ci.org/facile-it/paraunit.svg
[Appveyor build image]: https://ci.appveyor.com/api/projects/status/ohmhq2s762x3ixli/branch/master?svg=true
[Master climate image]: https://codeclimate.com/github/facile-it/paraunit/badges/gpa.svg
[Master scrutinizer image]: https://scrutinizer-ci.com/g/facile-it/paraunit/badges/quality-score.png?b=master
[Master coverage image]: https://coveralls.io/repos/facile-it/paraunit/badge.svg?branch=master&service=github
[SL Insight image]: https://insight.sensiolabs.com/projects/6571b482-6e1d-4e0c-b215-94d757909b20/mini.png

[Packagist link]: https://packagist.org/packages/facile-it/paraunit
[Master build link]: https://travis-ci.org/facile-it/paraunit
[Appveyor build link]: https://ci.appveyor.com/project/Jean85/paraunit/branch/master
[Master climate link]: https://codeclimate.com/github/facile-it/paraunit
[Master scrutinizer link]: https://scrutinizer-ci.com/g/facile-it/paraunit/?branch=master
[Master coverage link]: https://coveralls.io/github/facile-it/paraunit?branch=master
[SL Insight link]: https://insight.sensiolabs.com/projects/6571b482-6e1d-4e0c-b215-94d757909b20
