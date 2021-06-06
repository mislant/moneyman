<?php

declare(strict_types=1);

namespace Moneyman\App\Config;

/**
 * Compiler interface
 *
 * Compiler provides method to convert input
 * configuration content in one array.
 *
 * @package Moneyman\App\Config
 */
interface CompilerInterface
{
    /**
     * Compiles raw string data
     *
     * @param string $data
     * This is content string of configuration file.
     *
     * @return mixed
     *
     * @throws ConfigureException
     */
    public function compile(string $data): array;
}