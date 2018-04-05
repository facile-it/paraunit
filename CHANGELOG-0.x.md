# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
 * Added support for PHPUnit 7 and `phpunit/php-code-coverage` 6

### Changed
 * The coverage text output options have changed:
   * The `--text` option now accepts a filename as an argument, and defaults to the console as output (replacing `--text-to-console`) 
   * The new `--text-summary` option behaves in the same way, but it writes only the summary part 

### Removed
 * Removed support for PHP 7.0 (needed to support PHPUnit 7 correctly)
 * Dropped the `--text-to-console` coverage option in favor of the new behavior

## [0.11] - 2017-12-13

### Added
 * Added support for Symfony 4

### Changed
 * Migrated the whole DI configuration from YAML to PHP 
 * Require at least jean85/pretty-package-versions 1.0.3 (see related [#3](https://github.com/Jean85/pretty-package-versions/issues/3))

### Fixed
 * When a test class is retried, previous tests are no longer shown/counted toward executed tests (#109). 

### Removed
 * Removed support for Symfony 2.7
 * Removed dependency from `symfony/yaml` 

## [0.10.1] - 2017-10-19

### Added
 * Add support for deprecation warnings found by `symfony/phpunit-bridge`: failures and tests output are now reported

### Changed
 * Start suggesting `dama/doctrine-test-bundle` instead of `facile-it/paraunit-testcase`, since it has been abandoned.

## [0.10] - 2017-09-06

### Added
 * Add official support to PHP 7.2 (added to Travis CI matrix)
 * Add executed tests counter at the end of each line in output (#91)

### Changed
 * The Shark logo is now optional; to show it at the top, use the `--logo` option
 * The `--debug` output has been completely rewritten and now is more useful than ever! 

### Fixed
 * Fix the `--text-to-console` coverage option (#99)

## [0.9.2] - 2017-07-06

### Fixed
* Add missing dependencies: `symfony/yaml` and `symfony/stopwatch`

## [0.9.1] - 2017-07-06

### Changed
* Use `Jean85\PrettyVersions` to print the version header, and hide the big SHA commit hash when not needed

## [0.9] - 2017-07-05

### Changed

* Implement pipelines: now every single test process has an environment variable called `PARAUNIT_PIPELINE_NUMBER` injected which is a number that identifies the pipeline in which the test is executed; it can be used as a discriminator to access a different set of resources (i.e. a different copy of a test DB). See the [documentation](https://engineering.facile.it/paraunit/documentation/) for more details.
* Support for PHPUnit 6
* Drop support for PHPUnit 4 & 5
* Minimum PHP version required: 7.0
* Improvement to the whole codebase (scalar and return type hints, strict types enforced, see [#93](https://github.com/facile-it/paraunit/pull/93))
* Realign options passed through to PHPUnit:
    * Added `--strict-coverage`
    * Added `--disallow-resource-usage`
    * Added `--no-coverage`
    * Added `--no-extensions`
    * Changed `--report-useless-tests` to `--dont-report-useless-tests` 
    * Changed `--no-globals-backup` to `--globals-backup` 

### Fixed
* The `--repeat` option now works correctly (see [#92](https://github.com/facile-it/paraunit/issues/92))
* The PHPUnit configuration is no longer copied temporarily in the current working folder, it's no longer needed

## [0.8.3] - 2017-07-06

### Fixed
* Add missing dependencies: `symfony/yaml` and `symfony/stopwatch` (backported from 0.9.2)

## [0.8.2] - 2017-01-26

### Fixed

* Fix the signature of `LogPrinter`, to avoid warnings (this would lead to no test execution when considering warnings as test failures)

## [0.8.1] - 2017-01-25

### Fixed

* Fix #88: write the temporary configuration file in the same dir of the original, to avoid issue with Symfony when it guesses its kernel dir

## [0.8] - 2017-01-24

### Changed

* Add support for two additional coverage output format: `--php` and `--crap4j`
* Add support for colored output when using `--text-to-console`: just use the `--ansi` option with it
* Copied and moved the JSON log printer from PHPUnit to Paraunit, to allow further support and better control over the logs
* When printing the file recap at the end of the execution, print the test's FQCN if possible instead of the filename
* Various internal refactoring (DI, configuration, runner, coverage processors)
* Added Continuous Deployment: the PHAR gets built and signed on Travis when tagging, and deployed automatically into a GitHub release (thanks to @heiglandreas for the tips)

## [0.7.4] - 2017-01-11

### Changed

* Add optional argument to both commands to filter tests filenames; the filter is case insensitive and doesn't rely on paths, so it doesn't have to be a full or relative path; it can also be used in conjunction with the `--testsuite` option

## [0.7.3] - 2016-12-30

### Changed

* Add PHAR generation tools (thanks @taueres for the previous PR); from now on, Paraunit is released as a PHAR too!

## [0.7.2] - 2016-12-28

### Fixed

* Disable colored output in text coverage to console, to avoid issues with GitLab integration.  

## [0.7.1] - 2016-12-28

### Changed

* Added support to the text coverage format with two new CLI options: `--text-to-console` to output it directly onto the standard output, and `--text=filename.txt` to save it into a file; this is highly useful with various CI/CD integrations like Jenkins and GitLab.  

## [0.7] - 2016-12-12

### Changed

* Paraunit is now capable of producing the test coverage of your test suites in parallel! To use it, use the new 
 `coverage` command (instead of the normal `run`), along with at least an option to specify the requested format; for
 more information, please use the `--help` option on the CLI or refer to the [documentation](http://engineering.facile.it/paraunit/documentation/).
* It's possible to choose how many concurrent processes Paraunit should spawn using the new `--parallel` option.
* A lot of new options are now supported by Paraunit, and carried over to the single PHPUnit processes. The full list of
 new supported options is:
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
  
 `testsuite` and `configuration` options are still supported; thanks to @sergeyz for suggesting the feature (#56)
* Added support to Windows (thanks to R.D. for the help) and Appveyor CI build
* PHP 7.1 added to the Travis build matrix
* Log (and coverage) temp files are deleted right after being read, to reduce memory consumption (#63)

### Fixed

* Symfony 2.3 compatibility restored: the Travis build now tests with `--prefer-lowest` under PHP 5.3; this added 
  `symfony/http-kernel` to the required packages
* Fixed a minor typo that caused version number to be different in the command help versus the command cli "header"

## [0.6.2] - 2016-10-24

### Changed

* Binaries moved to the `/bin` dir (#50, thanks @garak)

### Fixed

* Temp dir for storing JSON partial logs now works in non-Linux OS too (#52, thanks @thomasvargiu): it previously used a
wrong dir (`/temp`); now it relies on `sys_get_temp_dir()` as a fallback
* Minor fixes to README.md (#48, #49, thanks @garak)
* Paraunit version is shown correctly now when launching it

## [0.6.1] - 2016-06-10

### Changed

* Symfony compatibility has been extended to `<4.0`, since Symfony 3.1 has been released; we will rely on [their BC promise](http://symfony.com/doc/current/contributing/code/bc.html)
* A new approach for dockerfiles, now they will be based on the standard Docker PHP library. This will allow for a
faster image build, cleaner dockerfiles and a more precise PHP version targeting, including:
  * `docker/dockerfile-php-5.6` with the related `docker/setup-php-5.6.sh` script that will allow to build a container
  starting from the PHP 5.6 image
  * `docker/dockerfile-php-7` with the related  `docker/setup-php-7.sh` script that will allow to build a container
  starting from the PHP 7.0 image
* Paraunit now adopts [PSR-2](http://www.php-fig.org/psr/psr-2/) as a coding style
  * A `contrib/contributing.sh` has been added to tidy the code style, using `phpcbf`
  * A git pre-commit hook is enabled during the `composer install` command to warn about code style violations
* CONTRIBUTING.md file has been added, with instructions for coding style and usage of Docker images for development

## [0.6] - 2016-03-20

### Changed

* MASSIVE refactoring of the result output parsing (#31, #33) and the result printing (#37): now Paraunit fetches the 
  tests' results using PHPUnit's `--log-json` option (thanks to @taueres for the idea).
  This grants a lot of new features:
  * Parsing of tests results is more robust, it should never fail!
  * Improved performances! The JSON parsing is faster, even with unit tests
  * Fatal errors or segfaults are now grouped as "Abnormal termination"
  * When a test has an abnormal termination, the culpable test function is indicated
  * Tests with abnormal termination are printed out in full output
  * Tests executed in a test class that has a later abnormal termination are showed and counted in results anyhow
* Added support for warnings, introduced in PHPUnit 5.1 (#30)
* Added support for risky outcomes

### Fixed

* Removed `Container.php` file and `CompilerPass` class in favor of proper usage of Symfony's components
* `paraunit` bin now uses the container directly
* FinalPrinter class splitted in 3 classes 

## [0.5] - 2015-12-08

### Changed

* SQLite is now supported in the deadlock-recognition fase (issue #26, thanks @Algatux)
* Travis is testing on PHP 7.0, no more nightlies or allowed failures
* Symfony 3.0 components are now supported and compatible with Paraunit
* Retry parsing and management is improved

### Fixed

* Removed double generation of MD5 hash of process

## [0.4.4] - 2015-11-15

### Fixed

* Merged previous `v0.4` branch, there were missing fixes in previous release

## [0.4.3] - 2015-11-04

### Changed

*  `--configuration` behaviour is now identical to the same option in PHPUnit: it can accept also a path without a filename,
   and has a `-c` shortcut (default filename is `phpunit.xml.dist`) [#19]
* Added `-c` shortcut to README

## [0.4.2] - 2015-10-19

### Changed

* Unlocked supporto for PHPUnit >= 5.x
* README changed to suggest stable version of package in `composer.json` example

### Fixed

* Fixed: test stub for fatal errors fixed for HHVM & PHP7

## [0.4.1] - 2015-09-23

### Changed

* Improved: `--configuration` parameter now has the `-c` shortcut

### Fixed

* Fixed: test with fatal errors are not mistaken (and printed twice) for unknown results in the final results

## [0.4.0] - 2015-09-08

* Initial public release
