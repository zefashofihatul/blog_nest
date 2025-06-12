<?php
require_once 'config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$response = [
    'success' => false,
    'data' => [],
    'error' => null
];

try {
    // Prepare and execute query using MySQLi
    $query = "SELECT id, name, slug FROM categories ORDER BY name ASC";
    $result = $conn->query($query);

    if ($result === false) {
        throw new Exception($conn->error);
    }

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    $response['success'] = true;
    $response['data'] = $categories;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(500);
} finally {
    if (isset($conn)) {
        $conn->close(); // Close MySQLi connection
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
