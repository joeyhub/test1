<?php

namespace App\EventSubscriber;

use App\Controller\AuthenticationController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Service\AuthenticationService;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    private $service;

    public function __construct(AuthenticationService $service)
    {
        $this->service = $service;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::CONTROLLER => 'onKernelController'];
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        // Note: This is symfony being weird, and probably a security bipass if a programmer uses a closure.
        if (!is_array($controller)) {
            return;
        }

        // Note: This would be better off using an interface and possibly the reverse logic.
        // Note: I don't know why this can't be instance of Closure or something on Symfony's side.
        if ($controller[0] instanceof AuthenticationController) {
            return;
        }

        $this->service->validate($event->getRequest());
    }
}
