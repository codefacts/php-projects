<?php
include "auth.php";
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("UPDATE content SET content=? WHERE section_name=?");
$content = json_encode($data["content"]);

$stmt->bind_param("ss", $content, $data["section"]);
$stmt->execute();

echo json_encode(["success" => true]);
?>