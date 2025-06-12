<?php
require_once 'php/config.php';
header("Content-Type: application/json");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['valid' => false, 'error' => null, 'role' => null];

try {
    $jsonInput = file_get_contents("php://input");
    if (empty($jsonInput)) {
        throw new Exception("No input data");
    }

    $data = json_decode($jsonInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON input");
    }

    // Validasi input
    if (!isset($data['user_id']) || !is_numeric($data['user_id'])) {
        throw new Exception("Invalid user ID");
    }

    // Validasi session dan role
    if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $data['user_id']) {
        // Ambil data user dari database untuk memverifikasi role
        $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->bind_param("i", $data['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("User not found");
        }

        $user = $result->fetch_assoc();
        $response['valid'] = true;
        $response['role'] = $user['role'];
        $_SESSION['role'] = $user['role']; // Simpan role di session
        $_SESSION['last_activity'] = time();
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response, JSON_UNESCAPED_SLASHES);
exit;
