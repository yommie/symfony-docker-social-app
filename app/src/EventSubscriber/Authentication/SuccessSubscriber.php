<?php

namespace App\EventSubscriber\Authentication;

use App\Http\ApiResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class SuccessSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $event->setData(ApiResponse::format(
            true,
            "Authentication successful",
            JsonResponse::HTTP_OK,
            $event->getData(),
            [],
        ));
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onLexikJwtAuthenticationOnAuthenticationSuccess',
        ];
    }
}
