<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use App\Enum\Role;
use App\Service\SocialInteraction\Follow;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PostVoter extends Voter
{
    private const CREATE = "CREATE_POST";
    private const VIEW = "VIEW_POST";
    private const EDIT = "EDIT_POST";
    private const DELETE = "DELETE_POST";
    private const COMMENT = "COMMENT_ON_POST";
    private const PUBLISH = "PUBLISH_POST";
    private const UNPUBLISH = "UNPUBLISH_POST";

    private const SUPPORTED = [
        self::CREATE,
        self::VIEW,
        self::EDIT,
        self::DELETE,
        self::COMMENT,
        self::PUBLISH,
        self::UNPUBLISH
    ];

    private Security $security;

    private Follow $followService;





    public function __construct(
        Security $security,
        Follow $followService
    ) {
        $this->security = $security;
        $this->followService = $followService;
    }





    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, self::SUPPORTED) && $subject instanceof Post;
    }





    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate();
            case self::VIEW:
                return $this->canView($subject, $user);
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
            case self::COMMENT:
                return $this->canComment($subject, $user);
            case self::PUBLISH:
                return $this->canPublishArticle($user);
            case self::UNPUBLISH:
                return $this->canUnpublishArticle($user);
        }

        return false;
    }





    private function canCreate()
    {
        return $this->security->isGranted(Role::USER);
    }





    private function canView(Post $post, User $user)
    {
        if ($this->security->isGranted(Role::ADMIN)) {
            return true;
        }

        if ($post->getUser() === $user) {
            return true;
        }

        return $post->getIsPublished();
    }





    private function canEdit(Post $post, User $user)
    {
        if ($this->security->isGranted(Role::ADMIN)) {
            return true;
        }

        return $post->getUser() === $user;
    }





    private function canDelete(Post $post, User $user)
    {
        if ($this->security->isGranted(Role::ADMIN)) {
            return true;
        }

        return $post->getUser() === $user;
    }





    private function canComment(Post $post, User $user)
    {
        if ($this->security->isGranted(Role::ADMIN)) {
            return true;
        }

        if ($post->getUser() === $user) {
            return true;
        }

        return $this->followService->isFollowing(
            $post->getUser(),
            $user
        );
    }




    
    private function canPublishArticle()
    {
        return $this->security->isGranted(Role::ADMIN);
    }




    
    private function canUnpublishArticle()
    {
        return $this->security->isGranted(Role::ADMIN);
    }
}
