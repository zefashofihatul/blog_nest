<?php
header('Content-Type: application/json');

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hybrid_editor');

// Membuat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

try {
    // Array untuk menyimpan hasil
    $stats = [];

    // Query untuk total authors (users)
    $query = "SELECT COUNT(*) as total_authors FROM users WHERE role = 'author'";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        $stats['totalAuthors'] = (int)$row['total_authors'];
        $result->free();
    } else {
        throw new Exception("Failed to fetch authors count: " . $conn->error);
    }

    // Query untuk total articles (editor_contents)
    $query = "SELECT COUNT(*) as total_articles FROM editor_contents WHERE status = 'published'";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        $stats['totalArticles'] = (int)$row['total_articles'];
        $result->free();
    } else {
        throw new Exception("Failed to fetch articles count: " . $conn->error);
    }

    // Query untuk total categories (categories)
    $query = "SELECT COUNT(*) as total_categories FROM categories";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['totalCategories'] = (int)$row['total_categories'];
        $result->free();
    } else {
        throw new Exception("Failed to fetch categories count: " . $conn->error);
    }

    // Response sukses
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch dashboard statistics',
        'error' => $e->getMessage()
    ]);
} finally {
    // Tutup koneksi
    $conn->close();
}
