<?php

declare(strict_types=1);

namespace Moneyman\App\Config;

use Moneyman\App\Config\Yaml\Compiler;
use Psr\Cache\{CacheItemInterface, CacheItemPoolInterface, InvalidArgumentException};

/**
 * Application configurator
 *
 * @package Moneyman\App\Config
 */
final class Configurator
{
    /** Key for compiled configuration item */
    private const CACHE = 'config_cache_item';

    private ConfigInterface $configs;
    private CompilerInterface $compiler;
    private CacheItemPoolInterface $cacheItemPool;
    /**
     * Helps to locate configurator
     * part from other point of program
     * @var Configurator|null
     */
    public static ?Configurator $locator;

    /**
     * Configurator constructor
     *
     * @param ConfigInterface $configs
     * @param CompilerInterface $compiler
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(
        ConfigInterface $configs,
        CompilerInterface $compiler,
        CacheItemPoolInterface $cacheItemPool,
    )
    {
        $this->configs = $configs;
        $this->compiler = $compiler;
        $this->cacheItemPool = $cacheItemPool;
        self::$locator = $this;
    }

    /**
     * Gets configs
     *
     * @return ConfigInterface
     */
    public function config(): ConfigInterface
    {
        return $this->configs;
    }

    /**
     * Gets compiler
     *
     * @return Compiler
     */
    public function compiler(): Compiler
    {
        return $this->compiler;
    }

    /**
     * Runs config compilation
     *
     * @param bool $forcedRebuild
     * If true rebuilds configuration every new run
     *
     * @return array
     *
     * @throws ConfigureException
     */
    public function run(bool $forcedRebuild = false): array
    {
        $cacheItem = $this->getFromCache();
        if ($cacheItem->isHit() && !$forcedRebuild) {
            return $cacheItem->get();
        }

        $out = $this->compiler->compile($this->configs->main());
        $this->saveInCache($cacheItem, $out);

        $this->freeMem();
        return $out;
    }

    /**
     * Gets configuration from cache
     *
     * @return CacheItemInterface
     *
     * @throws ConfigureException
     */
    private function getFromCache(): CacheItemInterface
    {
        try {
            return $this->cacheItemPool->getItem(self::CACHE);
        } catch (InvalidArgumentException $e) {
            throw new ConfigureException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Saves out configuration cache
     *
     * @param CacheItemInterface $item
     *
     * @param array $configuration
     */
    private function saveInCache(CacheItemInterface $item, array $configuration): void
    {
        $item->set($configuration);
        $this->cacheItemPool->save($item);
    }

    /**
     * Free memory
     */
    private function freeMem(): void
    {
        self::$locator = null;
    }
}