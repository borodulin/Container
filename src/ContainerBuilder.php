<?php

declare(strict_types=1);

namespace Borodulin\Container;

use Borodulin\Container\Autowire\ClassNameExtractor;
use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\Autowire\Item\AliasItem;
use Borodulin\Container\Autowire\Item\CallableItem;
use Borodulin\Container\Autowire\Item\ClassItem;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class ContainerBuilder
{
    /**
     * @var FileFinder
     */
    private $fileFinder;
    /**
     * @var CacheInterface|null
     */
    private $cache;
    /**
     * @var iterable
     */
    private $config;
    /**
     * @var ClassNameExtractor
     */
    private $classNameExtractor;

    public function __construct(
        FileFinder $fileFinder,
        iterable $config = [],
        CacheInterface $cache = null
    ) {
        $this->fileFinder = $fileFinder;
        $this->cache = $cache;
        $this->config = $config;
        $this->classNameExtractor = new ClassNameExtractor();
    }

    /**
     * @throws ContainerException
     * @throws InvalidArgumentException
     */
    public function build(): ContainerInterface
    {
        if ($this->cache && $this->cache->has(static::class)) {
            $compilerItems = unserialize($this->cache->get(static::class));
        } else {
            $compilerItems = [];

            foreach ($this->config as $id => $item) {
                if (\is_string($item)) {
                    $compilerItems[$id] = new AliasItem($id, $item);
                } elseif (\is_callable($item)) {
                    $compilerItems[$id] = new CallableItem($id, $item);
                } else {
                    throw new ContainerException('Unsupported item type');
                }
            }

            foreach ($this->fileFinder as $fileName) {
                $className = $this->classNameExtractor->extract($fileName);
                if (null !== $className) {
                    if (!isset($compilerItems[$className])) {
                        $compilerItems[$className] = new ClassItem($className);
                    }
                }
            }
        }

        return new Container($compilerItems);
    }
}
