<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag;

/**
 * List of available tags
 *
 * This class contains of available tags needed
 * to create application configurations
 *
 * @package Moneyman\App\Config
 */
final class Tag
{
    /**
     * Helps to highlight callback
     *
     * Callable tag should contain list with name
     * of callable function and arguments
     */
    public const CALLABLE = 'callable';
    /**
     * Determines environment directory
     */
    public const ENV = 'env';
    /**
     * Indicates that next values will be concatenates in one
     * (works only with strings)
     */
    public const CONCATENATE = 'concatenate';
    /**
     * Indicates that next values will be merged in one
     * (works with lists and arrays)
     */
    public const MERGE = 'merge';
    /**
     * Shows next value  will be interpreted like code
     */
    public const INTERPRET = 'interpret';
    /**
     * Means that current config will be get from
     * another file
     */
    public const SUB_CONF = 'subconf';

    /**
     * Returns list of tags
     *
     * @return array
     */
    public static function list(): array
    {
        $constants = (new \ReflectionClass(self::class))->getConstants();
        return array_values($constants);
    }
}