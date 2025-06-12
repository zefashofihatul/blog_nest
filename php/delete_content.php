<?php
require_once 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Content-Type: application/json");

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($id) {
        $sql = "DELETE FROM editor_contents WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Content deleted successfully';
        } else {
            $response['message'] = 'Error deleting content: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = 'ID parameter required';
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
$conn->close();
