<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'db.php';  // Database connection
require_once 'jwt.php'; // JWT functions

// Check the method
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

// Authentication Middleware
function authenticate($token) {
    try {
        return JwtAuth::decode($token); // Decode the token and return the payload
    } catch (Exception $e) {
        return null;
    }
}

// Login user (POST /users/login)
if ($path[0] == 'users' && $path[1] == 'login' && $method == 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $username = $input['username'];
    $password = $input['password'];

    // Query the database for the user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Generate JWT token
        $jwt = JwtAuth::encode([
            'id' => $user['id'],
            'username' => $user['username']
        ]);

        // Respond with the token
        echo json_encode([
            'message' => 'Login successful',
            'token' => $jwt
        ]);
    } else {
        // Invalid credentials
        echo json_encode([
            'message' => 'Invalid credentials'
        ]);
    }
    exit;
}

// Protected routes requiring authentication
$token = null;
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
}

if ($path[0] == 'users' && $method == 'GET') {
    // Authenticate the user via token
    if ($token && authenticate($token)) {
        if ($path[1] == 'list') {
            // List all users (GET /users/list)
            $stmt = $pdo->query("SELECT id, username, email FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        } elseif ($path[1] == 'single' && isset($path[2])) {
            // Get single user by ID (GET /users/single/{id})
            $userId = $path[2];
            $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($user ? $user : ['message' => 'User not found']);
        }
    } else {
        echo json_encode(['message' => 'Unauthorized']);
    }
    exit;
}

// Create user (POST /users/create)
if ($path[0] == 'users' && $path[1] == 'create' && $method == 'POST') {
    if ($token && authenticate($token)) {
        $input = json_decode(file_get_contents("php://input"), true);

        // Sanitize and validate input
        $username = $input['username'];
        $email = $input['email'];
        $password = password_hash($input['password'], PASSWORD_BCRYPT);

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Username already exists']);
            exit;
        }

        // Insert new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);

        echo json_encode(['message' => 'User created successfully']);
    } else {
        echo json_encode(['message' => 'Unauthorized']);
    }
    exit;
}

// Update user (PUT /users/update/{id})
if ($path[0] == 'users' && $path[1] == 'update' && $method == 'PUT' && isset($path[2])) {
    if ($token && authenticate($token)) {
        $userId = $path[2];
        $input = json_decode(file_get_contents("php://input"), true);

        $username = $input['username'];
        $email = $input['email'];
        $password = password_hash($input['password'], PASSWORD_BCRYPT);

        // Update user in the database
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $email, $password, $userId]);

        echo json_encode(['message' => 'User updated successfully']);
    } else {
        echo json_encode(['message' => 'Unauthorized']);
    }
    exit;
}

// Delete user (DELETE /users/delete/{id})
if ($path[0] == 'users' && $path[1] == 'delete' && $method == 'DELETE' && isset($path[2])) {
    if ($token && authenticate($token)) {
        $userId = $path[2];

        // Delete user from the database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        echo json_encode(['message' => 'User deleted successfully']);
    } else {
        echo json_encode(['message' => 'Unauthorized']);
    }
    exit;
}

// If no valid endpoint is found
echo json_encode(['message' => 'Invalid endpoint']);
