<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag\Processors;

use Moneyman\App\Config\Yaml\Tag\{TagProcessor, TagProcessStrategy};
use Moneyman\App\Config\ConfigureException;

/**
 * Get tag processor
 *
 * This processor gets `key` from `from` array and returns it value
 * Helps to configure option by another configuration
 * Tag value has to contain of 2 keys: 'key' - point what to get
 * and 'from' - array from witch value will be get
 *
 * @package Moneyman\App\Config\Yaml\Tag\Processors
 */
final class GetProcessor implements TagProcessStrategy
{
    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): mixed
    {
        list($key, $from) = $this->getKeyAndFrom($value);
        return $from[$key];
    }

    /**
     * Gets 'key' and 'from' values
     *
     * Prepares tag value and check if it is compatible
     *
     * @param array $value
     *
     * @return array
     * [string, array]
     *
     * @throws ConfigureException
     */
    private function getKeyAndFrom(array $value): array
    {
        $value = TagProcessor::prepareValue($value);
        if (!isset($value['key']) || !isset($value['from'])) {
            throw new ConfigureException("Tag value doesn't contain 'key' or 'from'!");
        }
        return [$value['key'], $value['from']];
    }
}