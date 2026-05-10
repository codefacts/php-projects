<?php

require_once "./cors.php";

$conn = new mysqli("localhost", "user", "123", "portfolio");

$result = $conn->query("SELECT content FROM admin WHERE id = 1");

$row = $result->fetch_assoc();

$content = $row["content"];

echo json_encode([
    "success" => true,
    "data" => $content ? json_decode($content, true) : new stdClass()
]);