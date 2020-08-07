<?php

namespace App\EventListener;

use App\Entity\Visit;
use App\Services\Support\Str;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;

class VisitListener
{
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var EntityManagerInterface
     */
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

        if ($this->isSupported($request, $response) === false) {
            return;
        }

        $route = $request->attributes->get('_route');
        $user = $this->security->getUser();
        $ipAddress = $request->getClientIp();
        $url = $request->getUri();
        $routeParameters = $request->attributes->get('_route_params');

        $visit = (new Visit)
            ->setUser($user)
            ->setRoute($route)
            ->setRouteParameters($routeParameters)
            ->setUrl($url)
            ->setIpAddress($ipAddress);

        $lastVisit = $this->entityManager->getRepository(Visit::class)->getLastVisit($user, $ipAddress);

        if ($lastVisit->getRoute() !== $route || $lastVisit->getRouteParameters() !== $routeParameters) {
            $this->entityManager->persist($visit);
            $this->entityManager->flush();
        }
    }

    public function isSupported(Request $request, Response $response): bool
    {
        return
            Str::startsWith($request->attributes->get('_route'), 'app') &&
            $response->getStatusCode() === Response::HTTP_OK &&
            $request->getMethod() === 'GET' &&
            !$request->isXmlHttpRequest();
    }
}
