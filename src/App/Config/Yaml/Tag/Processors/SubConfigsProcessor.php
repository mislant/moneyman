<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag\Processors;

use Moneyman\App\Config\Configurator;
use Moneyman\App\Config\ConfigureException;
use Moneyman\App\Config\Yaml\Tag\{TagProcessor, TagProcessStrategy};

/**
 * Sub configures processor
 *
 * Compiles sub configuration files
 *
 * @package Moneyman\App\Config\Yaml\Tag\Processors
 */
final class SubConfigsProcessor implements TagProcessStrategy
{
    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): array
    {
        $path = $this->parsePath($value);
        return Configurator::$locator->compiler()->compile(
            Configurator::$locator->config()->sub($path)
        );
    }

    /**
     * Parses value into string
     * Value should compiles in path string
     *
     * @param mixed $value
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