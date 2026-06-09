<?php
require_once "./cors.php";
require_once "./auth.php";
require_once "../db.php";
require_once "./api-logger.php";

logApiRequest();

$input = json_decode(
    file_get_contents(
        "php://input"
    ),
    true
);

$siteTitle =
    trim(
        $input["site_title"]
        ?? ""
    );

$faviconUrl =
    trim(
        $input["favicon_url"]
        ?? ""
    );

$conn =
    getDbConnection();

$table =
    getTablePrefix()
    . "admin";

$stmt =
    $conn->prepare("
    UPDATE {$table}
    SET
      site_title = ?,
      favicon_url = ?
    WHERE id = 1
");

$stmt->bind_param(
    "ss",
    $siteTitle,
    $faviconUrl
);

$stmt->execute();

echo json_encode([
    "success" => true
]);