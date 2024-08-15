# Chat Room - Case Study

### To-Do List for Chat Room Application Development

### **1. Project Initialization**

- [x]  Set up a new Laravel project.
- [x]  Initialize a Git repository.
- [ ]  Set up Docker with Dockerfile and docker-compose.yml.

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

- [ ]  Set up Pusher for WebSocket communication.
- [ ]  Implement real-time message broadcasting.
- [ ]  Create events and listeners for message broadcasting.
- [ ]  Write tests for real-time messaging.

### **6. Message Management**

- [ ]  Implement API endpoints for message management:
    - [ ]  Send Message.
    - [ ]  Delete Message.
- [ ]  Implement message deletion logic (user can delete own messages, admin can delete any).
- [ ]  Write tests for message management.

### **7. Scheduling & Queue Management**

- [ ]  Set up Laravel Queues.
- [ ]  Implement job for sending notification emails on join requests.
- [ ]  Schedule daily task to archive messages older than 30 days.
- [ ]  Write tests for scheduled tasks and queues.

### **8. Throttling & Rate Limiting**

- [ ]  Implement rate limiting on:
    - [ ]  Message sending.
    - [ ]  Chat room creation/deletion.
    - [ ]  Join requests.
- [ ]  Customize rate limit exceeded responses.
- [ ]  Write tests for rate limiting.

### **9. Logging & Error Handling**

- [ ]  Set up logging for critical events (e.g., JWT errors).
- [ ]  Implement global exception handling.
- [ ]  Write tests for error handling.

### **10. Dockerization**

- [ ]  Finalize Dockerfile and docker-compose.yml.
- [ ]  Ensure all environment variables are correctly set up.
- [ ]  Test Docker setup locally.

### **11. Documentation**

- [ ]  Write comprehensive README with setup instructions.
- [ ]  Document API endpoints and usage examples.
- [ ]  Include information on testing and running the application.

### **12. Bonus Features (Optional)**

- [ ]  Implement search functionality within messages.
- [ ]  Add support for file attachments in messages.
- [ ]  Develop a basic front-end interface for interacting with the API.
- [ ]  Write additional tests for bonus features.

### **13. Testing & Final Checks**

- [ ]  Perform thorough testing (unit and feature tests).
- [ ]  Review code and documentation for completeness.
- [ ]  Push final changes to GitHub.
- [ ]  Prepare for deployment (if required).