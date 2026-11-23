<?php
require_once __DIR__ . "/../config/auth.php";
$rol = current_role();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sistema de Gestión Escolar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet"></head><body>
<nav class="navbar navbar-expand-lg navbar-dark topbar"><div class="container-fluid px-lg-4">
<a class="navbar-brand fw-bold" href="dashboard.php">✦ Sistema Escolar</a>
<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="nav"><ul class="navbar-nav me-auto">
<li class="nav-item"><a class="nav-link" href="dashboard.php">Inicio</a></li>
<?php if (in_array($rol, ["Administrador","Profesor"], true)): ?>
<li class="nav-item"><a class="nav-link" href="estudiantes.php">Estudiantes</a></li>
<?php endif; ?>
<?php if ($rol === "Administrador"): ?>
<li class="nav-item"><a class="nav-link" href="profesores.php">Profesores</a></li>
<li class="nav-item"><a class="nav-link" href="cursos.php">Cursos</a></li>
<li class="nav-item"><a class="nav-link" href="asignaturas.php">Asignaturas</a></li>
<?php endif; ?>
<li class="nav-item"><a class="nav-link" href="calificaciones.php">Calificaciones</a></li>
<li class="nav-item"><a class="nav-link" href="calendario.php">Calendario</a></li>
<li class="nav-item"><a class="nav-link" href="matriculas.php">Matrículas</a></li>
</ul><span class="navbar-text me-3"><?= e($_SESSION["usuario"]["nombre"] . " · " . $rol) ?></span>
<a class="btn btn-sm btn-light" href="logout.php">Cerrar sesión</a></div></div></nav>
<main class="container-fluid px-lg-4 py-4">
