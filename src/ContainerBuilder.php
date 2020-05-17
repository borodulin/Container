<?php

declare(strict_types=1);

namespace Borodulin\Container;

use Borodulin\Container\Autowire\CallableItemBuilder;
use Borodulin\Container\Autowire\ClassItemBuilder;
use Borodulin\Container\Autowire\Item\AliasItem;
use Borodulin\Container\Autowire\Item\VariadicItem;
use Borodulin\Container\Autowire\ItemProvider;
use Borodulin\Finder\FinderInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

class ContainerBuilder
{
    /**
     * @var int
     */
    private static $builderId = 0;
    /**
     * @var FinderInterface
     */
    private $classFinder;
    /**
     * @var CacheInterface|null
     */
    private $cache;
    /**
     * @var iterable
     */
    private $config = [];
    /**
     * @var int
     */
    private $id;

    public function __construct()
    {
        $this->id = ++self::$builderId;
    }

    public function build(): Container
    {
        $cacheKey = static::class.':'.$this->id;

        if ($this->cache && $this->cache->has($cacheKey)) {
            $itemProvider = unserialize($this->cache->get(static::class));
        } else {
            $itemProvider = new ItemProvider();
            $itemProvider->addItem(ContainerInterface::class, new AliasItem(ContainerInterface::class));

            $this->buildConfig($itemProvider);

            $this->buildFiles($itemProvider);

            $this->buildVariadicArgs($itemProvider);

            if ($this->cache) {
                $this->cache->set($cacheKey, serialize($itemProvider));
            }
        }

        return new Container(iterator_to_array($itemProvider));
    }

    public function setConfig(iterable $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function setCache(?CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function setClassFinder(FinderInterface $classFinder): self
    {
        $this->classFinder = $classFinder;

        return $this;
    }

    private function buildConfig(ItemProvider $itemProvider): void
    {
        foreach ($this->config as $id => $item) {
            if (\is_string($item)) {
                if (\is_int($id)) {
                    $itemProvider->addItem($item, (new ClassItemBuilder($itemProvider))->build($item));
                } else {
                    if ($itemProvider->hasItem($item)) {
                        $itemProvider->addItem($id, new AliasItem($item));
                    } else {
                        $itemProvider->addItem($id, new AliasItem($item, (new ClassItemBuilder($itemProvider))->build($item)));
                    }
                }
            } elseif (\is_callable($item)) {
                $id = (string) $id;
                $itemProvider->addItem($id, (new CallableItemBuilder($itemProvider))->build($item));
            } else {
                throw new ContainerException('Unsupported item type');
            }
        }
    }

    private function buildFiles(ItemProvider $itemProvider): void
    {
        if (null !== $this->classFinder) {
            foreach ($this->classFinder as $className) {
                if (!$itemProvider->hasItem($className)) {
                    $itemProvider->addItem($className, (new ClassItemBuilder($itemProvider))->build($className));
                }
            }
        }
    }

    private function buildVariadicArgs(ItemProvider $itemProvider): void
    {
        /** @var VariadicItem[] $items */
        foreach ($itemProvider->getVariadicPass()->getItems() as $interface => $items) {
            $classItems = [];
            foreach ($itemProvider->findInstanceOf($interface) as $className) {
                $classItems[] = (new ClassItemBuilder($itemProvider))->build($className);
            }
            foreach ($items as $variadicItem) {
                $variadicItem->setArgs($classItems);
            }
        }
    }
}
