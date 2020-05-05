<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

class FileFinder implements \IteratorAggregate
{
    private $paths = [];

    public function in(string $path): self
    {
        $this->paths[] = $path;

        return $this;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->paths as $path) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

            /** @var \SplFileInfo $file */
            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    continue;
                }
                if (false !== $file->getRealPath()) {
                    yield $file->getRealPath();
                }
            }
        }
    }
}
