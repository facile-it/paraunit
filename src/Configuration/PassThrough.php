<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class PassThrough
{
    private const DISALLOWED_OPTIONS = [
        '--no-configuration',
        '--no-extensions',
        '--no-logging',
        '--coverage-php',
        '--teamcity',
        '--testdox',
        '--atleast-version',
        '--check-version',
        '--generate-configuration',
        '--migrate-configuration',
        '--list-suites',
        '--list-groups',
        '--list-tests',
        '--list-tests-xml',
    ];

    /** @var list<string> */
    public readonly array $options;

    /**
     * @param string[]|null $options
     */
    public function __construct(?array $options = [])
    {
        foreach ($options ?? [] as $option) {
            if (in_array($option, self::DISALLOWED_OPTIONS)) {
                throw new \InvalidArgumentException('Invalid passed-through option: ' . $option);
            }
        }

        $this->options = array_values($options ?? []);
    }
}
