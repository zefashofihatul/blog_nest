<?php
require_once 'php/config.php';
header("Content-Type: application/json");

// Pastikan session start hanya dipanggil sekali
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$response = ['valid' => false, 'error' => null];

try {
    // Ambil input dengan cara yang lebih aman
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

    // Validasi session
    if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $data['user_id']) {
        $response['valid'] = true;

        // Perbarui waktu session untuk mencegah timeout
        $_SESSION['last_activity'] = time();
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(400); // Bad Request
}

// Pastikan output benar-benar JSON
echo json_encode($response, JSON_UNESCAPED_SLASHES);
exit;
