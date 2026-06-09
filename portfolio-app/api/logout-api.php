<?php

require_once "./cors.php";
require_once "./api-logger.php";

logApiRequest();

session_start();

session_unset();
session_destroy();

echo json_encode([
    "success" => true
]);