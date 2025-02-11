Sure! Here's a `README.md` for the project, outlining how to set up, run, and use the API:

---

# PHP RESTful API with JWT Authentication

This is a simple PHP-based RESTful API that uses JWT (JSON Web Tokens) for authentication and allows CRUD operations on the `users` table in a MySQL database.

## Requirements

- PHP 7.4 or higher
- MySQL Database
- Composer (for installing dependencies, if needed)
- cURL or any HTTP client for testing

## Installation

### 1. Clone or Download the Project

You can download the project as a `.zip` file or clone it using Git.

```bash
git clone https://github.com/your-repo/php-jwt-api.git
cd php-jwt-api
```

### 2. Setup Database

Create a MySQL database and table. You can execute the following SQL commands in your MySQL client to set up the `users` table:

```sql
CREATE DATABASE your_db_name;

USE your_db_name;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);
```

### 3. Configure the Database Connection

Open `db.php` and modify the following values with your own database credentials:

```php
$host = 'localhost';      // Your database host
$dbname = 'your_db_name'; // Your database name
$username = 'your_user';  // Your database username
$password = 'your_password';  // Your database password
```

### 4. Install Dependencies (Optional)

If you are using any additional dependencies (e.g., libraries for JWT), run Composer to install them:

```bash
composer install
```

For this project, all functionality is self-contained in `jwt.php` and `api.php`, so no external dependencies are required beyond PHP and MySQL.

---

## Usage

### API Endpoints

Below are the available API endpoints for this project:

1. **Login:**
    - **Method**: `POST`
    - **URL**: `/users/login`
    - **Body** (JSON):
      ```json
      {
        "username": "your_username",
        "password": "your_password"
      }
      ```
    - **Response** (on success):
      ```json
      {
        "message": "Login successful",
        "token": "<jwt_token>"
      }
      ```

2. **List Users:**
    - **Method**: `GET`
    - **URL**: `/users/list`
    - **Headers**:
        - `Authorization: Bearer <jwt_token>`
    - **Response**:
      ```json
      [
        {
          "id": 1,
          "username": "user1",
          "email": "user1@example.com"
        },
        ...
      ]
      ```

3. **Get Single User:**
    - **Method**: `GET`
    - **URL**: `/users/single/{id}`
    - **Headers**:
        - `Authorization: Bearer <jwt_token>`
    - **Response**:
      ```json
      {
        "id": 1,
        "username": "user1",
        "email": "user1@example.com"
      }
      ```

4. **Create User:**
    - **Method**: `POST`
    - **URL**: `/users/create`
    - **Headers**:
        - `Authorization: Bearer <jwt_token>`
    - **Body** (JSON):
      ```json
      {
        "username": "new_user",
        "email": "new_user@example.com",
        "password": "new_password"
      }
      ```
    - **Response** (on success):
      ```json
      {
        "message": "User created successfully"
      }
      ```

5. **Update User:**
    - **Method**: `PUT`
    - **URL**: `/users/update/{id}`
    - **Headers**:
        - `Authorization: Bearer <jwt_token>`
    - **Body** (JSON):
      ```json
      {
        "username": "updated_user",
        "email": "updated_user@example.com",
        "password": "updated_password"
      }
      ```
    - **Response** (on success):
      ```json
      {
        "message": "User updated successfully"
      }
      ```

6. **Delete User:**
    - **Method**: `DELETE`
    - **URL**: `/users/delete/{id}`
    - **Headers**:
        - `Authorization: Bearer <jwt_token>`
    - **Response** (on success):
      ```json
      {
        "message": "User deleted successfully"
      }
      ```

---

## Example with cURL

### 1. **Login and Get Token:**

```bash
curl -X POST http://localhost/api.php/users/login \
     -H "Content-Type: application/json" \
     -d '{"username": "your_username", "password": "your_password"}'
```

**Response:**
```json
{
  "message": "Login successful",
  "token": "your_jwt_token"
}
```

### 2. **Get List of Users:**

```bash
curl -X GET http://localhost/api.php/users/list \
     -H "Authorization: Bearer your_jwt_token"
```

**Response:**
```json
[
  {
    "id": 1,
    "username": "user1",
    "email": "user1@example.com"
  }
]
```

### 3. **Create a New User:**

```bash
curl -X POST http://localhost/api.php/users/create \
     -H "Authorization: Bearer your_jwt_token" \
     -H "Content-Type: application/json" \
     -d '{"username": "new_user", "email": "new_user@example.com", "password": "new_password"}'
```

**Response:**
```json
{
  "message": "User created successfully"
}
```

---

## JWT Token

- The `JWT` token is returned after successful login and should be included in the `Authorization` header for subsequent requests.
- The token expires after 1 hour by default. You may need to re-login after the token expires.

---

## Security

- The **secret key** used for JWT signing (`your_secret_key_here`) should be kept secure and not exposed in the public code repository.
- You can replace the default `secret_key` in `jwt.php` with your own secure key.
- Ensure that your database is secured and that sensitive data (e.g., passwords) are always hashed (we're using `bcrypt` here).

---

## License

This project is free to use and you don't have to mention me. it's stuff i put together copying from stackoverflow:D!
---
