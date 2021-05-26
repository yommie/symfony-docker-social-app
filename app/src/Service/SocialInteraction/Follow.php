<?php

namespace App\Service\SocialInteraction;

use App\Entity\User;
use App\Entity\Followers;
use App\Repository\FollowersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SocialInteraction\Exception\SameUserException;
use App\Service\SocialInteraction\Exception\NotFollowingException;
use App\Service\SocialInteraction\Exception\AlreadyFollowingException;

class Follow
{
    private EntityManagerInterface $entityManager;

    private FollowersRepository $followersRepository;





    public function __construct(
        EntityManagerInterface $entityManager,
        FollowersRepository $followersRepository
    ) {
        $this->entityManager = $entityManager;
        $this->followersRepository = $followersRepository;
    }





    public function followUser(
        User $user,
        User $follower
    ): void {
        $followersCheck = $this->followersRepository->findOneBy([
            "user" => $user,
            "follower" => $follower
        ]);

        if ($followersCheck !== null) {
            throw new AlreadyFollowingException();
        }

        if ($user->getId() === $follower->getId()) {
            throw new SameUserException();
        }

        $follow = new Followers();
        $follow->setUser($user);
        $follow->setFollower($follower);

        $this->entityManager->persist($follow);
        $this->entityManager->flush();
    }





    public function unfollowUser(
        User $user,
        User $follower
    ): void {
        $following = $this->followersRepository->findOneBy([
            "user" => $user,
            "follower" => $follower
        ]);

        if ($following === null) {
            throw new NotFollowingException();
        }

        $this->entityManager->remove($following);
        $this->entityManager->flush();
    }





    public function isFollowing(
        User $user,
        User $follower
    ): bool {
        $followersCheck = $this->followersRepository->findOneBy([
            "user" => $user,
            "follower" => $follower
        ]);

        return $followersCheck !== null;
    }
}
