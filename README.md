# Technical Test README

## Overview

This project is a simple CRM system designed for managing calls and tickets. It includes functionality for creating, reading, updating, and deleting (CRUD) calls and tickets. User authentication is implemented with role-based access for Agents and Supervisors. The backend is built with Laravel, while the frontend can be developed using Vue.js or React.

_I'm sorry I didn't have time to make it even better, as I have a lot of work recently out of nowhere..._

## Key Features

### Main Tasks

1. **Calls CRUD:**

    - **Agent**: Add new call (time, duration, subject)
    - **Supervisor**: View all recorded calls

2. **Tickets CRUD:**

    - **Agent**: Create ticket from a call
    - **Agent**: Update ticket status (e.g., In Progress, Resolved)
    - **Agent**: Add comments to a ticket
    - **Supervisor**: View all tickets

3. **User Authentication & Roles:**

    - Login system
    - Two roles: Agent and Supervisor
    - Role-based access restriction (Agent vs Supervisor views)

---

### Optional Features (Not Completed Yet)

-   **Email Notifications:**

    -   Send email on ticket creation
    -   Send email on ticket resolution

-   **Real-Time Notifications:**

    -   In-app notifications for new tickets
    -   In-app notifications for resolved tickets

---

## Backend Setup

### Prerequisites

-   PHP (>= 8)
-   Composer
-   MySQL
-   Node

### Steps

1. Clone the repository:

    ```bash
    git clone <repository_url>
    cd <project_directory>
    ```

2. Install dependencies using Composer:

    ```bash
    composer install
    ```

3. Set up your `.env` file with the database and environment configuration:

    ```bash
    cp .env.example .env
    ```

4. Generate the application key:

    ```bash
    php artisan key:generate
    ```

5. Create the database if you haven't already

6. Migrate the database:

    ```bash
    php artisan migrate
    ```

7. Place the folder inside your Herd and its directory inside the general settings for automatic deployment.

### Endpoints

### **1. Authentication Routes**

-   **`POST /register`**:

    -   This route handles user registration. It uses the `AuthController` to process new user registrations.

-   **`POST /login`**:

    -   This route handles user login. It uses the `AuthController` to verify credentials and generate an authentication token.

-   **`GET /user`**:

    -   This route returns the current authenticated user's data. It requires the user to be authenticated with Sanctum (`auth:sanctum` middleware) to access this route.

-   **`POST /logout`** (inside the `auth:sanctum` middleware group):
    -   This route allows the authenticated user to log out, by invalidating their authentication token via the `AuthController`.

---

### **2. Agent Routes (Authenticated User)**

These routes are protected by the `auth:sanctum` middleware, so only authenticated users can access them.

#### **Calls Routes**

-   **`GET /calls`** (`calls.index`):

    -   This route retrieves a list of all calls made by the authenticated agent.

-   **`GET /calls/create`** (`calls.create`):

    -   This route returns a form for the agent to create a new call.

-   **`POST /calls`** (`calls.store`):

    -   This route creates a new call, with the details (time, duration, subject) provided by the agent.

-   **`GET /calls/{call}`** (`calls.show`):

    -   This route shows the details of a specific call.

-   **`GET /calls/{call}/edit`** (`calls.edit`):

    -   This route returns a form to edit a specific call.

-   **`PUT /calls/{call}`** (`calls.update`):

    -   This route updates a specific call’s details (time, duration, subject).

-   **`DELETE /calls/{call}`** (`calls.delete`):

    -   This route deletes a specific call.

-   **`GET /calls/{call}/tickets/create`** (`calls.create-ticket`):
    -   This route creates a ticket based on the details of the call.

#### **Tickets Routes**

-   **`GET /tickets/create`** (`tickets.create`):

    -   This route returns a form for the agent to create a new ticket.

-   **`POST /tickets`** (`tickets.store`):

    -   This route creates a new ticket from the call details (if needed).

-   **`GET /tickets/{ticket}/edit`** (`tickets.edit`):

    -   This route returns a form to edit a specific ticket.

-   **`PUT /tickets/{ticket}`** (`tickets.update`):

    -   This route updates a specific ticket (e.g., its details).

-   **`POST /tickets/{ticket}/status`** (`tickets.update-status`):
    -   This route updates the status of a specific ticket (e.g., In Progress, Resolved).

