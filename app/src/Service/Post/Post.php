<?php

namespace App\Service\Post;

use App\Entity\User;
use App\Entity\Post as EntityPost;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Post\Exception\PostParamException;
use App\Service\Post\Exception\NoPostUpdateParamException;
use DateTime;

class Post
{
    private EntityManagerInterface $entityManager;

    private PostRepository $postRepository;





    public function __construct(
        EntityManagerInterface $entityManager,
        PostRepository $postRepository
    ) {
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
    }





    public function createPost(
        User $owner,
        string $title,
        string $content,
        string $shortContent
    ): EntityPost {
        // A separate method should be created for these checks for reuse in other methods
        // but time constraints...
        if (strlen($title) > 255 || strlen($title) < 8) {
            throw new PostParamException("Post title must be at least 8 characters and not more than 255 characters");
        }

        if (strlen($shortContent) > 255 || strlen($shortContent) < 8) {
            throw new PostParamException("
                Post short content must be at least 8 characters and not more than 255 characters
            ");
        }

        if (strlen($content) < 8) {
            throw new PostParamException("
                Post content must be at least 8 characters
            ");
        }

        $post = new EntityPost();
        $post->setUser($owner);
        $post->setTitle($title);
        $post->setContent($content);
        $post->setShortContent($shortContent);
        $post->setIsPublished(false);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }





    public function updatePost(
        EntityPost $post,
        string $title = null,
        string $content = null,
        string $shortContent = null
    ): EntityPost {
        if ($title === null && $content === null && $shortContent === null) {
            throw new NoPostUpdateParamException();
        }

        // A separate method should be created for these checks for reuse in other methods
        // but time constraints...
        if ($title !== null) {
            if (strlen($title) > 255 || strlen($title) < 8) {
                throw new PostParamException("
                    Post title must be at least 8 characters and not more than 255 characters
                ");
            }

            $post->setTitle($title);
        }

        if ($shortContent !== null) {
            if (strlen($shortContent) > 255 || strlen($shortContent) < 8) {
                throw new PostParamException("
                    Post short content must be at least 8 characters and not more than 255 characters
                ");
            }

            $post->setShortContent($shortContent);
        }

        if ($content !== null) {
            if (strlen($content) < 8) {
                throw new PostParamException("
                    Post content must be at least 8 characters
                ");
            }

            $post->setContent($content);
        }

        $post->setUpdatedDate(new DateTime());

        $this->entityManager->flush();

        return $post;
    }
}
