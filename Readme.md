# 🐳 Docker + PHP 7.4 + MySQL 8 + Nginx + Symfony 4.4 Simple Social App

## Description

This is a simplified social networking app running on Symfony 4.4 in Docker containers using docker-compose tool.

It is composed by 3 containers:

- `nginx`, acting as the webserver.
- `php`, the PHP-FPM container with the 7.4 PHPversion.
- `db` which is the MySQL database container with a **MySQL 8.0** image.

## Installation

1. 😀 Clone this rep.

2. Create a `.env` file at the root of the project and copy the contents of `env.dist` into it.

3. `Optional:` You can change the ports in the `.env` file if any clashes with your local machine

4. Run `docker-compose up -d`

5. The 3 containers are deployed:

```bash
Creating symfony-docker-social-app_db_1    ... done
Creating symfony-docker-social-app_php_1   ... done
Creating symfony-docker-social-app_nginx_1 ... done
```

## Functionalities

### Login

Route: `/api/v1/login`
Method: `POST`

Request Body:

```json
{
    "username": "your@email.com",
    "password": "yourpassword"
}
```

> Also sets JWT as an HTTP Cookie

### Register

Route: `/api/v1/register`
Method: `POST`

Request Body:

```json
{
    "email": "your@email.com",
    "password": "yourpassword"
}
```

### Refresh Token

Route: `/api/v1/token/refresh`
Method: `POST`

Request Body:

```json
{
    "refresh_token": "refresh-token-goes-here"
}
```

> Also sets JWT as an HTTP Cookie

### Create Post

Route: `/api/v1/post`
Method: `POST`

Request Body:

```json
{
    "title": "title-goes-here",
    "content": "content-goes-here",
    "short_content": "short-content-goes-here"
}
```

### Edit Post

Route: `/api/v1/post`
Method: `PUT`

Request Body:

```json
{
    "title": "title-goes-here",
    "content": "content-goes-here",
    "short_content": "short-content-goes-here"
}
```

> Parameters `title`, `content`, `short_content` are all optional. However, one of the parameters must be present in the request body

### Delete Post

Route: `/api/v1/post`
Method: `DELETE`

Request Body:

```json
{
    "post_id": "post-id-goes-here"
}
```

### View Post

Route: `/api/v1/post/:postId`
Method: `GET`

> `postId` is the `id` of a created `Post`

### Follow User

Route: `/api/v1/follow`
Method: `POST`

Request Body:

```json
{
    "email": "user-email-goes-here"
}
```

### Unfollow User

Route: `/api/v1/unfollow`
Method: `POST`

Request Body:

```json
{
    "email": "user-email-goes-here"
}
```

### Posts Feed

Route: `/api/v1/feed`
Method: `GET`

> Returns a feed of all followed users posts. Only published posts are returned here.

### Admin Posts

Route: `/api/v1/admin/posts`
Method: `GET`

> Returns all posts in the application for the admin.

### Admin Publish Post

Route: `/api/v1/admin/post/publish`
Method: `PATCH`

Request Body:

```json
{
    "post_id": "post-id-goes-here"
}
```

### Admin Unpublish Post

Route: `/api/v1/admin/post/unpublish`
Method: `PATCH`

Request Body:

```json
{
    "post_id": "post-id-goes-here"
}
```

## Todo Due to time constraints

1. Upload Post Image

2. Delete Post Image

3. Admin Delete User

4. Admin Delete Post

5. Entity and End to End Test Cases
