<?php

namespace App\Controller\Api\V1;

use App\Http\ApiResponse;
use App\Repository\UserRepository;
use App\Service\SocialInteraction\Follow;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\SocialInteraction\Exception\SameUserException;
use App\Service\User\Register\Exception\UserNotExistsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\SocialInteraction\Exception\AlreadyFollowingException;
use App\Service\SocialInteraction\Exception\NotFollowingException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FollowController extends AbstractController
{
    /**
     * @Route("/follow", methods={"POST"})
     */
    public function follow(
        Request $request,
        UserRepository $userRepository,
        Follow $followService
    ): Response {
        $email = $request->get("email");

        if ($email === null) {
            throw new BadRequestHttpException("'email' missing from request body");
        }

        $follower = $this->getUser();

        try {
            $user = $userRepository->findOneBy([
                "email" => $email
            ]);

            if ($user === null) {
                throw new UserNotExistsException();
            }

            $followService->followUser(
                $user,
                $follower
            );
        } catch (AlreadyFollowingException) {
            return new ApiResponse(
                false,
                "You are already following this user",
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (SameUserException) {
            return new ApiResponse(
                false,
                "You cannot follow yourself",
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (UserNotExistsException) {
            return new ApiResponse(
                false,
                "User {$email} not found",
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new ApiResponse(
            true,
            "{$email} followed successfully",
            JsonResponse::HTTP_CREATED
        );
    }





    /**
     * @Route("/unfollow", methods={"POST"})
     */
    public function unfollow(
        Request $request,
        UserRepository $userRepository,
        Follow $followService
    ): Response {
        $email = $request->get("email");
        $follower = $this->getUser();

        try {
            $user = $userRepository->findOneBy([
                "email" => $email
            ]);

            if ($user === null) {
                throw new UserNotExistsException();
            }

            $followService->unfollowUser(
                $user,
                $follower
            );
        } catch (NotFollowingException) {
            return new ApiResponse(
                false,
                "You are not following this user",
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (UserNotExistsException) {
            return new ApiResponse(
                false,
                "User {$email} not found",
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new ApiResponse(
            true,
            "{$email} unfollowed successfully",
            JsonResponse::HTTP_CREATED
        );
    }
}
