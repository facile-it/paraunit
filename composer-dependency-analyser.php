<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

$config->ignoreUnknownClassesRegex('/Php81|3GarbageCollectorStatusProvider/');

/**  eErrorsOnPackages([
    'symfony/dotenv',
    'symfony/http-client',
], [ErrorType::PROD_DEPENDENCY_ONLY_IN_DEV]);

$config->ignoreErrorsOnPackages([
    'dama/doctrine-test-bundle',
    'doctrine/doctrine-fixtures-bundle',
    'symfony/web-profiler-bundle',
], [ErrorType::DEV_DEPENDENCY_IN_PROD]);

$config->ignoreErrorsOnPackages([
    'facile-it/doctrine-mysql-come-back',
    'league/flysystem-aws-s3-v3',
    'league/flysystem-ftp',
    'symfony/amqp-messenger',
    'symfony/asset-mapper',
    'symfony/doctrine-messenger',
    'symfony/flex',
    'symfony/runtime',
    'symfony/translation',
    'symfony/yaml',
    'twig/intl-extra', // for format_currency filter
    'twig/string-extra', // for u.truncate filter in SOAP XMLs fields
], [ErrorType::UNUSED_DEPENDENCY]);

*/

return $config;
