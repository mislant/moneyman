<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag\Processors;

use Moneyman\App\Config\Yaml\Tag\{TagProcessor, TagProcessStrategy};
use Moneyman\App\Config\ConfigureException;
use Yiisoft\Arrays\ArrayHelper;

/**
 * Merge tag processor
 *
 * Merges tag values in one array. Uses Yiisoft/ArrayHelper.
 * Value hast to be array
 *
 * @see ArrayHelper
 *
 * @package Moneyman\App\Config\Yaml\Tag\Processors
 */
final class MergeProcessor implements TagProcessStrategy
{
    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): array
    {
        $value = TagProcessor::prepareValue($value);
        if (!is_array($value)) {
            throw new ConfigureException("Can't merge not array value!");
        }
        return ArrayHelper::merge(...$value);
    }
}