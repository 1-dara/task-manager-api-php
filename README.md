# Task Manager API (Laravel)

A RESTful task management API built with Laravel and Eloquent ORM, featuring full CRUD operations, request validation, and a relational database schema. Built as a Laravel port of an existing [Node.js/Express/Prisma version] to compare backend frameworks.

## Live Demo

- **API URL:** https://task-manager-api-php.onrender.com

Note: this is hosted on Render's free tier, so the first request after a period of inactivity may take 30-60 seconds to respond while the server spins back up. Occasional transient errors may also occur under free-tier resource constraints — if you hit one, a retry typically resolves it.

## Features

- Full CRUD for tasks (Create, Read, Update, Delete)
- Request validation with automatic error responses
- User–Task relationship (one-to-many)
- RESTful routing via Laravel's `apiResource`
- SQLite database (zero-config, easy to run locally)
- Laravel Sanctum installed (auth-ready)

## Tech Stack

- **PHP** 8.2
- **Laravel** 12
- **Eloquent ORM**
- **SQLite**
- **Laravel Sanctum** (API authentication)

## Database Schema

**User**
| Field | Type |
|---|---|
| id | integer, primary key |
| name | string |
| email | string, unique |
| password | string, hashed |

**Task**
| Field | Type |
|---|---|
| id | integer, primary key |
| title | string |
| description | text, nullable |
| status | string, default: `pending` |
| priority | string, default: `medium` |
| due_date | date, nullable |
| user_id | foreign key → users.id |

A `User` has many `Task`s; a `Task` belongs to a `User`.

## API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/tasks` | List all tasks |
| POST | `/api/tasks` | Create a new task |
| GET | `/api/tasks/{id}` | Get a single task |
| PUT/PATCH | `/api/tasks/{id}` | Update a task |
| DELETE | `/api/tasks/{id}` | Delete a task |

### Example Requests

**Create a task**
```bash
curl -X POST http://127.0.0.1:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"title": "Learn Laravel", "user_id": 1}'
```

**Update a task**
```bash
curl -X PUT http://127.0.0.1:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status": "completed"}'
```

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer

### Installation

```bash
git clone https://github.com/YOUR-USERNAME/task-manager-api-php.git
cd task-manager-api-php
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`.

## Validation

Requests are validated server-side. For example, creating a task without a `title` returns:

```json
{
  "message": "The title field is required.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

## Roadmap

- [ ] Authentication (register/login via Sanctum)
- [ ] User-scoped tasks (only see your own tasks)
- [ ] Nested route: get all tasks for a specific user
- [ ] Deployment

## About

Built while learning Laravel, as a direct comparison to an existing Node.js/Express/Prisma implementation of the same API.