<?php

namespace App\EventSubscriber\Authentication;

use App\Http\ApiResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;

class FailureSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $exceptionMessage = $event->getException()->getMessage();

        $response = new ApiResponse(
            false,
            $exceptionMessage,
            JsonResponse::HTTP_UNAUTHORIZED
        );

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_failure' => 'onLexikJwtAuthenticationOnAuthenticationFailure',
        ];
    }
}
