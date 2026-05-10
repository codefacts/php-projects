<?php
include "db.php";

$result = $conn->query("SELECT * FROM content");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[$row["section_name"]] = json_decode($row["content"]);
}

echo json_encode($data);
?>