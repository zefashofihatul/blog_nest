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

    // Validation
    if (empty($input['name'])) {
        throw new Exception('Category name is required');
    }

    // Check if category exists
    $checkStmt = $conn->prepare("SELECT id FROM categories WHERE name = ? OR slug = ?");
    if (!$checkStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $checkStmt->bind_param("ss", $input['name'], $input['slug']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        throw new Exception('Category with this name or slug already exists');
    }
    $checkStmt->close();

    // Insert new category
    $insertStmt = $conn->prepare("INSERT INTO categories (name, slug, description, created_at) VALUES (?, ?, ?, NOW())");
    if (!$insertStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $insertStmt->bind_param("sss", $input['name'], $input['slug'], $input['description']);

    if ($insertStmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Category created successfully';
        $response['category_id'] = $insertStmt->insert_id;
    } else {
        throw new Exception("Execute failed: " . $insertStmt->error);
    }

    $insertStmt->close();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    $conn->close();
}

echo json_encode($response);
