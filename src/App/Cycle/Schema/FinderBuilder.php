<?php

declare(strict_types=1);

namespace Moneyman\App\Cycle\Schema;

use Symfony\Component\Finder\Finder;

/**
 * Finder builder
 *
 * Produce finder object with specific
 * configurations
 *
 * @package Moneyman\App\Cycle\Schema
 */
final class FinderBuilder
{
    /**
     * Finder for entities
     *
     * Configuration for builder methods of Symfony's finder
     * Example:
     * ```php
     * [
     *  '<methodName>' => '<params>'
     * ]
     * ```
     * You can use single value, array and callback as params
     *
     * @see Finder
     *
     * @var array
     */
    private array $finderOptions;

    /**
     * FinderBuilder constructor
     *
     * @param array $finderOptions
     */
    public function __construct(array $finderOptions)
    {
        $this->finderOptions = $finderOptions;
    }

    /**
     * Creates finder
     *
     * Finder helps to get files in filesystem
     * by special configurations
     *
     * @return Finder
     */
    public function make(): Finder
    {
        $finder = new Finder();
        foreach ($this->finderOptions as $method => $params) {
            $this->setOption($finder, $method, $params);
        }
        return $finder;
    }

    /**
     * Sets finder option
     *
     * Sets option by using Finder building method and
     * it's parameters
     *
     * @param Finder $finder
     * @param string $method
     * @param mixed $params
     */
    private function setOption(
        Finder $finder,
        string $method,
        mixed $params
    ): void
    {
        if (is_array($params)) {
            $finder->$method(...$params);
        } elseif (is_null($params)) {
            $finder->$method();
        } elseif (is_callable($params)) {
            $this->setOption($finder, $method, $params());
        } else {
            $finder->$method($params);
        }
    }
}