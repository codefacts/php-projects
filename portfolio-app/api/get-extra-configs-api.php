<?php
require_once "./cors.php";
header('Content-Type: application/json');

require_once '../db.php'; 

try {
    $conn = getDbConnection(); 
    $tpfix = getTablePrefix();

    $stmt = $conn->prepare("SELECT site_title, favicon_url FROM {$tpfix}admin WHERE id = 1 LIMIT 1");
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode([
        'site_title' => $row['site_title'] ?? '',
        'favicon_url' => $row['favicon_url'] ?? ''
    ]);

} catch (Exception $e) {
    http_response_code(500);

    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}