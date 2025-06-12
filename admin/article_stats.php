<?php
require_once 'config.php';
header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            a.id,
            a.title,
            a.published_at,
            GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as categories
        FROM articles a
        LEFT JOIN article_categories ac ON a.id = ac.article_id
        LEFT JOIN categories c ON ac.category_id = c.id
        WHERE a.status = 'published'
        GROUP BY a.id
        ORDER BY a.published_at DESC
    ";

    $result = $conn->query($query);
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'article_id' => $row['id'],
            'title' => $row['title'],
            'published_at' => $row['published_at'],
            'categories' => $row['categories'] ?: 'Uncategorized'
        ];
    }

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
