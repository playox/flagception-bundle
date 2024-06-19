<?php

namespace Flagception\Bundle\FlagceptionBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Feature
{
    public string $name;

    public function __construct(
        string $name,
    ) {
        $this->name = $name;
    }
}
