<?php
require_once '../php/config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$response = [
    'success' => false,
    'data' => [],
    'meta' => ['count' => 0],
    'error' => null
];

try {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Get all authors
            $sql = "SELECT * FROM authors ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $response['data'][] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'bio' => $row['bio'],
                    'avatar' => $row['avatar'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at']
                ];
            }

            $response['success'] = true;
            $response['meta']['count'] = count($response['data']);
            break;

        case 'POST':
            // Create new author
            $data = json_decode(file_get_contents("php://input"), true);

            $sql = "INSERT INTO authors (name, email, bio, avatar, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssss",
                $data['name'],
                $data['email'],
                $data['bio'],
                $data['avatar'],
                $data['status']
            );

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['data'] = ['id' => $stmt->insert_id];
                http_response_code(201);
            } else {
                throw new Exception("Failed to create author");
            }
            break;

        case 'PUT':
            // Update author
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $_GET['id'] ?? null;

            if (!$id) throw new Exception("Author ID is required");

            $sql = "UPDATE authors SET name=?, email=?, bio=?, avatar=?, status=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssssi",
                $data['name'],
                $data['email'],
                $data['bio'],
                $data['avatar'],
                $data['status'],
                $id
            );

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['data'] = ['id' => $id];
            } else {
                throw new Exception("Failed to update author");
            }
            break;

        case 'DELETE':
            // Delete author
            $id = $_GET['id'] ?? null;

            if (!$id) throw new Exception("Author ID is required");

            $sql = "DELETE FROM authors WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['data'] = ['id' => $id];
            } else {
                throw new Exception("Failed to delete author");
            }
            break;

        default:
            throw new Exception("Method not allowed");
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(500);
} finally {
    if (isset($conn)) $conn->close();
}

if ($response['success']) {
    http_response_code($method === 'POST' ? 201 : 200);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
