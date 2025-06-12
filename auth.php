<?php
require_once 'php/config.php';
header("Content-Type: application/json");

session_start();

$response = ['success' => false, 'error' => null];

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validasi input
        if (empty($data['email']) || empty($data['password'])) {
            throw new Exception("Email and password are required");
        }

        $email = sanitizeInput($data['email']);

        // Cari user berdasarkan email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Invalid credentials");
        }

        $user = $result->fetch_assoc();

        // Verifikasi password
        if (!password_verify($data['password'], $user['password_hash'])) {
            throw new Exception("Invalid credentials");
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];

        // Update last login
        $update = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update->bind_param("i", $user['id']);
        $update->execute();

        $response['success'] = true;
        $response['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        // Jika ingin menggunakan JWT (opsional)
        $response['token'] = generateJWT($user);
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(401);
}

echo json_encode($response);

// Fungsi generate JWT (opsional)
function generateJWT($user)
{
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'user_id' => $user['id'],
        'role' => $user['role'],
        'exp' => time() + 3600 // Expires in 1 hour
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'your-secret-key', true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}
