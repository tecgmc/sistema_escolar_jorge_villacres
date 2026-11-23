<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/database.php";

function require_login() {
    if (empty($_SESSION["usuario"])) {
        header("Location: index.php");
        exit;
    }
}
function current_role() {
    return $_SESSION["usuario"]["rol"] ?? "";
}
function require_roles($roles) {
    require_login();
    if (!in_array(current_role(), $roles, true)) {
        http_response_code(403);
        exit("No tienes permisos para acceder a este módulo.");
    }
}
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
