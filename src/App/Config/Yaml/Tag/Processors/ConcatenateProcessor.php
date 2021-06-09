<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag\Processors;

use Moneyman\App\Config\Yaml\Tag\{TagProcessor, TagProcessStrategy};
use Moneyman\App\Config\ConfigureException;

/**
 * Concatenate tag processor
 *
 * This tag concatenates strings. Values has to be array of strings.
 *
 * @package Moneyman\App\Config\Yaml\Tag\Processors
 */
final class ConcatenateProcessor implements TagProcessStrategy
{
    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): string
    {
        if (!is_array($value)) {
            throw new ConfigureException("Can't get parts from tag value!");
        }
        $parts = $this->prepareValues($value);
        return implode('', $parts);
    }

    /**
     * Prepares value
     *
     * Gather all parts in one array and checks them
     * for correct type
     *
     * @param array $value
     *
     * @return array
     *
     * @throws ConfigureException
     */
    public function prepareValues(array $value): array
    {
        foreach (TagProcessor::prepareValue($value) as $part) {
            if (!is_string($part)) {
                throw new ConfigureException("Invalid arguments for concatenation!");
            }
            $parts[] = $part;
        }
        return $parts ?? [];
    }
}