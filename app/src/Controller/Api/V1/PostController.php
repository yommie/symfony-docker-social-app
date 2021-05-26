<?php

namespace App\Controller\Api\V1;

use App\Enum\Date;
use App\Entity\PostImage;
use App\Http\ApiResponse;
use App\Service\Post\Post;
use App\Entity\PostComment;
use App\Entity\Post as EntityPost;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Post\Exception\PostParamException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Post\Exception\PostNotFoundException;
use App\Service\Post\Exception\NoPostUpdateParamException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PostController extends AbstractController
{
    /**
     * @Route("/post/{id}", methods={"GET"})
     */
    public function view(
        string $id,
        PostRepository $postRepository
    ): Response {
        try {
            $post = $postRepository->find($id);

            if ($post === null) {
                throw new PostNotFoundException();
            }

            $this->denyAccessUnlessGranted("VIEW_POST", $post);

            $comments = array_map(function (PostComment $comment) {
                return [
                    "user" => $comment->getUser()->getEmail(),
                    "comment" => $comment->getComment(),
                    "created_date" => $comment->getCreatedDate()->format(Date::FORMAT)
                ];
            }, iterator_to_array($post->getPostComments()));

            $images = array_map(function (PostImage $image) {
                return [
                    "path" => $image->getPath(),
                    "created_date" => $image->getCreatedDate()->format(Date::FORMAT)
                ];
            }, iterator_to_array($post->getPostComments()));
        } catch (PostNotFoundException) {
            return new ApiResponse(
                false,
                "Post with id {$id} not found",
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new ApiResponse(
            true,
            "Post fetched successfully",
            JsonResponse::HTTP_OK,
            [
                "id" => $post->getId(),
                "title" => $post->getTitle(),
                "short_content" => $post->getShortContent(),
                "content" => $post->getContent(),
                "images" => $images,
                "comments" => $comments,
                "created_date" => $post->getCreatedDate()->format(Date::FORMAT)
            ]
        );
    }





    /**
     * @Route("/post", methods={"POST"})
     */
    public function create(
        Request $request,
        Post $postService
    ): Response {
        $this->denyAccessUnlessGranted("CREATE_POST", new EntityPost());

        $title = $request->get("title");
        $content = $request->get("content");
        $shortContent = $request->get("short_content");

        if ($title === null || $content === null || $shortContent === null) {
            throw new BadRequestHttpException("'title', 'content' and 'short_content' must be present in request body");
        }

        try {
            $post = $postService->createPost(
                $this->getUser(),
                $title,
                $content,
                $shortContent
            );
        } catch (PostParamException $e) {
            return new ApiResponse(
                false,
                $e->getMessage(),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new ApiResponse(
            true,
            "Post created successfully",
            JsonResponse::HTTP_CREATED,
            [
                "post_id" => $post->getId()
            ]
        );
    }





    /**
     * @Route("/post", methods={"PUT"})
     */
    public function update(
        Request $request,
        Post $postService,
        PostRepository $postRepository
    ): Response {
        $postId = $request->get("post_id");
        $title = $request->get("title");
        $content = $request->get("content");
        $shortContent = $request->get("short_content");

        if ($postId === null) {
            throw new BadRequestHttpException("'post_id' must be present in request body");
        }

        try {
            $post = $postRepository->find($postId);

            if ($post === null) {
                throw new PostNotFoundException();
            }

            $this->denyAccessUnlessGranted("EDIT_POST", $post);

            $postService->updatePost(
                $post,
                $title,
                $content,
                $shortContent
            );
        } catch (PostParamException $e) {
            return new ApiResponse(
                false,
                $e->getMessage(),
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (PostNotFoundException) {
            return new ApiResponse(
                false,
                "Post with id {$postId} not found",
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (NoPostUpdateParamException) {
            return new ApiResponse(
                false,
                "No title, content or short_content provided for update",
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new ApiResponse(
            true,
            "Post updated successfully",
            JsonResponse::HTTP_OK
        );
    }





    /**
     * @Route("/post", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Post $postService,
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

            $this->denyAccessUnlessGranted("DELETE_POST", $post);

            $entityManager->remove($post);
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
            "Post deleted successfully",
            JsonResponse::HTTP_OK
        );
    }
}
