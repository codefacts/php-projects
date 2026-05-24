<?php

require_once "./cors.php";
require_once "./auth.php";

$conn = new mysqli("localhost", "user", "123", "portfolio");

$data = json_decode(file_get_contents("php://input"), true);

$json = json_encode($data["content"]);

$stmt = $conn->prepare("UPDATE admin SET content = ? WHERE id = 1");
$stmt->bind_param("s", $json);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}