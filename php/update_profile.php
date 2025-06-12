<?php
require_once 'config.php';
require_once 'middleware.php';

header("Content-Type: application/json");

// Middleware untuk autentikasi
authenticate();

$response = ['success' => false, 'error' => null, 'avatar_url' => null];

try {
    $userId = $_SESSION['user_id'];

    // Validasi data
    if (empty($_POST['full_name'])) {
        throw new Exception("Full name is required");
    }

    // Handle file upload
    $avatarPath = null;
    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = 'uploads/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['avatar']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Only JPG, PNG, and GIF images are allowed");
        }

        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) { // 2MB max
            throw new Exception("Image size must be less than 2MB");
        }

        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
            $avatarPath = $destination;
            $response['avatar_url'] = $destination;
        } else {
            throw new Exception("Failed to upload image");
        }
    }

    // Mulai transaksi
    $conn->begin_transaction();

    // Cek apakah profile sudah ada
    $stmt = $conn->prepare("SELECT id FROM profiles WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $socialLinks = json_decode($_POST['social_links'] ?? '{}', true) ?: [];
    $fullName = $_POST['full_name'];
    $bio = $_POST['bio'] ?? '';
    $socialLinks = json_encode($socialLinks);

    if ($result->num_rows > 0) {
        // Update profile yang sudah ada
        if ($avatarPath) {
            $stmt = $conn->prepare("UPDATE profiles SET 
                full_name = ?, 
                bio = ?, 
                avatar_url = ?, 
                social_links = ?,
                updated_at = NOW()
                WHERE user_id = ?");
            $stmt->bind_param(
                "ssssi",
                $fullName,
                $bio,
                $avatarPath,
                $socialLinks,
                $userId
            );
        } else {
            $stmt = $conn->prepare("UPDATE profiles SET 
                full_name = ?, 
                bio = ?, 
                social_links = ?,
                updated_at = NOW()
                WHERE user_id = ?");
            $stmt->bind_param(
                "sssi",
                $fullName,
                $bio,
                $socialLinks,
                $userId
            );
        }
    } else {
        // Buat profile baru
        $stmt = $conn->prepare("INSERT INTO profiles 
            (user_id, full_name, bio, avatar_url, social_links) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "issss",
            $userId,
            $_POST['full_name'],
            $_POST['bio'],
            $avatarPath,
            json_encode($socialLinks)
        );
    }

    $stmt->execute();
    $conn->commit();

    $response['success'] = true;
} catch (Exception $e) {
    $conn->rollback();

    // Delete uploaded file if error occurred
    if (!empty($avatarPath) && file_exists($avatarPath)) {
        unlink($avatarPath);
    }

    $response['error'] = $e->getMessage();
    http_response_code(400);
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response);
