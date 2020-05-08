<?php

declare(strict_types=1);

namespace Borodulin\Container;

use Borodulin\Container\Autowire\ClassNameExtractor;
use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\Autowire\Item\AliasItem;
use Borodulin\Container\Autowire\Item\CallableItem;
use Borodulin\Container\Autowire\Item\ClassItem;
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
    private $config = [];
    /**
     * @var ClassNameExtractor
     */
    private $classNameExtractor;

    public function __construct()
    {
        $this->classNameExtractor = new ClassNameExtractor();
    }

    /**
     * @throws ContainerException
     * @throws InvalidArgumentException
     */
    public function build(): Container
    {
        if ($this->cache && $this->cache->has(static::class)) {
            $compilerItems = unserialize($this->cache->get(static::class));
        } else {
            $compilerItems = [];

            $this->buildConfig($compilerItems);

            $this->buildFiles($compilerItems);

            if ($this->cache) {
                $this->cache->set(static::class, serialize($compilerItems));
            }
        }

        return new Container($compilerItems);
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

    public function setFileFinder(FileFinder $fileFinder): self
    {
        $this->fileFinder = $fileFinder;

        return $this;
    }

    private function buildConfig(array &$compilerItems): void
    {
        foreach ($this->config as $id => $item) {
            if (\is_string($item)) {
                $id = \is_int($id) ? $item : $id;
                $compilerItems[$id] = new AliasItem($id, $item);
            } elseif (\is_callable($item)) {
                $id = (string) $id;
                $compilerItems[$id] = new CallableItem($id, $item);
            } else {
                throw new ContainerException('Unsupported item type');
            }
        }
    }

    private function buildFiles(array &$compilerItems): void
    {
        if (null !== $this->fileFinder) {
            foreach ($this->fileFinder as $fileName) {
                $className = $this->classNameExtractor->extract($fileName);
                if (null !== $className) {
                    if (!isset($compilerItems[$className])) {
                        $compilerItems[$className] = new ClassItem($className);
                    }
                }
            }
        }

    }
}
