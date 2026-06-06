<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Process\CommandLine;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

class CoverageCacheWarmer implements EventSubscriberInterface
{
    public function __construct(
        private readonly CommandLine $cliCommand,
        private readonly PHPUnitConfig $phpUnitConfig,
        private readonly OutputInterface $output,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEngineStart::class => 'warmCache',
        ];
    }

    public function warmCache(): void
    {
        if (! $this->phpUnitConfig->isCoverageCacheEnabled()) {
            $this->output->writeln('');
            $this->output->writeln('WARNING: PHPUnit coverage cache not enabled');
            $this->output->writeln('         To leverage it, set <phpunit cacheDirectory="..."> attribute in ' . $this->phpUnitConfig->getFileFullPath());
            $this->output->writeln('');

            return;
        }

        $process = new SymfonyProcess(
            [...$this->cliCommand->getExecutable(), '--warm-coverage-cache'],
        );
        $this->output->write('Warming up coverage cache... ');
        $process->run();

        if (! $process->isSuccessful()) {
            $this->output->writeln('');
            $this->output->writeln('ERROR: PHPUnit coverage cache warmup failed');
            $this->output->writeln('');
            $this->output->writeln($process->getOutput());
            $this->output->writeln('');
            $this->output->writeln($process->getErrorOutput());

            throw new \RuntimeException('PHPUnit coverage cache warmup failed');
        }

        $this->output->writeln('Done');
    }
}
