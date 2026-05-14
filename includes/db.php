<?php
// includes/db.php
$host = 'localhost';
$db   = 'chain_of_store';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// PDO Connection (for files using PDO)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // We don't die here because some files might use mysqli instead
    $pdo_error = $e->getMessage();
}

// MySQLi Connection (for files using mysqli)
$mysqli_conn = new mysqli($host, $user, $pass, $db);

if ($mysqli_conn->connect_error) {
    $mysqli_error = $mysqli_conn->connect_error;
}

// If both fail, then we die
if (isset($pdo_error) && isset($mysqli_error)) {
    die("Database connection failed. <br>PDO: $pdo_error <br>MySQLi: $mysqli_error");
}
?>