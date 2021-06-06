<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag;

use Moneyman\App\Config\ConfigureException;

/**
 * Tag process strategy
 *
 * This interface describes tag parsing algorithm. Helps to create some
 * specific logic for yaml tag
 *
 * @package Moneyman\App\Config\Tag
 */
interface TagProcessStrategy
{
    /**
     * Processes tag's value
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws ConfigureException
     */
    public function process(mixed $value): mixed;
}