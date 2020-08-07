<?php

namespace App\EventListener;

use App\Entity\Visit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
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

        if ($response->getStatusCode() === Response::HTTP_OK && $request->getMethod() === 'GET' && !$request->isXmlHttpRequest()) {
            $visit = (new Visit)
                ->setUser($this->security->getUser())
                ->setRoute($request->attributes->get('_route'))
                ->setRouteParameters($request->attributes->get('_route_params'))
                ->setUrl($request->getUri())
                ->setIpAddress($request->getClientIp());

            $this->entityManager->persist($visit);
            $this->entityManager->flush();
        }
    }
}
