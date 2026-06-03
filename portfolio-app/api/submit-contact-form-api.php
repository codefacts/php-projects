<?php
require_once "./cors.php";
header('Content-Type: application/json');
require_once "../db.php";

// ============================
// ENABLE MYSQLI EXCEPTIONS
// ============================
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    // ============================
    // DB CONNECTION
    // ============================
    $conn = getDbConnection();

    $conn->set_charset("utf8mb4");

    // ============================
    // CREATE TABLE (ONLY ONCE)
    // ============================
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            data LONGTEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $conn->query($createTableSQL);

    // ============================
    // READ INPUT JSON
    // ============================
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
        throw new Exception("Invalid JSON input");
    }

    // ============================
    // CONVERT TO JSON STRING
    // ============================
    $jsonData = json_encode($data);

    // ============================
    // INSERT INTO DATABASE
    // ============================
    $stmt = $conn->prepare("
        INSERT INTO contact_submissions (data)
        VALUES (?)
    ");

    $stmt->bind_param("s", $jsonData);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Message sent successfully"
    ]);

} catch (mysqli_sql_exception $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error occurred",
        "error" => $e->getMessage()
    ]);

} catch (Exception $e) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

} finally {

    if (isset($stmt) && $stmt) {
        $stmt->close();
    }

    if (isset($conn) && $conn) {
        $conn->close();
    }
}