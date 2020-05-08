<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\ContainerException;

class ClassNameExtractor
{
    public function extract($filename): ?string
    {
        $tokens = token_get_all(file_get_contents($filename));

        $namespace = '';

        $token = current($tokens);
        while (false !== $token) {
            if ($this->isToken($token, T_NAMESPACE)) {
                $namespace = $this->extractNamespace($tokens);
            }

            if ($this->isToken($token, T_CLASS)) {
                $className = $this->extractClassName($tokens);

                return $namespace ? "$namespace\\$className" : $className;
            }
            $token = next($tokens);
        }

        return null;
    }

    private function isToken($token, int $tokenType): bool
    {
        return \is_array($token) && $token[0] === $tokenType;
    }

    private function nextToken(array &$tokens, int $tokenType): string
    {
        $token = next($tokens);
        if (($token[0] ?? null) === $tokenType) {
            return $token[1];
        }
        throw new ContainerException('Parse error. Expected '.token_name($tokenType));
    }

    private function isNextToken(array &$tokens, int $tokenType): bool
    {
        $token = next($tokens);

        return $this->isToken($token, $tokenType);
    }

    private function extractNamespace(array &$tokens): string
    {
        $this->nextToken($tokens, T_WHITESPACE);
        $namespace = $this->nextToken($tokens, T_STRING);
        while ($this->isNextToken($tokens, T_NS_SEPARATOR)) {
            $namespace .= '\\'.$this->nextToken($tokens, T_STRING);
        }

        return $namespace;
    }

    private function extractClassName(array &$tokens): string
    {
        $this->nextToken($tokens, T_WHITESPACE);

        return $this->nextToken($tokens, T_STRING);
    }
}
