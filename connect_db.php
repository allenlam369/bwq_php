<?php

define('db_name', 'beach_data');
define('db_user', 'user');
define('db_password', 'pass');
define('db_host', '127.0.0.1:3307');

try {
  $conn = new pdo("mysql:host=" . db_host . "; dbname=" . db_name, db_user, db_password);
  $conn->setattribute($conn::ATTR_ERRMODE, $conn::ERRMODE_EXCEPTION);
  $conn->setattribute($conn::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
  echo "db connection failed: " . $e->getMessage();
}

