<?php
require_once "config/auth.php";
require_login();
$counts = [];
$tables = ["estudiantes"=>"Estudiantes","profesores"=>"Profesores","cursos"=>"Cursos","asignaturas"=>"Asignaturas","calificaciones"=>"Calificaciones"];
foreach ($tables as $table=>$label) {
    $res = $conn->query("SELECT COUNT(*) AS total FROM `$table`");
    $counts[$label] = $res ? (int)$res->fetch_assoc()["total"] : 0;
}
$school = $conn->query("SELECT nombre, periodo_lectivo FROM institucion WHERE id=1")->fetch_assoc();
include "includes/header.php";
?>
<div class="welcome-panel mb-4"><div><span class="eyebrow">PANEL PRINCIPAL</span>
<h2 class="fw-bold mb-2">Bienvenido/a, <?= e($_SESSION["usuario"]["nombre"]) ?></h2>
<p class="mb-0"><?= e($school["nombre"] ?? "Institución educativa") ?> · Período <?= e($school["periodo_lectivo"] ?? "2026 - 2027") ?></p></div>
<div class="welcome-mark">✦</div></div>
<div class="row g-3 mb-4">
<?php foreach ($counts as $label=>$total): ?><div class="col-6 col-lg">
<div class="card stat-card h-100"><div class="card-body"><div class="stat-number"><?= $total ?></div><div class="text-secondary"><?= e($label) ?></div></div></div>
</div><?php endforeach; ?></div>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
<h5 class="fw-bold">Accesos del sistema</h5><p class="text-secondary">Selecciona un módulo del menú superior para administrar o consultar la información escolar.</p>
<div class="d-flex flex-wrap gap-2">
<a class="btn btn-outline-primary" href="estudiantes.php">Estudiantes</a>
<a class="btn btn-outline-primary" href="profesores.php">Profesores</a>
<a class="btn btn-outline-primary" href="cursos.php">Cursos</a>
<a class="btn btn-outline-primary" href="asignaturas.php">Asignaturas</a>
<a class="btn btn-outline-primary" href="calificaciones.php">Calificaciones</a>
<a class="btn btn-outline-primary" href="calendario.php">Calendario</a>
<a class="btn btn-outline-primary" href="matriculas.php">Matrículas</a>
</div></div></div>
<?php include "includes/footer.php"; ?>
