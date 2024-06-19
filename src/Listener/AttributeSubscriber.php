<?php

namespace Flagception\Bundle\FlagceptionBundle\Listener;

use Flagception\Bundle\FlagceptionBundle\Attribute\Feature;
use Flagception\Manager\FeatureManagerInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AnnotationSubscriber
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package Flagception\Bundle\FlagceptionBundle\Listener
 */
class AttributeSubscriber implements EventSubscriberInterface
{
    private FeatureManagerInterface $manager;

    public function __construct(
        FeatureManagerInterface $manager,
    ) {
        $this->manager = $manager;
    }

    /**
     * Filter on controller / method
     *
     * @param ControllerEvent $event
     *
     * @return void
     *
     * @throws NotFoundHttpException
     * @throws ReflectionException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $eventController = $event->getController();

        $controller =  is_array($eventController) === false && method_exists($eventController, '__invoke')
            ? [$eventController, '__invoke']
            : $eventController;

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        $object = new ReflectionClass($controller[0]);
        foreach ($object->getAttributes(Feature::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            if (!$this->manager->isActive($attribute->newInstance()->name)) {
                throw new NotFoundHttpException('Feature for this class is not active.');
            }
        }

        $method = $object->getMethod($controller[1]);
        foreach ($method->getAttributes(Feature::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            if (!$this->manager->isActive($attribute->newInstance()->name)) {
                throw new NotFoundHttpException('Feature for this method is not active.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
