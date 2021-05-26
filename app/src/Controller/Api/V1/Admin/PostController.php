<?php

namespace App\Controller\Api\V1\Admin;

use App\Enum\Date;
use App\Http\ApiResponse;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/admin/posts", methods={"GET"})
     */
    public function index(
        PostRepository $postRepository
    ): Response {
        $posts = array_map(function ($post) {
            return [
                "id" => $post->getId(),
                "title" => $post->getTitle(),
                "short_content" => $post->getShortContent(),
                "content" => $post->getContent(),
                "created_date" => $post->getCreatedDate()->format(Date::FORMAT)
            ];
        }, $postRepository->findBy([], ["createdDate" => "DESC"]));

        return new ApiResponse(
            true,
            "Posts fetched successfully",
            JsonResponse::HTTP_OK,
            [
                "posts" => $posts
            ]
        );
    }
}
