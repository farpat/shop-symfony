<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Event\RequestEvent;
use function is_array;
use function json_decode;
use function json_last_error;
use const JSON_ERROR_NONE;

class RequestTransformerListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (false === $this->isAvailable($request)) {
            return;
        }

        if (false === $this->transform($request)) {
            $response = Response::create('Unable to parse request.', 400);

            $event->setResponse($response);
        }
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isAvailable(Request $request): bool
    {
        return 'json' === $request->getContentType() && $request->getContent();
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function transform(Request $request): bool
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if (is_array($data)) {
            $request->request->replace($data);
        }

        return true;
    }
}
