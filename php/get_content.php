<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID parameter is missing');
    }

    $contentId = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM editor_contents WHERE id = ?");
    $stmt->bind_param("i", $contentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Content not found');
    }

    $data = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'data' => $data
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
