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
        'per_page' => 10,
        'total_pages' => 1
    ],
    'error' => null
];

try {
    // Get parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $status = isset($_GET['status']) ? $_GET['status'] : 'published';
    $offset = ($page - 1) * $perPage;

    // Base SQL queries
    $baseCountSql = "SELECT COUNT(DISTINCT ec.id) as total 
                    FROM editor_contents ec
                    LEFT JOIN content_categories cc ON ec.id = cc.content_id
                    LEFT JOIN categories c ON cc.category_id = c.id
                    WHERE ec.status = ?";

    $baseDataSql = "SELECT 
                    ec.id, ec.title, ec.slug, ec.content, ec.excerpt, 
                    ec.featured_image, ec.status, ec.word_count, ec.author_id,
                    ec.created_at, ec.updated_at, p.full_name as author_name,
                    p.avatar_url as author_avatar,
                    GROUP_CONCAT(c.name SEPARATOR '|') as category_names,
                    GROUP_CONCAT(c.slug SEPARATOR '|') as category_slugs
                FROM editor_contents ec
                LEFT JOIN content_categories cc ON ec.id = cc.content_id
                LEFT JOIN categories c ON cc.category_id = c.id
                LEFT JOIN users u ON ec.author_id = u.id
                LEFT JOIN profiles p ON u.id = p.user_id
                WHERE ec.status = ?
                ";

    // Add search condition if provided
    $searchCondition = "";
    if (!empty($search)) {
        $search = "%$search%";
        $searchCondition = " AND (ec.title LIKE ? OR ec.content LIKE ? OR ec.excerpt LIKE ? 
                            OR c.name LIKE ?)";
    }

    // Prepare and execute count query
    $countSql = $baseCountSql . $searchCondition;
    $countStmt = $conn->prepare($countSql);

    if (!empty($search)) {
        $countStmt->bind_param("sssss", $status, $search, $search, $search, $search);
    } else {
        $countStmt->bind_param("s", $status);
    }

    $countStmt->execute();
    $totalResult = $countStmt->get_result()->fetch_assoc();
    $total = $totalResult['total'];

    // Prepare and execute data query
    $dataSql = $baseDataSql . $searchCondition . " 
                GROUP BY ec.id
                ORDER BY ec.created_at DESC
                LIMIT ?, ?";

    $stmt = $conn->prepare($dataSql);

    if (!empty($search)) {
        $stmt->bind_param("sssssii", $status, $search, $search, $search, $search, $offset, $perPage);
    } else {
        $stmt->bind_param("sii", $status, $offset, $perPage);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Process results
    while ($row = $result->fetch_assoc()) {
        // Format categories
        $categories = [];
        if (!empty($row['category_names'])) {
            $names = explode('|', $row['category_names']);
            $slugs = explode('|', $row['category_slugs']);

            for ($i = 0; $i < count($names); $i++) {
                if (!empty($names[$i])) {
                    $categories[] = [
                        'name' => $names[$i],
                        'slug' => $slugs[$i]
                    ];
                }
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
            'author_name' => $row['author_name'],
            'author_avatar' => 'php/' . $row['author_avatar'],
            'categories' => $categories,
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    };

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
