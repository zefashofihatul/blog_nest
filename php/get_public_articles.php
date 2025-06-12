<?php
require_once 'config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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

    // Get filter parameters
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;

    // Get total count of published articles
    $countSql = "SELECT COUNT(DISTINCT ec.id) as total 
                FROM editor_contents ec
                LEFT JOIN content_categories cc ON ec.id = cc.content_id
                LEFT JOIN categories c ON cc.category_id = c.id
                WHERE ec.status = 'published'";

    if ($categoryFilter) {
        $countSql .= " AND c.slug = ?";
    }

    $countStmt = $conn->prepare($countSql);

    if ($categoryFilter) {
        $countStmt->bind_param("s", $categoryFilter);
    }

    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];

    // Query with pagination and filters
    $sql = "SELECT 
                ec.id, ec.title, ec.slug, ec.content, ec.excerpt, 
                ec.featured_image, ec.status, ec.word_count, ec.author_id,
                ec.created_at, ec.updated_at,
                u.username as author_name,
                GROUP_CONCAT(c.name SEPARATOR '|') as category_names,
                GROUP_CONCAT(c.slug SEPARATOR '|') as category_slugs
            FROM editor_contents ec
            LEFT JOIN content_categories cc ON ec.id = cc.content_id
            LEFT JOIN categories c ON cc.category_id = c.id
            LEFT JOIN users u ON ec.author_id = u.id
            WHERE ec.status = 'published'";

    if ($categoryFilter) {
        $sql .= " AND c.slug = ?";
    }

    $sql .= " GROUP BY ec.id
            ORDER BY ec.created_at DESC
            LIMIT ?, ?";

    $stmt = $conn->prepare($sql);

    if ($categoryFilter) {
        $stmt->bind_param("sii", $categoryFilter, $offset, $perPage);
    } else {
        $stmt->bind_param("ii", $offset, $perPage);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
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
            'author' => [
                'username' => $row['author_name']
            ],
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
