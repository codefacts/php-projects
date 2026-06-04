<?php

require_once "./cors.php";
require_once "../db.php";

$conn = getDbConnection();
$tablePrefix = getTablePrefix();

$result = $conn->query("SELECT content FROM {$tablePrefix}admin WHERE id = 1");

$row = $result->fetch_assoc();

$content = $row["content"];

echo json_encode([
    "success" => true,
    "data" => $content ? json_decode($content, true) : new stdClass()
]);