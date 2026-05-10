<?php
require_once "./cors.php";
require_once '../auth.php';

$filename = $_POST['filename'];
$filepath = dirname(__DIR__) . "/uploads/" . basename($filename);

if (file_exists($filepath)) {

    unlink($filepath);

    echo json_encode([
        "success" => true
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Image not found"
    ]);
}
?>