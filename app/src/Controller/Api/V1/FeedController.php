<?php

namespace App\Controller\Api\V1;

use App\Enum\Date;
use App\Entity\Followers;
use App\Http\ApiResponse;
use App\Repository\PostRepository;
use App\Repository\FollowersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FeedController extends AbstractController
{
    /**
     * @Route("/feed", methods={"GET"})
     */
    public function index(
        FollowersRepository $followersRepository,
        PostRepository $postRepository
    ): Response {
        $user = $this->getUser();

        $followingIds = array_map(function (Followers $entry) {
            return $entry->getUser()->getId();
        }, $followersRepository->findBy([
            "follower" => $user
        ]));

        // Add logged in user to following Ids so self posts
        // would appear in feed
        $followingIds[] = $user->getId();

        $feedPosts = array_map(function ($post) use ($user) {
            $authorEmail = $post->getUser()->getEmail();

            return [
                "id" => $post->getId(),
                "author" => $authorEmail === $user->getEmail() ? "You" : $authorEmail,
                "title" => $post->getTitle(),
                "short_content" => $post->getShortContent(),
                "content" => $post->getContent(),
                "created_date" => $post->getCreatedDate()->format(Date::FORMAT)
            ];
        }, $postRepository->getUserFeed($followingIds));

        return new ApiResponse(
            true,
            "Feed fetched successfully",
            JsonResponse::HTTP_OK,
            [
                "posts" => $feedPosts
            ]
        );
    }
}
