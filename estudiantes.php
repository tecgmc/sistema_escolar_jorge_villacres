
<?php
require_once __DIR__ . "/auth.php";
require_roles(["Administrador", "Profesor"]);

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && current_role() === "Administrador") {
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $clave = $_POST["clave"] ?? "";
    $curso_id = (int)($_POST["curso_id"] ?? 0);

    if ($nombre !== "" && $apellido !== "" && filter_var($correo, FILTER_VALIDATE_EMAIL) && $clave !== "") {
        $conn->begin_transaction();

        try {
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            $rol_id = 3;

            $s = $conn->prepare(
                "INSERT INTO usuarios (nombre, apellido, correo, password, rol_id, estado)
                 VALUES (?, ?, ?, ?, ?, 'Activo')"
            );
            $s->bind_param("ssssi", $nombre, $apellido, $correo, $hash, $rol_id);
            $s->execute();

            $usuario_id = $conn->insert_id;

            $s = $conn->prepare(
                "INSERT INTO estudiantes (usuario_id, curso_id)
                 VALUES (?, NULLIF(?, 0))"
            );
            $s->bind_param("ii", $usuario_id, $curso_id);
            $s->execute();

            $conn->commit();
            $msg = "Estudiante registrado correctamente.";
        } catch (Throwable $e) {
            $conn->rollback();
            $msg = "No se pudo registrar. Verifica que el correo no esté registrado.";
        }
    } else {
        $msg = "Completa todos los campos correctamente.";
    }
}

$cursos = $conn->query("SELECT id, nombre, nivel, paralelo FROM cursos ORDER BY nombre");
$rows = $conn->query(
    "SELECT e.id, u.nombre, u.apellido, u.correo, c.nombre AS curso
     FROM estudiantes e
     JOIN usuarios u ON u.id = e.usuario_id
     LEFT JOIN cursos c ON c.id = e.curso_id
     ORDER BY u.apellido, u.nombre"
);

require_once __DIR__ . "/header.php";
?>

<div class="mb-3">
    <span class="eyebrow">GESTIÓN ACADÉMICA</span>
    <h2 class="fw-bold">Estudiantes</h2>
</div>

<?php if ($msg): ?>
    <div class="alert alert-info"><?= e($msg) ?></div>
<?php endif; ?>

<?php if (current_role() === "Administrador"): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold">Registrar estudiante</h5>

        <form method="post" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Nombres</label>
                <input class="form-control" name="nombre" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Apellidos</label>
                <input class="form-control" name="apellido" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Correo electrónico</label>
                <input class="form-control" type="email" name="correo" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="clave" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Curso</label>
                <select class="form-select" name="curso_id">
                    <option value="0">Sin asignar</option>
                    <?php if ($cursos): ?>
                        <?php while ($c = $cursos->fetch_assoc()): ?>
                            <option value="<?= e($c["id"]) ?>">
                                <?= e($c["nombre"] . " - " . $c["nivel"] . " " . $c["paralelo"]) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Guardar estudiante</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Listado de estudiantes</h5>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Correo</th>
                        <th>Curso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rows): ?>
                        <?php while ($r = $rows->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($r["id"]) ?></td>
                                <td><?= e($r["nombre"]) ?></td>
                                <td><?= e($r["apellido"]) ?></td>
                                <td><?= e($r["correo"]) ?></td>
                                <td><?= e($r["curso"] ?? "Sin asignar") ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>
