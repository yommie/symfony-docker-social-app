<?php

namespace App\EventSubscriber\Exception;

use App\Http\ApiResponse;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ResponsePhpSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $response = $this->createApiResponse($exception);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
    



    
    /**
     * Creates the ApiResponse from any Exception
     *
     * @param Exception $exception
     *
     * @return ApiResponse
     */
    private function createApiResponse(Exception $exception)
    {
        $getStatusCode = function (Exception $e) {
            if ($e instanceof HttpExceptionInterface) {
                return $e->getStatusCode();
            }

            return Response::HTTP_INTERNAL_SERVER_ERROR;
        };

        return new ApiResponse(
            false,
            $exception->getMessage(),
            $getStatusCode($exception)
        );
    }
}
