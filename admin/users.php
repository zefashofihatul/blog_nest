<?php
require_once 'middleware.php';

header("Content-Type: application/json");

// Middleware untuk autentikasi
authenticate();

$response = ['success' => false, 'data' => [], 'error' => null];

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $userId = $_SESSION['user_id'] ?? null;
    $userRole = $_SESSION['user_role'] ?? null;

    switch ($method) {
        case 'GET':
            // Hanya admin yang bisa melihat daftar user
            authorize(['superadmin', 'admin']);

            $query = "SELECT u.id, u.username, u.email, u.role, u.status, p.full_name, u.created_at
                      FROM users u LEFT JOIN profiles p ON u.id = p.user_id";

            // Filter berdasarkan role jika perlu

            if (isset($_GET['role'])) {
                $query .= " WHERE u.role = ?";
                $stmt = $conn->prepare($query);
                $role = $_GET['role'];
                $stmt->bind_param("s", $role);
            } else {
                $stmt = $conn->prepare($query);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }
            $response['success'] = true;
            break;

        case 'POST':
            // Hanya superadmin yang bisa membuat admin baru
            authorize(['superadmin']);

            $data = json_decode(file_get_contents("php://input"), true);
            $required = ['username', 'email', 'password', 'role'];

            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field $field is required");
                }
            }

            // Validasi role
            if (!in_array($data['role'], ['admin', 'author'])) {
                throw new Exception("Invalid role");
            }

            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            $conn->begin_transaction();

            try {
                $conn->begin_transaction();

                // Insert ke tabel users
                $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
                $username = $data['username'];
                $email = $data['email'];
                $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT); // kalau belum ada
                $role = $data['role'] ?? 'author'; // fallback default role

                $stmt->bind_param("ssss", $username, $email, $passwordHash, $role);
                $stmt->execute();
                $newUserId = $conn->insert_id;

                // Insert ke tabel profiles
                $stmt = $conn->prepare("INSERT INTO profiles (user_id, full_name) VALUES (?, ?)");
                $fullName = $data['full_name'] ?? $username;
                $stmt->bind_param("is", $newUserId, $fullName);
                $stmt->execute();

                $conn->commit();

                $response['success'] = true;
                $response['data'] = ['id' => $newUserId];
                http_response_code(201);
            } catch (Exception $e) {
                $conn->rollback();
                $response['error'] = $e->getMessage();
                http_response_code(400);
            }

            break;

        case 'PUT':
            // Update user
            $targetUserId = $_GET['id'] ?? null;
            $data = json_decode(file_get_contents("php://input"), true);

            // Validasi: Hanya superadmin atau admin yang bisa mengupdate
            if ($userRole === 'admin' && $targetUserId != $userId) {
                authorize(['superadmin']); // Admin hanya bisa update profil sendiri
            }

            $updates = [];
            $params = [];
            $types = '';

            if (isset($data['email'])) {
                $updates[] = "email = ?";
                $params[] = $data['email'];
                $types .= 's';
            }

            if (isset($data['status']) && $userRole === 'superadmin') {
                $updates[] = "status = ?";
                $params[] = $data['status'];
                $types .= 's';
            }

            if (!empty($updates)) {
                $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
                $params[] = $targetUserId;
                $types .= 'i';

                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();

                $response['success'] = true;
            }
            break;

        case 'DELETE':
            // Hanya superadmin yang bisa menghapus user
            authorize(['superadmin']);

            $targetUserId = $_GET['id'] ?? null;

            if (!$targetUserId) {
                throw new Exception("User ID is required");
            }

            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $targetUserId);
            $stmt->execute();

            $response['success'] = true;
            break;

        default:
            http_response_code(405);
            throw new Exception("Method not allowed");
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    if (!isset($http_response_code)) {
        http_response_code(500);
    }
}

echo json_encode($response);
