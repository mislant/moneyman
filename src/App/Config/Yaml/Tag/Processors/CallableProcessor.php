<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag\Processors;

use Moneyman\App\Config\Yaml\Tag\{TagProcessor, TagProcessStrategy};
use Moneyman\App\Config\ConfigureException;
use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * Callable tag processor
 *
 * Callable tag defines name of function and its arguments
 * ```yaml
 * !callable
 *   name: substr
 *   args:
 *     - string
 *     - 0
 *     - 3
 * ```
 * result will be `str`
 *
 * Processor supports nested tags and all data types. Tag has to consist of key `name`
 * and `args`
 *
 * @package Moneyman\App\Config\Tag\Processors
 */
final class CallableProcessor implements TagProcessStrategy
{
    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): mixed
    {
        $this->ensureInputs($value);
        list($callable, $args) = $this->parseValues($value);
        if (isset($args)) {
            if (is_array($args)) {
                return $callable(...$args);
            } else {
                return $callable($args);
            }
        }
        return $callable();
    }

    /**
     * Ensure input values is correct
     *
     * Tag input has to contain `name` and 'args' keys
     *
     * @param array $value
     *
     * @throws ConfigureException
     */
    private function ensureInputs(array $value): void
    {
        if (!isset($value['name']) || !isset($value['args'])) {
            throw new ConfigureException("Callable tag values isn't correct!");
        }
    }

    /**
     * Parses value of tag
     *
     * Returns array with two values, where first is callable name
     * and last is list of arguments
     *
     * @param array $value
     *
     * @return array
     *
     * @throws ConfigureException
     */
    private function parseValues(array $value): array
    {
        $values = TagProcessor::prepareValue($value);
        return [$values['name'], $values['args']];
    }
}