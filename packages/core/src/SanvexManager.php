<?php

namespace Sanvex\Core;

class SanvexManager
{
    public function __construct(private array $config = [])
    {
    }

    public static function make(array $config = []): self
    {
        return new self($config);
    }

    public function config(): array
    {
        return $this->config;
    }
}
