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
    $id = $_GET['id'] ?? null;

    if (!$id) {
        throw new Exception('Category ID is required');
    }

    // First check if category is used in any content
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM content_categories WHERE category_id = ?");
    if (!$checkStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        throw new Exception('Cannot delete category - it is being used by content');
    }

    // Delete category
    $deleteStmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    if (!$deleteStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $deleteStmt->bind_param("i", $id);

    if ($deleteStmt->execute()) {
        if ($deleteStmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Category deleted successfully';
        } else {
            throw new Exception('No category found with that ID');
        }
    } else {
        throw new Exception("Delete failed: " . $deleteStmt->error);
    }

    $deleteStmt->close();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    $conn->close();
}

echo json_encode($response);
