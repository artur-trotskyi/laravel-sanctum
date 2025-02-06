# ROCKET9

**Technologies:** PHP 8.3, Laravel 11, MongoDB 7

## Initial Local Setup (Ubuntu)

### 1. Clone the Project

Clone this repository:

```
git clone <repo-url>
```

Clone the Docker Compose repository:

```
git clone <docker-compose-repo-url>
```

### 2. Start Containers

Navigate to the `rocket9-docker-compose` folder and start the containers in the background:

```
docker compose up -d
```

### 3. Create the Database

Create the `rocket9` database in MongoDB for the project.

### 4. Access the Web Container

Launch Bash in the `webserver` container:

```
docker compose exec webserver bash
```

Navigate to the project folder:

```
cd /var/www/html/rocket9-laravel
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

### 10. Configure Hosts File

Open `/etc/hosts`:

```
sudo nano /etc/hosts
```

Add the following line:

```
127.0.0.1   rocket9.local
```

### 11. Access API Documentation

You can now use the API documentation. See the [`api-docs.json`](storage/api-docs/api-docs.json) file and start testing
API requests.
Swagger UI will be available at http://your-api-project.test/api/documentation. Replace your-api-project.test with your
local development domain http://rocket9.local/api/documentation . 🚀

