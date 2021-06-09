<?php

declare(strict_types=1);

namespace Moneyman\App\Config\Yaml\Tag;

use Moneyman\App\Config\Yaml\Tag\Processors\{
    CallableProcessor,
    ConcatenateProcessor,
    EnvironmentProcessor,
    GetProcessor,
    InterpretProcessor,
    MergeProcessor,
    SubConfigsProcessor
};
use Moneyman\App\Config\ConfigureException;
use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * Tag processor
 *
 * Runs curtain tag processing. Has strategy fabric method to
 * get needed processing algorithm
 *
 * @package Moneyman\App\Config\Tag
 */
final class TagProcessor implements TagProcessStrategy
{
    private TagProcessStrategy $processStrategy;

    /**
     * TagProcessor constructor
     *
     * @param string $tag
     */
    public function __construct(string $tag)
    {
        $this->processStrategy = $this->strategy($tag);
    }

    /**
     * Initializes tag processing strategy
     *
     * @param string $tag
     *
     * @return mixed
     */
    private function strategy(string $tag): TagProcessStrategy
    {
        $map = [
            Tag::CALLABLE => CallableProcessor::class,
            Tag::INTERPRET => InterpretProcessor::class,
            Tag::ENV => EnvironmentProcessor::class,
            Tag::SUB_CONF => SubConfigsProcessor::class,
            Tag::MERGE => MergeProcessor::class,
            Tag::GET => GetProcessor::class,
            Tag::CONCATENATE => ConcatenateProcessor::class
        ];
        $strategy = $map[$tag];
        return new $strategy();
    }

    /**
     * {@inheritdoc}
     */
    public function process(mixed $value): mixed
    {
        return $this->processStrategy->process($value);
    }

    /**
     * Prepares tag value
     *
     * Goes through values to convert all values
     * into scalar from TaggedValue::class
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws ConfigureException
     */
    public static function prepareValue(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as &$item) {
                if ($item instanceof TaggedValue) {
                    $item = (new TagProcessor($item->getTag()))->process($item->getValue());
                } elseif (is_array($item)) {
                    $item = self::prepareValue($item);
                }
            }
            return $value;
        }
        if ($value instanceof TaggedValue) {
            $value = (new TagProcessor($value->getTag()))->process($value->getValue());
        }
        return $value;
    }
}