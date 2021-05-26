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

3. Run `docker-compose up -d`

4. The 3 containers are deployed: 

```
Creating symfony-docker-social-app_db_1    ... done
Creating symfony-docker-social-app_php_1   ... done
Creating symfony-docker-social-app_nginx_1 ... done
```