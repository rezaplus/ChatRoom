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
   git clone https://github.com/rezaplus/ChatRoom.git
   cd ChatRoom
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
   sudo docker-compose up --build
   ``

9. **Test the application:**

   ``
   php artisan test
   ``

## Usage

Once the containers are up and running, you can access the application at `http://localhost:8000`. The chat room should be fully operational, and you can start using the features right away.

## Troubleshooting

- **Permission Issues:** Ensure that your `database` directory has the correct permissions for SQLite file creation.
- **JWT Issues:** If you encounter issues with JWT, ensure that your `.env` file has the correct configuration for the JWT secret key.

## API Endpoints Documentation
For detailed API documentation, you can refer to the [Postman Documentation](https://documenter.getpostman.com/view/25233262/2sA3s9E8ii#auth-info-1c9ffa0d-5a9c-4647-a7a1-6671bf169bd7).

##

# To-Do List

### **1. Project Initialization**

- [x]  Set up a new Laravel project.
- [x]  Initialize a Git repository.
- [x]  Set up Docker with Dockerfile and docker-compose.yml.

### **2. Authentication & Authorization**

- [x]  Install and configure JWT for authentication.
- [x]  Create middleware for JWT validation.
- [x]  Implement role-based access control (RBAC).
- [x]  Create roles: Admin, User, Guest.
- [x]  Write tests for JWT authentication and role-based access.

### **3. Database Schema Design**

- [x]  Design and create migrations for:
    - [x]  Users.
    - [x]  Chat Rooms.
    - [x]  Messages.
    - [x]  Roles.
    - [x]  Role_User (pivot table).
- [x]  Seed database with initial data (roles, admin user, etc.).

### **4. Chat Room Management**

- [x]  Implement API endpoints for chat room management:
    - [x]  Create Chat Room.
    - [x]  Delete Chat Room.
    - [x]  View Chat Rooms.
    - [x]  Join Chat Room (request and approval).
- [x]  Implement caching for chat room listings.
- [x]  Write tests for chat room APIs.

### **5. Real-Time Messaging**

- [x]  Set up Pusher for WebSocket communication.
- [x]  Implement real-time message broadcasting.
- [x]  Create events and listeners for message broadcasting.
- [x]  Write tests for real-time messaging.

### **6. Message Management**

- [x]  Implement API endpoints for message management:
    - [x]  Send Message.
    - [x]  Delete Message.
- [x]  Implement message deletion logic (user can delete own messages, admin can delete any).
- [x]  Write tests for message management.

### **7. Scheduling & Queue Management**

- [x]  Set up Laravel Queues.
- [x]  Implement job for sending notification emails on join requests.
- [x]  Schedule daily task to archive messages older than 30 days.
- [x]  Write tests for scheduled tasks and queues.

### **8. Throttling & Rate Limiting**

- [x]  Implement rate limiting on:
    - [x]  Message sending.
    - [x]  Chat room creation/deletion.
    - [x]  Join requests.
- [x]  Customize rate limit exceeded responses.
- [x]  Write tests for rate limiting.

### **9. Logging & Error Handling**

- [x]  Set up logging for critical events (e.g., JWT errors).
- [x]  Implement global exception handling.
- [x]  Write tests for error handling.

### **10. Dockerization**

- [x]  Finalize Dockerfile and docker-compose.yml.
- [x]  Ensure all environment variables are correctly set up.
- [x]  Test Docker setup locally.

### **11. Documentation**

- [x]  Write comprehensive README with setup instructions.
- [x]  Document API endpoints and usage examples.
- [x]  Include information on testing and running the application.

### **12. Bonus Features (Optional)**

- [ ]  Implement search functionality within messages.
- [ ]  Add support for file attachments in messages.
- [x]  Develop a basic front-end interface for interacting with the API.
- [ ]  Write additional tests for bonus features.

### **13. Testing & Final Checks**

- [x]  Perform thorough testing (unit and feature tests).
- [x]  Push final changes to GitHub.
