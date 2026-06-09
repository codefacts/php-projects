<?php

require_once "./cors.php";
require_once "./api-logger.php";

logApiRequest();

// Prevent PHP HTML warnings
ini_set("display_errors", 0);

error_reporting(E_ALL);

// =========================
// READ JSON BODY
// =========================

$data = json_decode(
    file_get_contents("php://input"),
    true
);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "No data received"
    ]);

    exit;
}

// =========================
// ACCESS FIELDS
// =========================

$dbName = trim($data["dbName"] ?? "");
$dbUser = trim($data["dbUser"] ?? "");
$dbPassword = trim($data["dbPassword"] ?? "");
$dbHost = trim($data["dbHost"] ?? "");
$tablePrefix = trim($data["tablePrefix"] ?? "");

// =========================
// VALIDATION
// =========================

if (
    !$dbName ||
    !$dbUser ||
    !$dbHost ||
    !$tablePrefix
) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);

    exit;
}

// =========================
// TEST DB CONNECTION
// =========================

// =========================
// TEST MYSQL LOGIN
// =========================

try {

    // NO DATABASE YET
    $conn = new mysqli(
        $dbHost,
        $dbUser,
        $dbPassword
    );

} catch (mysqli_sql_exception $e) {

    echo json_encode([
        "success" => false,

        "message" =>
            "Database authentication failed.",

        "details" =>
            $e->getMessage(),

        "error_type" =>
            "DB_AUTH_FAILED"
    ]);

    exit;
}

// =========================
// CHECK DATABASE ACCESS
// =========================

try {

    if (!$conn->select_db($dbName)) {

        echo json_encode([
            "success" => false,

            "message" =>
                "Database '{$dbName}' does not exist or cannot be accessed.",

            "details" =>
                $conn->error,

            "error_type" =>
                "INVALID_DATABASE"
        ]);

        exit;
    }

} catch (mysqli_sql_exception $e) {

    echo json_encode([
        "success" => false,

        "message" =>
            "Failed to access database '{$dbName}'.",

        "details" =>
            $e->getMessage(),

        "error_type" =>
            "INVALID_DATABASE"
    ]);

    exit;
}

// =========================
// CREATE CONFIG CONTENT
// =========================

$configContent = <<<PHP
<?php

if (basename(\$_SERVER["PHP_SELF"]) === basename(__FILE__)) {
    http_response_code(403);

    exit("Access denied");
}

return [
    "db_host" => "{$dbHost}",

    "db_user" => "{$dbUser}",

    "db_password" => "{$dbPassword}",

    "db_name" => "{$dbName}",

    "db_table_prefix" => "{$tablePrefix}"
];

PHP;

$configFilePath =
    __DIR__ . "/../app-config.php";

if (
    file_exists(
        $configFilePath
    )
) {

    echo json_encode([
        "success" => false,

        "message" =>
            "This application is already configured. Please delete app-config.php before running setup again.",

        "error_type" =>
            "APP_ALREADY_CONFIGURED"
    ]);

    exit;
}
    
// Prevent PHP warnings from breaking JSON
$result = @file_put_contents(
    $configFilePath,
    $configContent
);

if ($result === false) {

    $error = error_get_last();

    echo json_encode([
        "success" => false,

        "message" =>
            "Failed to create app-config.php",

        "details" =>
            $error["message"] ?? "Unknown file write error",

        "error_type" =>
            "CONFIG_WRITE_FAILED",

        "solution" =>
            "Please give write permission to your project directory."
    ]);

    exit;
}

// =========================
// SUCCESS
// =========================

echo json_encode([
    "success" => true,
    "message" =>
        "app-config.php created successfully"
]);