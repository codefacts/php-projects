CREATE TABLE api_request_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    request_method VARCHAR(10) NOT NULL,
    request_uri VARCHAR(2048) NOT NULL,
    script_name VARCHAR(255),
    ip_address VARCHAR(45) NOT NULL,
    forwarded_ip VARCHAR(255),
    user_agent TEXT,
    referer TEXT,
    query_string TEXT,
    response_status SMALLINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE INDEX idx_request_time
ON api_request_logs (request_time);

CREATE INDEX idx_ip_address
ON api_request_logs (ip_address);

CREATE INDEX idx_script_name
ON api_request_logs (script_name);