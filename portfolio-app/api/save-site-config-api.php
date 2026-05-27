<?php

session_start();
session_regenerate_id(true);

require_once "./cors.php";
require_once "../db.php";

header("Content-Type: application/json");

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

$table = "admin";

// =========================
// CREATE TABLE IF NOT EXISTS
// =========================

$createTableSql = "
CREATE TABLE IF NOT EXISTS {$table} (

    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(50) NULL,

    password VARCHAR(255) NULL,

    site_title VARCHAR(255) NULL,

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
// CLEAR OLD ADMIN
// =========================

$conn->query(
    "TRUNCATE TABLE {$table}"
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
    site_title
)
VALUES
(
    ?,
    ?,
    ?
)
");

$stmt->bind_param(
    "sss",
    $username,
    $hashedPassword,
    $siteTitle
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


