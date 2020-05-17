<?php

declare(strict_types=1);

namespace Borodulin\Container;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends \RuntimeException implements ContainerExceptionInterface
{
}
