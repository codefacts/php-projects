<?php

if (basename($_SERVER["PHP_SELF"]) === basename(__FILE__)) {
    http_response_code(403);

    exit("Access denied");
}

$config = require_once __DIR__ . "/app-config.php";

function getDbConnection() {
    global $config;

    $conn = new mysqli(
        $config["db_host"],
        $config["db_user"],
        $config["db_password"],
        $config["db_name"]
    );

    if ($conn->connect_error) {
        die(
            "DB Connection failed: " .
            $conn->connect_error
        );
    }

    return $conn;
}


function getTablePrefix() {
    global $config;
    return $config["db_table_prefix"];
}



