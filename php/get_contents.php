<?php
require_once 'config.php';
require_once 'middleware.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Middleware untuk autentikasi
authenticate();

$response = [
    'success' => false,
    'data' => [],
    'meta' => [
        'count' => 0,
        'total' => 0,
        'page' => 1,
        'per_page' => 10
    ],
    'error' => null
];

try {
    // Get pagination parameters
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $offset = ($page - 1) * $perPage;

    // Get current user ID from session
    $authorId = $_SESSION['user_id'];

    // Get total count of author's posts
    $countSql = "SELECT COUNT(DISTINCT ec.id) as total 
                FROM editor_contents ec
                WHERE ec.author_id = ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("i", $authorId);
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    // Query dengan pagination dan filter author
    $sql = "SELECT 
                ec.id, ec.title, ec.slug, ec.content, ec.excerpt, 
                ec.featured_image, ec.status, ec.word_count, ec.author_id,
                ec.created_at, ec.updated_at,
                GROUP_CONCAT(c.name SEPARATOR '|') as category_names,
                GROUP_CONCAT(c.slug SEPARATOR '|') as category_slugs
            FROM editor_contents ec
            LEFT JOIN content_categories cc ON ec.id = cc.content_id
            LEFT JOIN categories c ON cc.category_id = c.id
            WHERE ec.author_id = ?
            GROUP BY ec.id
            ORDER BY ec.created_at DESC
            LIMIT ?, ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $authorId, $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Format kategori
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

        $response['data'][] = [
            'id' => $row['id'],
            'title' => htmlspecialchars_decode($row['title']),
            'slug' => $row['slug'],
            'content' => $row['content'],
            'excerpt' => $row['excerpt'],
            'featured_image' => $row['featured_image'],
            'status' => $row['status'],
            'word_count' => (int)$row['word_count'],
            'author_id' => (int)$row['author_id'],
            'categories' => $categories,
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }

    $response['success'] = true;
    $response['meta'] = [
        'count' => count($response['data']),
        'total' => (int)$total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => ceil($total / $perPage)
    ];
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(500);
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
