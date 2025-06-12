<?php
require_once 'config.php';
header('Content-Type: application/json');

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [
    'people' => [],
    'files' => [],
    'channels' => []
];

if (empty($searchTerm)) {
    echo json_encode(['success' => true, 'data' => $results]);
    exit;
}

try {

    // 1. Search People (Users)
    $userStmt = $conn->prepare("
        SELECT id, username, full_name, avatar_url, 
               CASE 
                   WHEN last_activity > NOW() - INTERVAL 5 MINUTE THEN 'active'
                   ELSE CONCAT('Last seen ', TIMESTAMPDIFF(DAY, last_activity, NOW()), 'd ago')
               END as status
        FROM users
        WHERE username LIKE ? OR full_name LIKE ?
        LIMIT 5
    ");
    $likeTerm = "%$searchTerm%";
    $userStmt->bind_param("ss", $likeTerm, $likeTerm);
    $userStmt->execute();
    $results['people'] = $userStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 2. Search Files (Blog Posts)
    $postStmt = $db->prepare("
        SELECT id, title as filename, created_at as date, 'blog' as type
        FROM editor_contents
        WHERE title LIKE ? OR content LIKE ?
        LIMIT 5
    ");
    $postStmt->bind_param("ss", $likeTerm, $likeTerm);
    $postStmt->execute();
    $results['files'] = $postStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 3. Search Channels (Categories)
    $catStmt = $db->prepare("
        SELECT id, name as channel_name, 
               (SELECT COUNT(*) FROM content_categories WHERE category_id = categories.id) as members
        FROM categories
        WHERE name LIKE ? OR description LIKE ?
        LIMIT 5
    ");
    $catStmt->bind_param("ss", $likeTerm, $likeTerm);
    $catStmt->execute();
    $results['channels'] = $catStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'data' => $results]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
