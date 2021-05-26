<?php

namespace App\Service\User\Register;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Egulias\EmailValidator\EmailValidator;
use App\Validator\Password\PasswordValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use App\Service\User\Register\Exception\UserExistsException;
use App\Service\User\Register\Exception\InvalidEmailException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterService
{
    private EntityManagerInterface $entityManager;

    private UserPasswordEncoderInterface $passwordEncoder;

    private UserRepository $userRepository;





    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }





    public function registerUser(
        string $email,
        string $password
    ): User {
        $isValid = (new EmailValidator())->isValid(
            $email,
            new RFCValidation()
        );

        if (!$isValid) {
            throw new InvalidEmailException();
        }

        $userCheck = $this->userRepository->findOneBy([
            "email" => $email
        ]);

        if ($userCheck !== null) {
            throw new UserExistsException();
        }

        (new PasswordValidator())->validate($password);

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $password
        ));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
