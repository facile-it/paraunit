<?php

namespace Paraunit\Configuration;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * Class Paraunit
 * @package Paraunit\Configuration
 */
class Paraunit
{
    const PARAUNIT_VERSION = '0.5.1';

    private static $tempDirs = array(
        '/dev/shm',
        '/temp',
    );

    /** @var  string */
    private $timestamp;

    /**
     * Paraunit constructor.
     */
    public function __construct()
    {
        $this->timestamp = uniqid(date('Ymd-His'));
    }

    /**
     * @return ContainerBuilder
     */
    public static function buildContainer()
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config/'));
        $loader->load('services.yml');
        $loader->load('parser.yml');
        $loader->load('configuration.yml');
        $loader->load('output_container.yml');

        $containerBuilder->addCompilerPass(new RegisterListenersPass());

        $containerBuilder->setDefinition(
            'event_dispatcher',
            new Definition(
                'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher',
                array(new Reference('service_container'))
            )
        );

        $containerBuilder->compile();

        return $containerBuilder;
    }

    /**
     * @return string
     */
    public function getTempDirForThisExecution()
    {
        $dir = self::getTempBaseDir() . DIRECTORY_SEPARATOR . $this->timestamp;
        self::mkdirIfNotExists($dir);
        self::mkdirIfNotExists($dir . DIRECTORY_SEPARATOR . 'logs');
        self::mkdirIfNotExists($dir . DIRECTORY_SEPARATOR . 'coverage');

        return $dir;
    }

    /**
     * @return string
     */
    public static function getTempBaseDir()
    {
        foreach (self::$tempDirs as $directory) {
            if (file_exists($directory)) {
                $baseDir = $directory . DIRECTORY_SEPARATOR . 'paraunit';
                self::mkdirIfNotExists($baseDir);

                return $baseDir;
            }
        }

        throw new \RuntimeException('Unable to create a temporary directory');
    }

    /**
     * @param string $path
     */
    private static function mkdirIfNotExists($path)
    {
        if ( ! file_exists($path)) {
            mkdir($path);
        }
    }
}
