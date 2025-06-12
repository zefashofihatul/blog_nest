<?php
require_once 'config.php';
header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            u.id, 
            u.username as author_name,
            COUNT(a.id) as total_articles,
            (
                SELECT GROUP_CONCAT(c.name SEPARATOR ', ')
                FROM articles a2
                JOIN article_categories ac ON a2.id = ac.article_id
                JOIN categories c ON ac.category_id = c.id
                WHERE a2.author_id = u.id
                GROUP BY c.name
                ORDER BY COUNT(a2.id) DESC
                LIMIT 1
            ) as top_category
        FROM users u
        LEFT JOIN articles a ON u.id = a.author_id
        WHERE u.role = 'author'
        GROUP BY u.id
        ORDER BY total_articles DESC
    ";

    $result = $conn->query($query);
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'author_id' => $row['id'],
            'author_name' => $row['author_name'],
            'total_articles' => (int)$row['total_articles'],
            'top_category' => $row['top_category'] ?: 'Tidak ada kategori'
        ];
    }

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
