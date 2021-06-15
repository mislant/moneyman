<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag\Processors;

use Moneyman\App\Config\ConfigureException;
use Moneyman\App\Config\Yaml\Tag\{TagProcessor, TagProcessStrategy};

/**
 * Variable tag processor
 *
 * This processor provides ability to create global variables. This tag can
 * be used in two ways:
 *  - with `set` key. This key is needed for create global variable. Also it
 *    needed to set `value` key
 *  - without `set` key. If you wants get variable value
 *
 * _Example:_
 * ```
 *  - !var {set: variable, value: some_value} # to set variable
 *  .
 *  .
 *  - !var variable
 * ```
 *
 * @package Moneyman\App\Config\Yaml\Tag\Processors
 */
final class VariableProcessor implements TagProcessStrategy
{
    /**
     * Container for variables
     *
     * @var array
     */
    private static array $container;

    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): mixed
    {
        $value = TagProcessor::prepareValue($value);
        if ($this->isSetMode($value)) {
            return $this->setVariable($value);
        }
        if (!self::$container[$value]) {
            throw new ConfigureException("Can't find variable $value!");
        }
        return self::$container[$value];
    }

    /**
     * Checks witch mode needed processor
     * works
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function isSetMode(mixed $value): bool
    {
        return is_array($value);
    }

    /**
     * Sets variable
     *
     * Sets variable and returns it in
     * configurations
     *
     * @param array $value
     *
     * @return mixed
     *
     * @throws ConfigureException
     */
    private function setVariable(array $value): mixed
    {
        $this->ensureValue($value);
        list($key, $value) = [$value['set'], $value['value']];
        if (isset(self::$container[$key])) {
            throw new ConfigureException("Can't set key $key twice!");
        }
        return self::$container[$key] = $value;
    }

    /**
     * Ensures input value valid to set variable
     *
     * @param array $value
     *
     * @throws ConfigureException
     */
    private function ensureValue(array $value): void
    {
        if (!isset($value['set']) || !isset($value['value'])) {
            throw new ConfigureException("Value is not valid for setting variable!");
        }
    }
}