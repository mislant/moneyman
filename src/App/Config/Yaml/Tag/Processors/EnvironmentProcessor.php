<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag\Processors;

use Moneyman\App\Config\Yaml\Tag\{TagProcessor, TagProcessStrategy};
use Moneyman\App\Config\Configurator;
use Moneyman\App\Config\ConfigureException;
use Moneyman\App\Config\Environment;

/**
 * Environment processor
 *
 * Replaces tag by current application environment. Helps
 * to get sub configs for curtain environment. This class uses
 * Environment class and directly depends on it
 *
 * @see Environment
 *
 * @package Moneyman\App\Config\Tag\Processors
 */
final class EnvironmentProcessor implements TagProcessStrategy
{
    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): array
    {
        try {
            $envDir = Environment::current()->value();
        } catch (\Exception) {
            throw new ConfigureException("Can't get environment!");
        }
        return Configurator::$locator->compiler()->compile(
            Configurator::$locator->config()->sub(
                "$envDir{$this->parsePath($value)}"
            )
        );
    }

    /**
     * Parses value into string
     * Value should compiles in path string
     *
     * @param mixed $value
     *
     * @return string
     *
     * @throws ConfigureException
     */
    private function parsePath(mixed $value): string
    {
        $value = TagProcessor::prepareValue($value);
        if (!is_string($value)) {
            throw new ConfigureException("Value isn't correct for compiling!");
        }
        return $value;
    }
}