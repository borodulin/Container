<?php

declare(strict_types=1);

namespace Borodulin\Container;

use Borodulin\Container\Autowire\ClassNameExtractor;
use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\Autowire\Item\AliasItem;
use Borodulin\Container\Autowire\Item\CallableItem;
use Borodulin\Container\Autowire\Item\ClassItem;
use Psr\SimpleCache\CacheInterface;

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
    /**
     * @var int
     */
    private static $versionId = 0;

    public function __construct()
    {
        ++self::$versionId;
        $this->classNameExtractor = new ClassNameExtractor();
    }

    public function build(): Container
    {
        $cacheKey = static::class.self::$versionId;
        if ($this->cache && $this->cache->has($cacheKey)) {
            $compilerItems = unserialize($this->cache->get(static::class));
        } else {
            $compilerItems = [];

            $this->buildConfig($compilerItems);

            $this->buildFiles($compilerItems);

            if ($this->cache) {
                $this->cache->set($cacheKey, serialize($compilerItems));
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
                $compilerItems[$id] = new AliasItem($item);
            } elseif (\is_callable($item)) {
                $id = (string) $id;
                $compilerItems[$id] = new CallableItem($item);
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
