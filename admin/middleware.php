<?php
require_once '../php/config.php';
session_start();

function authenticate()
{
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
}

function authorize($roles = [])
{
    if (!in_array($_SESSION['user_role'], $roles)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Forbidden']);
        exit;
    }
}

// Fungsi untuk mendapatkan user yang sedang login
function getAuthUser()
{
    global $conn;

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}
