<?php

require_once "./cors.php";

header("Content-Type: application/json");

// =========================
// CONFIG FILE PATH
// =========================

$configPath =
    __DIR__ . "/../app-config.php";

// =========================
// FILE EXISTS?
// =========================

if (!file_exists($configPath)) {

    echo json_encode([
        "success" => false,

        "error_type" =>
            "CONFIG_NOT_FOUND",

        "message" =>
            "app-config.php file does not exist."
    ]);

    exit;
}

// =========================
// LOAD CONFIG
// =========================

$config = require $configPath;

// =========================
// VALIDATE CONFIG STRUCTURE
// =========================

$requiredKeys = [
    "db_host",
    "db_user",
    "db_password",
    "db_name",
    "db_table_prefix"
];

foreach ($requiredKeys as $key) {

    if (!isset($config[$key])) {

        echo json_encode([
            "success" => false,

            "error_type" =>
                "INVALID_CONFIG",

            "message" =>
                "Missing config key: {$key}"
        ]);

        exit;
    }
}

// =========================
// TEST MYSQL LOGIN
// =========================

try {

    $conn = new mysqli(
        $config["db_host"],
        $config["db_user"],
        $config["db_password"]
    );

} catch (mysqli_sql_exception $e) {

    echo json_encode([
        "success" => false,

        "error_type" =>
            "DB_AUTH_FAILED",

        "message" =>
            "Database authentication failed.",

        "details" =>
            $e->getMessage()
    ]);

    exit;
}

// =========================
// CHECK DATABASE ACCESS
// =========================

try {

    $conn->select_db(
        $config["db_name"]
    );

} catch (mysqli_sql_exception $e) {

    echo json_encode([
        "success" => false,

        "error_type" =>
            "INVALID_DATABASE",

        "message" =>
            "Database '{$config["db_name"]}' does not exist or cannot be accessed.",

        "details" =>
            $e->getMessage()
    ]);

    exit;
}

// =========================
// SUCCESS
// =========================

echo json_encode([
    "success" => true,

    "message" =>
        "Configuration file is valid."
]);