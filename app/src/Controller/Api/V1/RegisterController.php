<?php

namespace App\Controller\Api\V1;

use App\Http\ApiResponse;
use App\Service\Register\RegisterService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Validator\Password\PasswordStrengthException;
use App\Service\Register\Exception\UserExistsException;
use App\Service\Register\Exception\InvalidEmailException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register")
     */
    public function index(
        Request $request,
        RegisterService $registerService
    ): Response {
        $email = $request->get("email");
        $password = $request->get("password");


        try {
            $registerService->registerUser(
                $email,
                $password
            );
        } catch (InvalidEmailException) {
            return new ApiResponse(
                false,
                "The email '{$email}' is not a valid email address",
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (UserExistsException) {
            return new ApiResponse(
                false,
                "The user '{$email}' already exists on the system",
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (PasswordStrengthException $e) {
            return new ApiResponse(
                false,
                "Password does not meet certain criteria",
                JsonResponse::HTTP_BAD_REQUEST,
                [],
                $e->passwordErrors
            );
        }

        return new ApiResponse(
            true,
            "Registration successful",
            JsonResponse::HTTP_CREATED
        );
    }
}
