<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag\Processors;

use Moneyman\App\Config\Yaml\Tag\{TagProcessor, TagProcessStrategy};
use Moneyman\App\Config\ConfigureException;
use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * Interpret tag processor
 *
 * Interpret tag converts it values into string that would be
 * interpret by `eval` function with return
 * ```yaml
 * !interpret '$_SERVER['REQUEST_TIME']'
 * ```
 * result wil be `1242333455`
 *
 * Processor supports nested tags. Value to interpret has to be string
 *
 * @package Moneyman\App\Config\Tag\Processors
 */
final class InterpretProcessor implements TagProcessStrategy
{
    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): mixed
    {
        $command = $this->parseCommand($value);
        try {
            return eval("return {$command};");
        } catch (\Throwable $t) {
            throw new ConfigureException("Input string can't be interpreted!");
        }
    }

    /**
     * Parses value to interpret
     *
     * @param mixed $value
     *
     * @return string
     *
     * @throws ConfigureException
     */
    private function parseCommand(mixed $value): string
    {
        $value = TagProcessor::prepareValue($value);
        if (!is_string($value)) {
            throw new ConfigureException("Value of interpret tag hast to be string");
        }
        return $value;
    }
}