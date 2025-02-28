# LARAVEL SANCTUM

**Technologies:** PHP 8.3, Laravel 11, MySQL 8

## Initial Local Setup (Ubuntu)

### 1. Clone the Project

Clone this repository:

```
git clone <laravel-sanctum-repo-url>
```

Clone the Docker Compose repository:

```
git clone <laravel-sanctum-docker-compose-repo-url>
```

### 2. Start Containers

Navigate to the `laravel-sanctum-docker-compose` folder and start the containers in the background:

```
docker compose up -d
```

### 3. Create the Database

Create the `laravel_sanctum` database in MySQL on host machine for the project.

### 4. Access the Web Container

Launch Bash in the `webserver` container:

```
docker compose exec webserver bash
```

Navigate to the project folder:

```
cd /var/www/html/laravel-sanctum
```

### 5. Configure `.env`

Copy `.env.example` to `.env`:

```
cp .env.example .env
```

### 6. Install Dependencies

Install all dependencies using Composer:

```
composer install
```

### 7. Generate Application Key

```
php artisan key:generate
```

### 8. Fix Errors

```
php artisan optimize:clear
chmod -R 777 bootstrap/cache
chmod -R 777 storage
```

### 9. Run Migrations and Seeders

```
php artisan migrate:fresh --seed
```

### 10. Run Queue

```
php artisan horizon
```

### 11. Configure Hosts File on Host Machine

Open `/etc/hosts`:

```
sudo nano /etc/hosts
```

Add the following line:

```
127.0.0.1   laravel-sanctum-vue.local
```

### 12. Access API Documentation

You can now use the API documentation. See the [`api-docs.json`](storage/api-docs/api-docs.json) file and start testing
API requests.
Swagger UI will be available at http://your-api-project.test/api/documentation. Replace your-api-project.test with your
local development domain http://laravel-sanctum-vue.local/api/documentation . 🚀

### 13. Change HTTP Port if Needed

If port 80 is already in use (e.g., by a frontend application), you can change the HTTP port
HOST_MACHINE_UNSECURE_HOST_PORT in the .env file in the laravel-sanctum-docker-compose folder

### 14. Monitor Queue Execution

Visit /horizon in your browser to monitor the execution of queued jobs.

In production environments, access to this page is protected by basic authentication. Use HORIZON_BASIC_AUTH_USERNAME and HORIZON_BASIC_AUTH_PASSWORD from the .env file to log in.

### 15. Other commands
```
docker compose stop
docker compose start
docker compose down
docker compose up -d
docker compose exec vue bash
docker compose build --no-cache
-------------------------
./vendor/bin/pint
php artisan l5-swagger:generate
php artisan test
php artisan migrate:fresh --seed
php artisan horizon
php artisan cache:clear - for cache reset, e.g. for jobs

Using --hours=0.01 removes tokens that expired less than a minute ago
php artisan sanctum:prune-expired --hours=0.01
```
