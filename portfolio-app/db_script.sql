CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255),
  content JSON
);

ALTER TABLE admin
ADD COLUMN site_title VARCHAR(255)
AFTER password;