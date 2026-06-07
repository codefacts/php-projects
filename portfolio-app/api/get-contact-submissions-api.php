<?php
require_once "./cors.php";
require_once './auth.php';
require_once "../db.php";

// ============================
// ENABLE EXCEPTIONS
// ============================
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    // ============================
    // DB CONNECTION
    // ============================
    $conn = getDbConnection();

    $conn->set_charset("utf8mb4");

    // ============================
    // ENSURE TABLE EXISTS
    // (safe even if already created)
    // ============================
    $tpfix = getTablePrefix();
    $conn->query("
        CREATE TABLE IF NOT EXISTS {$tpfix}contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            data LONGTEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // ============================
    // FETCH DATA
    // ============================
    $tpfix = getTablePrefix();

    $result = $conn->query("
        SELECT id, data, created_at
        FROM {$tpfix}contact_submissions
        ORDER BY id DESC
    ");

    $submissions = [];

    while ($row = $result->fetch_assoc()) {

        $decoded = json_decode($row['data'], true);

        // fallback if JSON is broken
        if (json_last_error() !== JSON_ERROR_NONE) {
            $decoded = [
                "raw_data" => $row['data']
            ];
        }

        $submissions[] = [
            "id" => (int)$row["id"],
            "data" => $decoded,
            "created_at" => $row["created_at"]
        ];
    }

    // ============================
    // RESPONSE
    // ============================
    echo json_encode([
        "success" => true,
        "data" => $submissions
    ]);

} catch (mysqli_sql_exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Server error",
        "error" => $e->getMessage()
    ]);

} finally {

    if (isset($conn) && $conn) {
        $conn->close();
    }
}