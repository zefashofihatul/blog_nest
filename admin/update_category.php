<?php
header('Content-Type: application/json');
require_once '../php/config.php';

$response = ['success' => false, 'message' => ''];

// Check connection
if ($conn->connect_error) {
    $response['message'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['id'])) {
        throw new Exception('Category ID is required');
    }

    // Prepare statement
    $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        "sssi",
        $input['name'],
        $input['slug'],
        $input['description'],
        $input['id']
    );

    // Execute and check result
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Category updated successfully';
        } else {
            $response['message'] = 'No changes made or category not found';
        }
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    $conn->close();
}

echo json_encode($response);
