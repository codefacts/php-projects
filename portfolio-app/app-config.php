<?php

if (basename($_SERVER["PHP_SELF"]) === basename(__FILE__)) {
    http_response_code(403);

    exit("Access denied");
}

return [
    "db_host" => "localhost",

    "db_user" => "user",

    "db_password" => "123",

    "db_name" => "portfolio",

    "db_table_prefix" => "pt_"
];