#### **Comments Routes**

-   **`POST /comments/tickets/{ticket}`** (`comments.store`):

    -   This route allows an agent to add a comment to a ticket.

-   **`PUT /comments/{comment}`** (`comments.update`):

    -   This route allows the agent to update an existing comment.

-   **`DELETE /comments/{comment}`** (`comments.delete`):
    -   This route allows the agent to delete an existing comment.

---

### **3. Supervisor Routes (Authenticated User)**

The following routes are also protected by `auth:sanctum`, meaning only authenticated users can access them. These routes are specifically for the supervisor’s role.

#### **Calls Routes**

-   **`GET /calls`** (`calls.index`):

    -   This route retrieves a list of all calls made by agents, accessible by supervisors.

-   **`GET /calls/{call}`** (`calls.show`):
    -   This route allows a supervisor to view the details of a specific call.

#### **Tickets Routes**

-   **`GET /tickets`** (`tickets.index`):

    -   This route allows a supervisor to view a list of all tickets created by agents.

-   **`GET /tickets/{ticket}`** (`tickets.show`):

    -   This route allows a supervisor to view the details of a specific ticket.

-   **`PUT /tickets/{ticket}`** (`tickets.update`):

    -   This route allows a supervisor to update a ticket’s details (e.g., comments, status).

-   **`DELETE /tickets/{ticket}`** (`tickets.destroy`):
    -   This route allows a supervisor to delete a ticket.

#### **Comments Routes**

-   **`POST /comments/tickets/{ticket}`** (`comments.store`):

    -   This route allows a supervisor to add a comment to a ticket.

-   **`PUT /comments/{comment}`** (`comments.update`):

    -   This route allows a supervisor to update an existing comment on a ticket.

-   **`DELETE /comments/{comment}`** (`comments.delete`):
    -   This route allows a supervisor to delete an existing comment on a ticket.

---

### **Route Grouping and Naming Conventions**

-   The routes are grouped based on their functionality (calls, tickets, comments) and are prefixed accordingly (`calls.`, `tickets.`, `comments.`). This helps with organization and also when referring to the routes in the application.

-   The `middleware('auth:sanctum')` ensures that routes are protected and only accessible by authenticated users.

---

## Frontend Setup

### Prerequisites

-   Node.js (>= 16.x)
-   NPM (Node Package Manager)

### Steps

1. Clone the repository:

    ```bash
    git clone <repository_url>
    cd <project_directory>
    ```

2. Install frontend dependencies:

    ```bash
    npm install
    ```

3. Start the frontend server:

    ```bash
    npm run dev
    ```

4. Navigate to the provided URL (typically `http://localhost:5173` or 5174) to view the app.

### Components

-   **Login Page**: Allows users to log in and access role-specific views.
-   **Agent Dashboard**:

    -   Form to register a call
    -   Ticket creation form from call details
    -   View & update their own tickets

-   **Supervisor Dashboard**:

    -   View all calls (table)
    -   View all tickets with status
    -   Comment on any ticket

---

## Current State & Experience

### Completed

-   The backend is fully functional with endpoints for calls and tickets.
-   Role-based access control (Agent and Supervisor) is working.
-   Frontend includes basic login and dashboard views.

### Unfinished

-   **Email Notifications** and **Real-Time Notifications** are not implemented yet.
-   some ux details and ui glitches here and there

### Working Experience

Working on this technical test was a great learning experience. The tasks were straightforward but allowed for enough complexity to challenge me in designing and organizing the system. I enjoyed working with Laravel for the backend, especially with authentication and CRUD operations. The frontend setup and implementation were a good exercise in handling role-based access and ensuring a smooth user experience.

I faced challenges when it came to setting up real-time notifications, and while email notifications were on the radar, I didn't have enough time to fully integrate them.

God forbid I ever try to do web.php views first with laravel breeze, it was a mess. I lost the project mid work.

Hours spent: 8 hours and 24 minutes (I had to redo some stuff from scratch and lost time there)

---

## Conclusion

This project covers the essential aspects of a ticketing system with user roles. The basic CRUD operations are functional, and the system is mostly ready to be expanded with optional features like email and real-time notifications.
