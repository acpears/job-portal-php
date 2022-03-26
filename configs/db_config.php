<?php
// Load environnement variables for database connection
require dirname(__DIR__, 1) . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

$db = mysqli_connect($_ENV["DB_SERVER"], $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"], $_ENV["DB_DATABASE"]);
if ($db->connect_errno) {
    printf("Database connection failed: %s\n", $db->connect_error);
    exit();
}
