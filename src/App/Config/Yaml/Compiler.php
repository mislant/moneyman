<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml;

use Moneyman\App\Config\CompilerInterface;
use Moneyman\App\Config\Yaml\Tag\TagProcessor;
use Symfony\Component\Yaml\{Tag\TaggedValue, Yaml};

/**
 * Compiler
 *
 * This class compiles yaml configuration files. It uses symfony
 * yaml parses and provides ability to fork with tags
 *
 * @package Moneyman\App\Config\Yaml
 */
final class Compiler implements CompilerInterface
{
    /**
     * {@inheritdoc}
     */
    public function compile(string $data): array
    {
        $data = Yaml::parse($data, Yaml::PARSE_CUSTOM_TAGS);
        if (is_array($data)) {
            foreach ($data as $name => $item) {
                if ($item instanceof TaggedValue) {
                    $out[$name] = (new TagProcessor($item->getTag()))->process($item->getValue());
                    continue;
                }
                if (is_array($item)) {
                    foreach ($item as &$part) {
                        if ($part instanceof TaggedValue) {
                            $part = (new TagProcessor($part->getTag()))->process($part->getValue());
                        }
                    }
                }
                $out[$name] = $item;
            }
        } else {
            if ($data instanceof TaggedValue) {
                $out = (new TagProcessor($data->getTag()))->process($data->getValue());
                if (!is_array($out)) {
                    $out[] = $out;
                }
            }
        }
        return $out ?? [];
    }
}