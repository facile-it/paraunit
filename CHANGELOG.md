# Changelog
All notable changes to this project will be documented in this file. For previous changes, refer to the [CHANGELOG-0.x.md](https://github.com/facile-it/paraunit/blob/0.12.x/CHANGELOG-0.x.md) document.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased
 * Add support for Symfony 7

## [2.2.2] - 2023-10-06
 * Add support for PHPUnit 10.4 [#218](https://github.com/facile-it/paraunit/pull/218)

## [2.2.1] - 2023-08-30
 * Add output when failing due to PHPUnit runner errors (i.e. with an empty data provider) [#217](https://github.com/facile-it/paraunit/pull/217)

## [2.2.0] - 2023-06-08
 * Add `--cobertura` coverage report format, useful for [GitLab test code coverage visualization](https://docs.gitlab.com/ee/ci/testing/test_coverage_visualization.html#php-example) [#206](https://github.com/facile-it/paraunit/pull/206)

## [2.1.0] - 2023-05-03
 * Add `--sort=random` option to execute test classes in random order

## [2.0.1] - 2023-04-28
## Fixed
 * Fix handling of second outcome on last test of class (i.e. deprecation emitted after the last test has passed) [#204](https://github.com/facile-it/paraunit/pull/204)

## [2.0.0] - 2023-03-06
### Added
 * Support for PHPUnit 10
 * `--pass-through` option [#194](https://github.com/facile-it/paraunit/pull/194)
### Changed
 * The integration mechanic with PHPUnit has now changed, and it now leverages the new [event system](https://github.com/sebastianbergmann/phpunit/issues/4676); to do that, Paraunit will need a bootstrap extension registered in the PHPUnit XML config; at the first run without it, Paraunit will ask if you want to add it automatically [#186](https://github.com/facile-it/paraunit/pull/186)
### Removed
 * Drop support for PHPUnit < 10 

## [1.3.0] - 2022-06-15
### Added
 * Add `--chunk-size` option [#164](https://github.com/facile-it/paraunit/pull/164)
 * Add native enabling of Xdebug coverage mode [#170](https://github.com/facile-it/paraunit/pull/170)
 * Report full process output when in debug mode [#170](https://github.com/facile-it/paraunit/pull/170)
 * Add support for Symfony 6 [#168](https://github.com/facile-it/paraunit/pull/168)
### Removed
 * Drop support for Symfony < 4.4 [#168](https://github.com/facile-it/paraunit/pull/168)

## [1.2.1] - 2021-03-25
### Added
 * Allow `jean85/pretty-package-versions` v2 [90f84b5](https://github.com/facile-it/paraunit/commit/90f84b545323053833834ea6d1b2641bd2d810f0)

## [1.2.0] - 2020-09-15
### Added
 * Add support for PHP 8.0 [#154](https://github.com/facile-it/paraunit/pull/154)
 * Add support for PHPUnit 9.3 [#153](https://github.com/facile-it/paraunit/pull/153)
### Removed
 * Drop support for PHPUnit < 9.3 [#153](https://github.com/facile-it/paraunit/pull/153)

## [1.1.1] - 2020-05-06
### Added
 * Add support for deadlock detection on PostgreSQL [#152](https://github.com/facile-it/paraunit/pull/152), thanks @elernonelma

## [1.1.0] - 2020-04-03
### Added
 * Add support for PHPUnit 9.1 [#149](https://github.com/facile-it/paraunit/pull/149)
### Changed
 * Large internal refactor from using PHPUnit's `--printer` to `TestHook`s [#149](https://github.com/facile-it/paraunit/pull/149)
### Removed
 * Drop support for PHP <= 7.2 [#149](https://github.com/facile-it/paraunit/pull/149)
 * Drop support for PHPUnit <= 9.0 [#149](https://github.com/facile-it/paraunit/pull/149)

## [1.0.1] - 2020-03-23
### Fixed
 * Fix handling of PHPUnit `--stderr` option [#144](https://github.com/facile-it/paraunit/pull/144), thanks @pczerkas
 * Fix small issue in checking coverage data syntax [8f70c](https://github.com/facile-it/paraunit/commit/8f70c479adf266ccec59103b20895c02ac7ef4c3)

## [1.0.0] - 2020-03-11
First stable release. The following changes are in comparison to the previous, unstable release (0.12.3), split into the beta releases that were tagged in the meantime.
### Removed
 * PHAR release (it's not working)

## [1.0.0-beta2] - 2020-02-26
### Added
 * Add support for PHP 7.4
 * Add support for Symfony 5
 * Add support for ext-pcov as a coverage driver [#146](https://github.com/facile-it/paraunit/pull/146)

### Changed
 * Update PHPStan to 0.12 [#145](https://github.com/facile-it/paraunit/pull/145)
 * Prefer Pcov or Xdebug over PHPDBG as coverage driver [#146](https://github.com/facile-it/paraunit/pull/146)

## [1.0.0-beta1] - 2019-04-08
### Breaking changes
 * Drop support for older packages: [#134](https://github.com/facile-it/paraunit/pull/134)
   * `phpunit/phpunit` 6
   * `phpunit/php-code-coverage` < 6
   * All Symfony components < 3.4
 * Scalar and return types added everywhere possible

### Added
 * Add support for `phpunit/phpunit` 8 and `phpunit/php-code-coverage` 7 [#133](https://github.com/facile-it/paraunit/pull/133)
 * Add explicit requirement for `ext-dom` and `ext-json` [#134](https://github.com/facile-it/paraunit/pull/134)

### Fixed
 * Do not set values on PHPUnit options that do not expect values [#127](https://github.com/facile-it/paraunit/pull/127), thanks @fullbl

### Changed
 * Update PHPStan to 0.11 [#128](https://github.com/facile-it/paraunit/pull/128)
 * Update coding standard to 0.3 [#131](https://github.com/facile-it/paraunit/pull/131)
 * Disable Scrutinizer [#132](https://github.com/facile-it/paraunit/pull/132)
