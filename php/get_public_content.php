<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID parameter is missing');
    }

    $contentId = (int)$_GET['id'];

    // Modified query to join with users table and get author information
    $stmt = $conn->prepare("
        SELECT 
            ec.id, 
            ec.title, 
            ec.content, 
            ec.featured_image, 
            ec.created_at, 
            ec.published,
            ec.status,
            u.id as author_id,
            p.avatar_url as avatar_url,
            p.full_name as author_full_name
        FROM editor_contents ec
        LEFT JOIN users u ON ec.author_id = u.id
        LEFT JOIN profiles p ON p.user_id = u.id
        WHERE ec.id = ? AND (ec.status = 'published' OR ? = 1)
    ");

    // The second parameter is for admin preview (not implemented here)
    $isAdmin = 0;
    $stmt->bind_param("ii", $contentId, $isAdmin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Content not found or not published');
    }

    $data = $result->fetch_assoc();

    // Format the author data
    $authorData = [
        'id' => $data['author_id'],
        'avatar_url' => $data['avatar_url'],
        'full_name' => $data['author_full_name']
    ];

    // Remove author fields from main data array
    unset(
        $data['author_id'],
        $data['author_username'],
        $data['author_email'],
        $data['avatar_url'],
        $data['author_full_name']
    );

    // Add author to the response
    $data['author'] = $authorData;

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
