<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimiterSubscriber implements EventSubscriberInterface
{

    /**
     * @var RateLimiterFactory
     */
    private RateLimiterFactory $anonymousAppLimiter;

    /*
     * @var array
     */
    private array $exclude = [];

    /**
     *
     * @param RateLimiterFactory $anonymousAppLimiter
     */
    public function __construct(RateLimiterFactory $anonymousAppLimiter)
    {
        $this->anonymousAppLimiter = $anonymousAppLimiter;
    }

    /**
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    /**
     *
     * @param RequestEvent $event
     * @return void
     * @throws TooManyRequestsHttpException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        // if (strpos($request->get("_route"), 'app_') !== false) {
        if ($request->isMethod('POST')) {
            $limiter = $this->anonymousAppLimiter->create($request->getClientIp());
            if (false === $limiter->consume(1)->isAccepted()) {
                throw new TooManyRequestsHttpException();
            }
        }
    }
}
