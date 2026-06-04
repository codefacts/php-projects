<?php

require_once "./cors.php";
require_once "./auth.php";
require_once "../db.php";

$conn = getDbConnection();
$tpfix = getTablePrefix();

$data = json_decode(file_get_contents("php://input"), true);

$json = json_encode($data["content"]);

$stmt = $conn->prepare("UPDATE {$tpfix}admin SET content = ? WHERE id = 1");
$stmt->bind_param("s", $json);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}