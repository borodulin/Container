<?php

declare(strict_types=1);

namespace Borodulin\Container;

use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class NotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{
    public function __construct(string $id, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("$id is not found.", $code, $previous);
    }
}
