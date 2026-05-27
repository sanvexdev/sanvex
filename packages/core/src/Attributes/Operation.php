<?php

namespace Sanvex\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Operation
{
    public function __construct(
        public string $description = '',
        public bool $readOnly = false,
        public array $schema = [],
        public array $responseFields = [],
    ) {
    }
}
