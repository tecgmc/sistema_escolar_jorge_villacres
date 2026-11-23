<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "escuela_db";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("No se pudo conectar con MySQL: " . htmlspecialchars($conn->connect_error));
}
$conn->set_charset("utf8mb4");
