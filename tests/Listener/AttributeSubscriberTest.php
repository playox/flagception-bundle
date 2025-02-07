<?php

namespace Flagception\Tests\FlagceptionBundle\Listener;

use Flagception\Bundle\FlagceptionBundle\Listener\AttributeSubscriber;
use Flagception\Manager\FeatureManagerInterface;
use Flagception\Tests\FlagceptionBundle\Fixtures\Helper\AttributeTestClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AttributeSubscriberTest
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package Flagception\Tests\FlagceptionBundle\Listener
 */
class AttributeSubscriberTest extends TestCase
{
    /**
     * Test implement interface
     *
     * @return void
     */
    public function testImplementInterface()
    {
        $manager = $this->createMock(FeatureManagerInterface::class);
        $subscriber = new AttributeSubscriber($manager);

        static::assertInstanceOf(EventSubscriberInterface::class, $subscriber);
    }

    /**
     * Test subscribed events
     *
     * @return void
     */
    public function testSubscribedEvents()
    {
        static::assertEquals(
            [KernelEvents::CONTROLLER => 'onKernelController',],
            AttributeSubscriber::getSubscribedEvents()
        );
    }

    /**
     * Test controller is closure
     *
     * @return void
     */
    public function testControllerIsClosure()
    {
        $manager = $this->createMock(FeatureManagerInterface::class);
        $manager->expects(static::never())->method('isActive');

        $event = $this->createControllerEvent(
            function () {
            }
        );

        $subscriber = new AttributeSubscriber($manager);
        $subscriber->onKernelController($event);
    }

    /**
     * Test on class with active feature
     *
     * @return void
     */
    public function testOnClassIsActive()
    {
        $manager = $this->createMock(FeatureManagerInterface::class);
        $manager->method('isActive')->with('feature_abc')->willReturn(true);

        $event = $this->createControllerEvent([
            new AttributeTestClass(),
            'normalMethod'
        ]);

        $subscriber = new AttributeSubscriber($manager);
        $subscriber->onKernelController($event);
    }

    /**
     * Test on class with inactive feature
     *
     * @return void
     */
    public function testOnClassIsInactive()
    {
        $this->expectException(NotFoundHttpException::class);

        $manager = $this->createMock(FeatureManagerInterface::class);
        $manager->method('isActive')->with('feature_abc')->willReturn(false);

        $event = $this->createControllerEvent([
            new AttributeTestClass(),
            'normalMethod'
        ]);

        $subscriber = new AttributeSubscriber($manager);
        $subscriber->onKernelController($event);
    }

    /**
     * Test on method with active feature
     *
     * @return void
     */
    public function testOnMethodIsActive()
    {
        $manager = $this->createMock(FeatureManagerInterface::class);
        $manager
            ->method('isActive')
            ->withConsecutive(['feature_abc'], ['feature_def'])
            ->willReturnOnConsecutiveCalls(true, true);

        $event = $this->createControllerEvent([
            new AttributeTestClass(),
            'invalidMethod'
        ]);

        $subscriber = new AttributeSubscriber($manager);
        $subscriber->onKernelController($event);
    }

    /**
     * Test on method with inactive feature
     *
     * @return void
     */
    public function testOnMethodIsInactive()
    {
        $this->expectException(NotFoundHttpException::class);

        $manager = $this->createMock(FeatureManagerInterface::class);
        $manager
            ->method('isActive')
            ->withConsecutive(['feature_abc'], ['feature_def'])
            ->willReturnOnConsecutiveCalls(true, false);

        $event = $this->createControllerEvent([
            new AttributeTestClass(),
            'invalidMethod'
        ]);

        $subscriber = new AttributeSubscriber($manager);
        $subscriber->onKernelController($event);
    }

    /**
     * Create ControllerEvent
     *
     * @param $controller
     *
     * @return ControllerEvent
     */
    private function createControllerEvent($controller): ControllerEvent
    {
        return new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST
        );
    }
}
