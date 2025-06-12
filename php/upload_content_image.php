<?php
require_once 'config.php';

// Tingkatkan limit upload
ini_set('post_max_size', '20M');
ini_set('upload_max_filesize', '20M');
ini_set('memory_limit', '128M');

header('Content-Type: application/json');

try {
    // Periksa metode request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Periksa apakah file ada
    if (!isset($_FILES['content_image']) || $_FILES['content_image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No image uploaded or upload error');
    }

    $targetDir = "uploads/content_images/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Generate unique filename
    $fileExt = strtolower(pathinfo($_FILES['content_image']['name'], PATHINFO_EXTENSION));
    $fileName = uniqid() . '.' . $fileExt;
    $targetPath = $targetDir . $fileName;

    // Validate image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($_FILES['content_image']['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Only JPG, PNG & GIF files are allowed');
    }

    // Check file size (max 10MB)
    if ($_FILES['content_image']['size'] > 10 * 1024 * 1024) {
        throw new Exception('File is too large (max 10MB)');
    }

    // Upload file
    if (move_uploaded_file($_FILES['content_image']['tmp_name'], $targetPath)) {
        // Return full URL jika perlu
        $imageUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/' . $targetPath;

        echo json_encode([
            'success' => true,
            'path' => $targetPath,
            'url' => $imageUrl
        ]);
    } else {
        throw new Exception('Error moving uploaded file');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
