<?php

declare(strict_types=1);

namespace Moneyman\App\Cycle;

use Cycle\ORM\{Factory, FactoryInterface, ORM, SchemaInterface};
use Spiral\Database\{Config\DatabaseConfig, DatabaseManager};
use yii\base\{BaseObject, InvalidArgumentException};

/**
 * Cycle adapter
 *
 * Provides access to ORM.
 *
 * @package Moneyman\App\Cycle
 */
final class CycleAdapter extends BaseObject
{
    /**
     * Database configurations
     *
     * <p>
     * This should be array of configuration needed for
     * DataBaseProviderInterface class. DataBaseConfig used as implementation
     * of configurations.
     * </p>
     * __In this array you should specify next values:__<br>
     * `default` -> string: name of default database<br>
     * `databases` -> array with databases and its connections<br>
     * `connections` -> array of connection to physic db
     *
     * @see DatabaseManager
     *
     * @var array
     */
    private array $dbConfigs;
    private SchemaManagerInterface $schemaManager;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function __construct(SchemaManagerInterface $schemaManager, $config = [])
    {
        $this->dbConfigs =
            $config['dbConfigs'] ??
            throw new InvalidArgumentException("'dbConfig' is required!");
        unset($config['dbConfigs']);
        $this->schemaManager = $schemaManager;
        parent::__construct($config);
    }

    /**
     * Gets orm
     *
     * Orm is service class needed for managing entities and repositories.
     * It implements entity mapping and repositories locating.
     *
     * @return ORM
     */
    public function orm(): ORM
    {
        return new ORM(
            $this->dbal(),
            $this->schema()
        );
    }

    /**
     * Gets factory
     *
     * @return FactoryInterface
     */
    private function dbal(): FactoryInterface
    {
        return new Factory(
            new DatabaseManager(
                new DatabaseConfig($this->dbConfigs)
            )
        );
    }

    /**
     * Gets schema
     *
     * @return SchemaInterface
     */
    private function schema(): SchemaInterface
    {
        return $this->schemaManager->get();
    }
}