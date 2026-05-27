<?php

require_once "./cors.php";
require_once "../db.php";

header("Content-Type: application/json");

try {

    // =========================
    // DB CONNECTION
    // =========================
    $conn = getDbConnection();

} catch (mysqli_sql_exception $e) {

    echo json_encode([
        "success" => false,
        "configured" => false,
        "message" => "Database connection failed",
        "details" => $e->getMessage()
    ]);

    exit;
}

// =========================
// CHECK IF TABLE EXISTS
// =========================

$table = "admin";

$tableCheck = $conn->query("
    SHOW TABLES LIKE '{$table}'
");

if ($tableCheck->num_rows === 0) {

    echo json_encode([
        "success" => true,
        "configured" => false,
        "message" => "Admin table does not exist. Site is not configured."
    ]);

    exit;
}

// =========================
// CHECK IF ADMIN EXISTS
// =========================

$result = $conn->query("
    SELECT id, username, site_title
    FROM {$table}
    LIMIT 1
");

if (!$result) {

    echo json_encode([
        "success" => false,
        "configured" => false,
        "message" => "Failed to query admin table",
        "details" => $conn->error
    ]);

    exit;
}

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => true,
        "configured" => false,
        "message" => "No admin found. Site is not configured."
    ]);

    exit;
}

// =========================
// SITE IS CONFIGURED
// =========================

$admin = $result->fetch_assoc();

echo json_encode([
    "success" => true,
    "configured" => true,
    "message" => "Site is configured."
]);