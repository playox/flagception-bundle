<?php

namespace Flagception\Bundle\FlagceptionBundle\Route;

use Flagception\Manager\FeatureManagerInterface;
use Flagception\Model\Context;

class IsFeature
{
    private FeatureManagerInterface $featureManager;

    public function __construct(
        FeatureManagerInterface $featureManager,
    ) {
        $this->featureManager = $featureManager;
    }

    public function __invoke($feature, ?Context $context = null): bool
    {
        return $this->featureManager->isActive($feature, $context);
    }
}
