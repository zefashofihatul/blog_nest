<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID parameter is missing');
    }

    $contentId = (int)$_GET['id'];
    $stmt = $conn->prepare("
        SELECT c.id, c.name 
        FROM categories c
        JOIN content_categories cc ON c.id = cc.category_id
        WHERE cc.content_id = ?
    ");
    $stmt->bind_param("i", $contentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $categories
    ]);

    $stmt->close();
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    $conn->close();
}
