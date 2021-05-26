<?php

namespace App\Controller\Api\V1\Admin;

use App\Enum\Date;
use App\Http\ApiResponse;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Post\Exception\PostNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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





    /**
     * @Route("/admin/post/publish", methods={"PATCH"})
     */
    public function publish(
        Request $request,
        PostRepository $postRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $postId = $request->get("post_id");

        if ($postId === null) {
            throw new BadRequestHttpException("'post_id' must be present in request body");
        }

        try {
            $post = $postRepository->find($postId);

            if ($post === null) {
                throw new PostNotFoundException();
            }

            $this->denyAccessUnlessGranted("PUBLISH_POST", $post);

            $post->setIsPublished(true);

            $entityManager->flush();
        } catch (PostNotFoundException) {
            return new ApiResponse(
                false,
                "Post with id {$postId} not found",
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new ApiResponse(
            true,
            "Post published successfully",
            JsonResponse::HTTP_OK
        );
    }





    /**
     * @Route("/admin/post/unpublish", methods={"PATCH"})
     */
    public function unpublish(
        Request $request,
        PostRepository $postRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $postId = $request->get("post_id");

        if ($postId === null) {
            throw new BadRequestHttpException("'post_id' must be present in request body");
        }

        try {
            $post = $postRepository->find($postId);

            if ($post === null) {
                throw new PostNotFoundException();
            }

            $this->denyAccessUnlessGranted("UNPUBLISH_POST", $post);

            $post->setIsPublished(false);

            $entityManager->flush();
        } catch (PostNotFoundException) {
            return new ApiResponse(
                false,
                "Post with id {$postId} not found",
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new ApiResponse(
            true,
            "Post unpublished successfully",
            JsonResponse::HTTP_OK
        );
    }
}
