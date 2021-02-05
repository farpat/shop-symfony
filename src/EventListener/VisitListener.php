<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Visit;
use App\Services\Support\Str;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;

class VisitListener
{
    private const WHITE_ROUTES = [
        'app_front_category',
        'app_front_product',
        'app_front_home',
        'app_front_purchase_purchase'
    ];
    private Security               $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$this->isSupported($request, $response)) {
            return;
        }

        $route = $request->attributes->get('_route');
        /** @var User $user */
        $user = $this->security->getUser();
        $ipAddress = $request->getClientIp();
        $routeParameters = $request->attributes->get('_route_params');

        $lastVisit = $this->entityManager->getRepository(Visit::class)->getLastVisit($user, $ipAddress);

        if ($lastVisit === null || ($lastVisit->getRoute() !== $route || $lastVisit->getRouteParameters() !== $routeParameters)) {
            $this->entityManager->persist((new Visit)
                ->setUser($user)
                ->setRoute($route)
                ->setRouteParameters($routeParameters)
                ->setUrl($request->getUri())
                ->setIpAddress($ipAddress));
            $this->entityManager->flush();
        }
    }

    public function isSupported(Request $request, Response $response): bool
    {
        $route = $request->attributes->get('_route');

        return
            $route &&
            Str::startsWith($route, self::WHITE_ROUTES) &&
            $response->getStatusCode() === Response::HTTP_OK &&
            $request->getMethod() === 'GET' &&
            !$request->isXmlHttpRequest();
    }
}
