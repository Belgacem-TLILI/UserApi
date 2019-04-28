<?php

namespace Belga\EventListener;

use Belga\Security\ApiKeyAuthenticator;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    /**
     * @var ApiKeyAuthenticator
     */
    private $apiKeyAuthenticator;

    public function __construct($apiKeyAuthenticator)
    {
        $this->apiKeyAuthenticator = $apiKeyAuthenticator;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // authorize only the first request from the user, ignore internal forwards
            return;
        }

        $request = $event->getRequest();

        $this->apiKeyAuthenticator->authenticate($request);
    }
}