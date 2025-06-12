<?php
require_once 'config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$response = [
    'success' => false,
    'data' => null,
    'error' => null
];

try {
    $articleId = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if (!$articleId) {
        throw new Exception("Article ID is required");
    }

    $sql = "SELECT 
                ec.id, ec.title, ec.slug, ec.content, ec.excerpt, 
                ec.featured_image, ec.status, ec.word_count, ec.author_id,
                ec.created_at, ec.updated_at,
                u.username,
                p.full_name, p.bio, p.avatar_url,
                GROUP_CONCAT(c.name SEPARATOR '|') as category_names,
                GROUP_CONCAT(c.slug SEPARATOR '|') as category_slugs
            FROM editor_contents ec
            LEFT JOIN content_categories cc ON ec.id = cc.content_id
            LEFT JOIN categories c ON cc.category_id = c.id
            LEFT JOIN users u ON ec.author_id = u.id
            LEFT JOIN profiles p ON u.id = p.user_id
            WHERE ec.id = ? AND ec.status = 'published'
            GROUP BY ec.id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $articleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Article not found or not published");
    }

    $row = $result->fetch_assoc();

    // Format categories
    $categories = [];
    if (!empty($row['category_names'])) {
        $names = explode('|', $row['category_names']);
        $slugs = explode('|', $row['category_slugs']);

        for ($i = 0; $i < count($names); $i++) {
            $categories[] = [
                'name' => $names[$i],
                'slug' => $slugs[$i]
            ];
        }
    }

    $response['data'] = [
        'id' => $row['id'],
        'title' => htmlspecialchars_decode($row['title']),
        'slug' => $row['slug'],
        'content' => $row['content'],
        'excerpt' => $row['excerpt'],
        'featured_image' => $row['featured_image'],
        'status' => $row['status'],
        'word_count' => (int)$row['word_count'],
        'author_id' => (int)$row['author_id'],
        'author' => [
            'username' => $row['username'],
            'full_name' => $row['full_name'],
            'bio' => $row['bio'],
            'avatar_url' => $row['avatar_url']
        ],
        'categories' => $categories,
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at']
    ];

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(404);
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
