<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

final class CheckIncomingCookieListnerListener
{
    public function __construct(
        private JWTEncoderInterface $jwtEncoder
    ) {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (
            $request->attributes->get("_route") === 'api_user_profile_create' ||
            $request->attributes->get("_route") === 'api_user_profile_get' ||
            $request->attributes->get("_route") === 'api_review_create' ||
            $request->attributes->get("_route") === 'api_project_create' ||
            $request->attributes->get("_route") === 'api_company_create' ||
            $request->attributes->get("_route") === 'api_project_get_user_projects' ||
            $request->attributes->get("_route") === 'api_project_get'

        ) {
            if($request->cookies->has("JWT_TOKEN")) {
                $jwtToken = $request->cookies->get("JWT_TOKEN");

                try {
                    $this->jwtEncoder->decode($jwtToken);
                } catch (\Exception $e) {
                    $response = new JsonResponse([
                        "message" => "Your token is invalid",
                        "errors" => [$e->getMessage()]
                    ], Response::HTTP_BAD_REQUEST);
                    $response->headers->clearCookie('JWT_TOKEN');
                    $event->setResponse($response);
                }
            } else {
                $response = new JsonResponse(["message" => "Token is nonexistant please login"], Response::HTTP_NOT_FOUND);
                $event->setResponse($response);
            }
        }
    }
}
