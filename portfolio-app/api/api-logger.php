<?php
require_once "../db.php";
/**
 * Logs API requests into database
 *
 * Requires:
 * - getDbConnection() function returning mysqli connection
 */

function logApiRequest()
{
    try {

        $conn = getDbConnection();

        if (!$conn) {
            return;
        }

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';

        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

        $forwardedIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        $queryString = $_SERVER['QUERY_STRING'] ?? '';

        $responseStatus = http_response_code();

        $sql = "
            INSERT INTO api_request_logs (
                request_method,
                request_uri,
                script_name,
                ip_address,
                forwarded_ip,
                user_agent,
                referer,
                query_string,
                response_status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return;
        }

        $stmt->bind_param(
            "ssssssssi",
            $requestMethod,
            $requestUri,
            $scriptName,
            $ipAddress,
            $forwardedIp,
            $userAgent,
            $referer,
            $queryString,
            $responseStatus
        );

        $stmt->execute();

        $stmt->close();

    } catch (Throwable $e) {

        /*
         * Never allow logging failures
         * to break the API.
         */

        error_log(
            'API Logger Error: ' . $e->getMessage()
        );
    }
}