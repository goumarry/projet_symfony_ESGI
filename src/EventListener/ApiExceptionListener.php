<?php


namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
class ApiExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $message = $exception->getMessage();

        if ($statusCode === 500) {
            $message = "Une erreur interne est survenue. Veuillez contacter le support.";
        }

        $data = [
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message
        ];

        // 6. Remplacer la réponse par défaut de Symfony
        $response = new JsonResponse($data, $statusCode);
        $event->setResponse($response);
    }
}