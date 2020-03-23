# Changelog
All notable changes to this project will be documented in this file. For previous changes, refer to the [CHANGELOG-0.x.md](https://github.com/facile-it/paraunit/blob/0.12.x/CHANGELOG-0.x.md) document.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).
## Unreleased
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
