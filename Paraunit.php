<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Paraunit\Command\ParallelCommand;

require 'Container.php';

$loader->load('services.yml');

$command = new ParallelCommand(
    $container->get('facile.cbr.parallel_test_bundle.filter.filter'),
    $container->get('facile.cbr.parallel_test_bundle.runner.runner')
);

$application = new Application('Paraunit', '0.4');
$application->add($command);
$application->run();


