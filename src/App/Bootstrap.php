<?php

declare(strict_types=1);

namespace Moneyman\App;

use Moneyman\App\Config\{
    Configurator,
    ConfigureException,
    Environment,
    Yaml\Compiler,
    Yaml\Config
};
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Bootstraps components
 *
 * Runs component for application work
 *
 * @package Moneyman\App
 */
final class Bootstrap
{
    /**
     * Initializes environment
     *
     * @throws \Exception
     */
    public function env(): void
    {
        Environment::define(EnvList::DEV);
    }

    /**
     * Gets configs
     *
     * @return array
     *
     * @throws ConfigureException
     */
    public function configs(): array
    {
        $configurator = new Configurator(
            new Config(dirname($_SERVER['DOCUMENT_ROOT']) . '/config', 'main'),
            new Compiler(),
            new FilesystemAdapter()
        );
        $result = $configurator->run();
        $configurator = null;
        return $result;
    }
}