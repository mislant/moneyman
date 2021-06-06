<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml;

use Moneyman\App\Config\{ConfigInterface, ConfigureException};
use Yiisoft\Json\Json;

/**
 * Configuration resources
 *
 * Contains folder and enter point configuration file.
 * Gets content files of yaml files
 *
 * @package Moneyman\App\Config
 */
final class Config implements ConfigInterface
{
    /**
     * Name of enter point file
     *
     * @var string
     */
    private string $file;
    /**
     * Configurations path
     *
     * Path of directory with configuration.
     *
     * @var string
     */
    private string $dir;

    /**
     * Config constructor
     *
     * @param string $dir
     * @param string $file
     *
     * @throws ConfigureException
     */
    public function __construct(string $dir, string $file)
    {
        $this->ensureDir($dir);
        $this->ensureEnterFile($dir, $file);
        $this->dir = $dir;
        $this->file = $file;
    }

    /**
     * Ensures is directory correct
     *
     * @param string $dir
     *
     * @throws ConfigureException
     */
    private function ensureDir(string $dir): void
    {
        if (!file_exists($dir) || !is_dir($dir) || !is_readable($dir)) {
            throw new ConfigureException("$dir doesn't exist or readable!");
        }
    }

    /**
     * Ensures is enter point file correct
     *
     * @param string $dir
     * @param string $file
     *
     * @throws ConfigureException
     */
    private function ensureEnterFile(string $dir, string $file): void
    {
        $main = "$dir/$file.yaml";
        if (!file_exists($main) || !is_readable($main)) {
            throw new ConfigureException("Enter file $main doesn't exist or readable!");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dir(): string
    {
        return $this->dir;
    }

    /**
     * {@inheritdoc}
     */
    public function main(): string
    {
        return $this->getContent("$this->dir/$this->file.yaml");
    }

    /**
     * {@inheritdoc}
     */
    public function sub(string $path): string
    {
        return $this->getContent("$this->dir/$path.yaml");
    }

    /**
     * Gets file content
     *
     * @param string $path
     *
     * @return string
     *
     * @throws ConfigureException
     */
    private function getContent(string $path): string
    {
        $res = file_get_contents($path);
        if (!$res) {
            try {
                $error = Json::encode(error_get_last());
                throw new ConfigureException("Error: $error, while reading $path!");
            } catch (\JsonException) {
                throw new ConfigureException("There is an error while reading $path!");
            }
        }
        return $res;
    }
}