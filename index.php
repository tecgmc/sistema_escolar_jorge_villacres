<?php
require_once "config/auth.php";
if (!empty($_SESSION["usuario"])) {
    header("Location: dashboard.php");
    exit;
}
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"] ?? "");
    $clave = $_POST["clave"] ?? "";
    $stmt = $conn->prepare("SELECT u.id, u.nombre, u.apellido, u.correo, u.clave, r.nombre AS rol
        FROM usuarios u INNER JOIN roles r ON r.id=u.rol_id
        WHERE u.correo=? AND u.estado='Activo' LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($clave, $user["clave"])) {
        $_SESSION["usuario"] = [
            "id" => $user["id"], "nombre" => $user["nombre"],
            "apellido" => $user["apellido"], "correo" => $user["correo"],
            "rol" => $user["rol"]
        ];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Correo o contraseña incorrectos.";
}
?>
<!doctype html>
<html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Acceso | Sistema Escolar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet"></head>
<body class="login-bg">
<div class="container min-vh-100 d-flex align-items-center justify-content-center">
<div class="card login-card shadow-lg border-0"><div class="card-body p-4 p-md-5">
<div class="text-center mb-4"><div class="school-icon">✦</div>
<h4 class="fw-bold mt-3">ESCUELA DE EDUCACIÓN BÁSICA<br>JORGE VILLACRÉS MOSCOSO</h4>
<p class="text-secondary mb-0">Sistema de gestión escolar · Período 2026 - 2027</p></div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<div class="mb-3"><label class="form-label">Correo electrónico</label>
<input class="form-control form-control-lg" type="email" name="correo" required></div>
<div class="mb-4"><label class="form-label">Contraseña</label>
<input class="form-control form-control-lg" type="password" name="clave" required></div>
<button class="btn btn-primary btn-lg w-100">Ingresar al sistema</button>
</form><p class="small text-secondary text-center mt-4 mb-0">Acceso exclusivo para usuarios registrados.</p>
</div></div></div></body></html>
