<?php

declare(strict_types=1);

namespace Moneyman\App\Cycle\Schema;

use Moneyman\App\Cycle\CycleAdapter;
use Cycle\ORM\{Schema, SchemaInterface};
use Cycle\Schema\{
    Generator\GenerateRelations,
    Generator\GenerateTypecast,
    Generator\RenderRelations,
    Generator\RenderTables,
    Generator\ResetTables,
    Generator\ValidateEntities,
    Compiler,
    Registry,
};
use Cycle\Annotated\{Embeddings, Entities, MergeColumns};
use Psr\Cache\{CacheItemPoolInterface, InvalidArgumentException};
use Spiral\Database\{Config\DatabaseConfig, DatabaseManager};
use Spiral\Tokenizer\ClassLocator;
use Moneyman\App\Cycle\SchemaManagerInterface;

/**
 * Schema manager
 *
 * Implements SchemaManagerInterface
 *
 * @see SchemaManagerInterface
 *
 * @package Moneyman\App\Cycle\Schema
 */
final class SchemaManager implements SchemaManagerInterface
{
    private const CACHE = 'schema_cache';

    /**
     * Force schema building class.
     *
     * If true manager
     * always produce new scheme
     *
     * @var bool
     */
    private bool $forceBuild;
    /**
     * Database configurations
     *
     * @see CycleAdapter::$dbConfigs
     *
     * @var array
     */
    private array $dbConfigs;
    /**
     * Configurations for finder
     *
     * @see FinderBuilder::$finderOptions
     *
     * @var array
     */
    private array $finderOptions;
    private CacheItemPoolInterface $cacheItemPool;


    /**
     * SchemaManager constructor
     *
     * @param array $dbConfigs
     * @param array $finderOptions
     * @param CacheItemPoolInterface $cacheItemPool
     * @param bool $forceBuild
     */
    public function __construct(array $dbConfigs, array $finderOptions, CacheItemPoolInterface $cacheItemPool, bool $forceBuild = false)
    {
        $this->dbConfigs = $dbConfigs;
        $this->cacheItemPool = $cacheItemPool;
        $this->finderOptions = $finderOptions;
        $this->forceBuild = $forceBuild;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): SchemaInterface
    {
        try {
            $schema = $this->getFromCache();
            if (isset($schema)) {
                return $schema;
            }
            return $this->buildNew();
        } catch (InvalidArgumentException) {
            return new Schema([]);
        }
    }

    /**
     * Gets schema from cache
     *
     * Tries to get schema from cache. If not found null
     * returns
     *
     * @return SchemaInterface|null
     *
     * @throws InvalidArgumentException
     */
    private function getFromCache(): ?SchemaInterface
    {
        $cacheItem = $this->cacheItemPool->getItem(self::CACHE);
        if ($cacheItem->isHit() && !$this->forceBuild) {
            return $cacheItem->get();
        }
        return null;
    }

    /**
     * Builds new schema
     *
     * Creates finder to make entity class locator
     * needed for schema generating. Schema generator
     * parses all entities and by their annotations crates schema
     *
     * @return SchemaInterface
     *
     * @throws InvalidArgumentException
     */
    private function buildNew(): SchemaInterface
    {
        $finder = (new FinderBuilder($this->finderOptions))->make();
        $entityClassLocator = new ClassLocator($finder);
        $schema = new Schema((new Compiler())->compile(new Registry(new DatabaseManager(new DatabaseConfig($this->dbConfigs))), [
            new ResetTables(),
            new Embeddings($entityClassLocator),
            new Entities($entityClassLocator),
            new MergeColumns(),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new GenerateTypecast()
        ]));
        if (!$this->forceBuild) {
            $cacheItem = $this->cacheItemPool->getItem(self::CACHE);
            $cacheItem->set($schema);
            $this->cacheItemPool->save($cacheItem);
        }
        return $schema;
    }
}
