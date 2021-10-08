<?php

$additionalRules = [
    'declare_strict_types' => true,
    'no_superfluous_phpdoc_tags' => true,
    'php_unit_construct' => true,
    'php_unit_dedicate_assert' => true,
    'phpdoc_to_comment' => false,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'random_api_migration' => true,
    'self_accessor' => true,
];
$rulesProvider = new Facile\CodingStandards\Rules\CompositeRulesProvider([
    new Facile\CodingStandards\Rules\DefaultRulesProvider(),
    new Facile\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
]);

$config = new PhpCsFixer\Config();
$config->setRules($rulesProvider->getRules());

$config->setUsingCache(true);
$config->setRiskyAllowed(true);

$autoloadPathProvider = new Facile\CodingStandards\AutoloadPathProvider();

$finder = new PhpCsFixer\Finder();
$finder->in($autoloadPathProvider->getPaths());
$finder->notName('Coverage4Stub.php');
$finder->notName('ParseErrorTestStub.php');
$config->setFinder($finder);

return $config;
