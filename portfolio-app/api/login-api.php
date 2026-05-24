<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "./cors.php";

session_start();

$data = json_decode(file_get_contents("php://input"), true);

$username = $data["username"];
$password = $data["password"];

$conn = new mysqli("localhost", "user", "123", "portfolio");

$stmt = $conn->prepare("SELECT * FROM admin WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

if ($result && password_verify($password, $result["password"])) {

    $_SESSION["admin"] = $result["id"];

    echo json_encode([
        "success" => true
    ]);

} else {

    echo json_encode([
        "success" => false
    ]);
}