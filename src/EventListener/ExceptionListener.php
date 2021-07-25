<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionListener
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = $exception->getStatusCode();
        $content = [
            "code"    => $statusCode,
            "message" => $exception->getMessage(),
        ];

        $response = new Response(
            $this->serializer->serialize($content, 'json'),
            $statusCode,
            ['Content-Type' => 'application/json']
        );

        $event->setResponse($response);
    }
}
