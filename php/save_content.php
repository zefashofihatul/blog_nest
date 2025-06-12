<?php
require_once 'config.php';
require_once 'middleware.php';

header('Content-Type: application/json');

// Middleware untuk autentikasi
authenticate();

function generateExcerpt($content, $length = 150)
{
    $text = strip_tags($content);
    $text = preg_replace('/\s+/', ' ', $text);
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return $text;
}

try {
    // Validasi input
    if (!isset($_POST['title'])) {
        throw new Exception('Judul artikel harus diisi');
    }

    $title = trim($_POST['title']);
    if (empty($title)) {
        throw new Exception('Judul tidak boleh kosong');
    }

    // Sanitasi input
    $title = sanitizeInput($title);
    $content = $_POST['content'] ?? '';
    $authorId = $_SESSION['user_id'];

    // Validasi status
    $allowed_statuses = ['draft', 'published'];
    $status = isset($_POST['publish']) && $_POST['publish'] === '1' ? 'published' : 'draft';
    if (!in_array($status, $allowed_statuses)) {
        throw new Exception('Status tidak valid');
    }

    $excerpt = generateExcerpt($content);
    $publish = isset($_POST['publish']) && $_POST['publish'] === '1' ? 1 : 0;
    $contentId = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $wordContent = isset($_POST['wordCount']) ? (int)$_POST['wordCount'] : 0;
    $categories = !empty($_POST['categories']) ? explode(',', $_POST['categories']) : [];

    // Proses upload cover image
    $coverPath = null;
    if (!empty($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/covers/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileInfo = getimagesize($_FILES['cover_image']['tmp_name']);
        if (!$fileInfo) {
            throw new Exception('File gambar tidak valid');
        }

        $validTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
        if (!in_array($fileInfo[2], $validTypes)) {
            throw new Exception('Hanya format JPEG, PNG dan GIF yang diizinkan');
        }

        if ($_FILES['cover_image']['size'] > 5 * 1024 * 1024) {
            throw new Exception('Ukuran gambar melebihi 5MB');
        }

        $extension = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        $fileName = uniqid() . '.' . $extension;
        $targetPath = $targetDir . $fileName;

        if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $targetPath)) {
            throw new Exception('Gagal mengupload gambar cover');
        }

        $coverPath = 'php/' . $targetPath;
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Operasi database untuk konten
        if ($contentId) {
            // Update konten yang sudah ada
            $stmt = $conn->prepare("UPDATE editor_contents SET 
                                  title = ?, 
                                  content = ?, 
                                  excerpt = ?, 
                                  featured_image = COALESCE(?, featured_image), 
                                  status = ?, 
                                  published = ?, 
                                  word_count = ?,
                                  updated_at = NOW()
                                  WHERE id = ?");
            $stmt->bind_param("sssssiii", $title, $content, $excerpt, $coverPath, $status, $publish, $wordContent, $contentId);
        } else {
            // Insert konten baru
            $stmt = $conn->prepare("INSERT INTO editor_contents 
                                  (title, content, excerpt, featured_image, status, word_count, author_id, published)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiii", $title, $content, $excerpt, $coverPath, $status, $wordContent, $authorId, $publish);
        }

        if (!$stmt->execute()) {
            throw new Exception('Error database: ' . $stmt->error);
        }

        // Dapatkan ID konten (untuk insert baru)
        $newContentId = $contentId ?: $stmt->insert_id;

        // Hapus kategori lama jika melakukan update
        if ($contentId) {
            $deleteStmt = $conn->prepare("DELETE FROM content_categories WHERE content_id = ?");
            $deleteStmt->bind_param("i", $newContentId);
            if (!$deleteStmt->execute()) {
                throw new Exception('Gagal menghapus kategori lama');
            }
            $deleteStmt->close();
        }

        // Tambahkan kategori baru jika ada
        if (!empty($categories)) {
            $categoryStmt = $conn->prepare("INSERT INTO content_categories (content_id, category_id) VALUES (?, ?)");

            foreach ($categories as $catId) {
                if (is_numeric($catId)) {
                    $categoryStmt->bind_param("ii", $newContentId, $catId);
                    if (!$categoryStmt->execute()) {
                        throw new Exception('Gagal menambahkan kategori: ' . $categoryStmt->error);
                    }
                }
            }
            $categoryStmt->close();
        }

        // Commit transaksi jika semua berhasil
        $conn->commit();

        // Response sukses
        echo json_encode([
            'success' => true,
            'message' => $publish ? 'Artikel berhasil dipublikasikan' : 'Draft berhasil disimpan',
            'content_id' => $newContentId
        ]);
    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        throw $e;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
