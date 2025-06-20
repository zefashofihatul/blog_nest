<?php
header('Content-Type: application/json');
require_once '../php/config.php';

$response = ['success' => false, 'data' => null];

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    $response['message'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

try {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        throw new Exception('Category ID is required');
    }

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id); // "i" for integer type
    $stmt->execute();

    $result = $stmt->get_result();
    $category = $result->fetch_assoc();

    if ($category) {
        $response['success'] = true;
        $response['data'] = $category;
    } else {
        $response['message'] = 'Category not found';
    }

    $stmt->close();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    $conn->close();
}

echo json_encode($response);
