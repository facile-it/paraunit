<?php

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/** @var ContainerBuilder $container */


//services:
//  paraunit.configuration.phpunit_bin_file:
//    class: Paraunit\Configuration\PHPUnitBinFile
//
$container->setDefinition('paraunit.configuration.phpunit_bin_file', new Definition(PHPUnitBinFile::class));
//  paraunit.configuration.phpunit_config:
//    class: Paraunit\Configuration\PHPUnitConfig
//    arguments:
//      - '%paraunit.phpunit_config_filename%'
$container->setDefinition('paraunit.configuration.phpunit_config', new Definition(PHPUnitConfig::class, ['%paraunit.phpunit_config_filename%']));
//
//  paraunit.configuration.temp_filename_factory:
//    class: Paraunit\Configuration\TempFilenameFactory
//    arguments:
//      - "@paraunit.file.temp_directory"
$container->setDefinition('paraunit.configuration.temp_filename_factory', new Definition(TempFilenameFactory::class, [new Reference('paraunit.file.temp_directory')]));
