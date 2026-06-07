<?php

require_once './cors.php';
require_once './auth.php';

if (!isset($_FILES['file'])) {
    echo json_encode([
        "success" => false,
        "message" => "No image uploaded"
    ]);
    exit;
}

$file = $_FILES['file'];

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid file type"
    ]);
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode([
        "success" => false,
        "message" => "File too large"
    ]);
    exit;
}

$uploadDir = "../uploads/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = time() . "_" . basename($file['name']);

$targetFile = $uploadDir . $fileName;

if (move_uploaded_file($file['tmp_name'], $targetFile)) {

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? "https://"
        : "http://";

    $baseUrl = $protocol . $_SERVER['HTTP_HOST'] . "/apps/portfolio-app";
    $imageUrl = $baseUrl . "/uploads/" . basename($targetFile);

    echo json_encode([
        "success" => true,
        "imageUrl" => $imageUrl
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Upload failed"
    ]);
}
?>