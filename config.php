<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('ROOT_URL', 'http://localhost/opg/');

define('ORDER_PREFIX', 'OPGO');

define('UPLOAD_IMAGE_FILE_SIZE', 300000);  //KB
define('UPLOAD_IMAGE_FILE_FORMATS', ['jpg', 'jpeg', 'png', 'gif']);

header("Content-Type: text/html; charset=utf-8");

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'opg');
 
/* Attempt to connect to MySQL database */
try{
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>