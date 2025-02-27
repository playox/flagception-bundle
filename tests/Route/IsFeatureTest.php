<?php

namespace Flagception\Tests\FlagceptionBundle\Route;

use Flagception\Bundle\FlagceptionBundle\Listener\RoutingMetadataSubscriber;
use Flagception\Bundle\FlagceptionBundle\Route\IsFeature;
use Flagception\Manager\FeatureManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IsFeatureTest extends TestCase
{
    /**
     * Test feature is not active
     *
     * @return void
     */
    public function testFeatureIsNotActive()
    {
        $manager = $this->createMock(FeatureManagerInterface::class);
        $manager
            ->expects(static::once())
            ->method('isActive')
            ->with('feature_abc')
            ->willReturn(false);

        $isFeature = new IsFeature($manager);
        $this->assertFalse($isFeature->__invoke('feature_abc'));
    }

    /**
     * Test feature is  active
     *
     * @return void
     */
    public function testFeatureIsActive()
    {
        $manager = $this->createMock(FeatureManagerInterface::class);
        $manager
            ->expects(static::once())
            ->method('isActive')
            ->with('feature_abc')
            ->willReturn(true);

        $isFeature = new IsFeature($manager);
        $this->assertTrue($isFeature->__invoke('feature_abc'));
    }
}
