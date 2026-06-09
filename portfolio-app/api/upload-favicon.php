<?php
require_once "./cors.php";
require_once "./api-logger.php";

logApiRequest();

$uploadDir = "../uploads/favicons/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (
    !isset($_FILES["favicon"]) ||
    $_FILES["favicon"]["error"] !== UPLOAD_ERR_OK
) {
    echo json_encode([
        "success" => false,
        "message" => "No file uploaded"
    ]);
    exit;
}

$ext = strtolower(
    pathinfo(
        $_FILES["favicon"]["name"],
        PATHINFO_EXTENSION
    )
);

$allowed = ["png", "ico", "svg", "jpg", "jpeg"];

if (!in_array($ext, $allowed)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid file type"
    ]);
    exit;
}

$fileName =
    "favicon_" .
    time() .
    "." .
    $ext;

$targetPath =
    $uploadDir .
    $fileName;

move_uploaded_file(
    $_FILES["favicon"]["tmp_name"],
    $targetPath
);

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? "https://"
        : "http://";

    $baseUrl = $protocol . $_SERVER['HTTP_HOST'] . "/apps/portfolio-app";
    $imageUrl = $baseUrl . "/uploads/favicons/" . $fileName;

echo json_encode([
    "success" => true,
    "url" => $imageUrl
]);