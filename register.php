<?php
require_once 'php/config.php';
header("Content-Type: application/json");

$response = ['success' => false, 'error' => null];

// Define allowed roles
$allowedRoles = ['author', 'reader'];

try {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validasi input
    $required = ['username', 'email', 'password', 'role'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Field $field is required");
        }
    }

    // Validate role - this is the security measure
    if (!in_array($data['role'], $allowedRoles)) {
        throw new Exception("Invalid role selected");
    }

    // Rest of your validation (email, password, etc.)
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    if (strlen($data['password']) < 8) {
        throw new Exception("Password must be at least 8 characters");
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $data['email']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        throw new Exception("Email already registered");
    }

    // Hash password
    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

    // Create user with selected role (validated against allowedRoles)
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $username = sanitizeInput($data['username']);
    $email = sanitizeInput($data['email']);
    $role = sanitizeInput($data['role']);

    $stmt->bind_param("ssss", $username, $email, $passwordHash, $role);
    if ($stmt->execute()) {
        $response['success'] = true;
        http_response_code(201);
    } else {
        throw new Exception("Failed to create user");
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
