# Chat Room Application

Welcome to the Chat Room Application! This is a Laravel-based project designed to provide a real-time chat experience. Follow the instructions below to set up and run the application.

## Prerequisites

Ensure you have the following installed on your machine:

- PHP 8.2 or higher
- Composer
- Docker and Docker Compose

## Installation

1. **Clone the repository:**

   ``
   git clone <repository-url>
   cd <repository-directory>
   ``

2. **Install PHP dependencies:**

   ``
   composer update
   ``

3. **Create the SQLite database file:**

   ``
   touch database/database.sqlite
   ``

4. **Set up the environment file:**

   ``
   cp .env.example .env
   ``

5. **Generate the application key:**

   ``
   php artisan key:generate
   ``

6. **Generate the JWT secret key:**

   ``
   php artisan jwt:secret
   ``

7. **Run migrations and seed the database:**

   ``
   php artisan migrate --seed
   ``

8. **Build and run Docker containers:**

   ``
   docker-compose up --build
   ``

## Usage

Once the containers are up and running, you can access the application at `http://localhost:8000`. The chat room should be fully operational, and you can start using the features right away.

## Troubleshooting

- **Permission Issues:** Ensure that your `database` directory has the correct permissions for SQLite file creation.
- **JWT Issues:** If you encounter issues with JWT, ensure that your `.env` file has the correct configuration for the JWT secret key.
