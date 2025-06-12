<?php
require_once '../php/config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$response = [
    'success' => false,
    'data' => [],
    'error' => null
];

try {
    // Data untuk chart pertama: Articles by Author and Category
    $authorsChartData = [];

    // Query untuk mendapatkan jumlah artikel per author dan kategori
    $sql = "SELECT 
                u.username as author,
                c.name as category,
                COUNT(ec.id) as article_count
            FROM editor_contents ec
            JOIN users u ON ec.author_id = u.id
            LEFT JOIN content_categories cc ON ec.id = cc.content_id
            LEFT JOIN categories c ON cc.category_id = c.id
            GROUP BY u.username, c.name
            ORDER BY u.username, c.name";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $authorsChartData[] = $row;
    }

    // Data untuk chart kedua: Articles Timeline
    $timelineChartData = [];

    // Query untuk mendapatkan jumlah artikel per bulan dan kategori
    $sql = "SELECT 
                DATE_FORMAT(ec.created_at, '%Y-%m') as month,
                c.name as category,
                COUNT(ec.id) as article_count
            FROM editor_contents ec
            LEFT JOIN content_categories cc ON ec.id = cc.content_id
            LEFT JOIN categories c ON cc.category_id = c.id
            WHERE ec.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(ec.created_at, '%Y-%m'), c.name
            ORDER BY month, c.name";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $timelineChartData[] = $row;
    }

    $response['success'] = true;
    $response['data'] = [
        'authors_chart' => $authorsChartData,
        'timeline_chart' => $timelineChartData
    ];
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(500);
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
