<?php

declare(strict_types=1);

namespace Moneyman\App\Cycle;

use Cycle\ORM\SchemaInterface;

/**
 * Schema manager interface
 *
 * Describes method needed for schema creation. Schema is array of with data
 * for mapping layer in CycleOrm. It contains entities their mapping
 * layers, repositories and other.
 *
 * @see SchemaInterface
 *
 * @package Moneyman\App\Cycle
 */
interface SchemaManagerInterface
{
    /**
     * Gets schema
     *
     * @return SchemaInterface
     */
    public function get(): SchemaInterface;
}