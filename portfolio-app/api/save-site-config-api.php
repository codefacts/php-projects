<?php

session_start();
session_regenerate_id(true);

require_once "./cors.php";
require_once "../db.php";
require_once "./api-logger.php";

logApiRequest();

// =========================
// READ JSON BODY
// =========================

$input = json_decode(
    file_get_contents("php://input"),
    true
);

// =========================
// VALIDATE INPUT
// =========================

$siteTitle =
    trim(
        $input["site_title"] ?? ""
    );

$faviconUrl =
    trim(
        $input["favicon_url"] ?? ""
    );

$username =
    trim(
        $input["username"] ?? ""
    );

$password =
    trim(
        $input["password"] ?? ""
    );

if (
    !$siteTitle ||
    !$username ||
    !$password
) {

    echo json_encode([
        "success" => false,

        "message" =>
            "All fields are required."
    ]);

    exit;
}

// =========================
// DB CONNECT
// =========================

try {

    $conn = getDbConnection();

} catch (
    mysqli_sql_exception $e
) {

    echo json_encode([
        "success" => false,

        "message" =>
            "Database connection failed.",

        "details" =>
            $e->getMessage()
    ]);

    exit;
}

// =========================
// TABLE NAME
// =========================
$tpfix = getTablePrefix();

$table = $tpfix . "admin";

// =========================
// CHECK IF ALREADY INSTALLED
// =========================

$checkTable =
    $conn->query(
        "SHOW TABLES LIKE '{$table}'"
    );

if (
    $checkTable &&
    $checkTable->num_rows > 0
) {

    echo json_encode([
        "success" => false,

        "message" =>
            "Admin is already configured.",

        "error_type" =>
            "ADMIN_ALREADY_CONFIGURED"
    ]);

    exit;
}


// =========================
// CREATE TABLE IF NOT EXISTS
// =========================

$createTableSql = "
CREATE TABLE IF NOT EXISTS {$table} (

    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(50) NULL,

    password VARCHAR(255) NULL,

    site_title VARCHAR(255) NULL,

    favicon_url VARCHAR(500) NULL,

    content LONGTEXT NULL

)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci
";

$conn->query(
    $createTableSql
);

// =========================
// HASH PASSWORD
// =========================

$hashedPassword =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );

// =========================
// INSERT ADMIN
// =========================

$stmt = $conn->prepare("
INSERT INTO {$table}
(
    username,
    password,
    site_title,
    favicon_url
)
VALUES
(
    ?,
    ?,
    ?,
    ?
)
");

$stmt->bind_param(
    "ssss",
    $username,
    $hashedPassword,
    $siteTitle,
    $faviconUrl
);

$success =
    $stmt->execute();

// =========================
// RESPONSE
// =========================

if (!$success) {

    echo json_encode([
        "success" => false,

        "message" =>
            "Failed to save admin data.",

        "details" =>
            $stmt->error
    ]);

    exit;
}

echo json_encode([
    "success" => true,

    "message" =>
        "Portfolio installed successfully."
]);

$adminId = $stmt->insert_id;
// echo "[[{$adminId}]]";
$_SESSION["admin_id"] = $adminId;
$_SESSION["admin_username"] = $username;
$_SESSION["site_title"] = $siteTitle;
$_SESSION["logged_in"] = true;


