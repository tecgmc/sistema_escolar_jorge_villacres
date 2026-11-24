
<?php
require_once __DIR__ . "/auth.php";
require_roles(["Administrador"]);

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $n = trim($_POST["nombre"] ?? "");
    $a = trim($_POST["apellido"] ?? "");
    $mail = trim($_POST["correo"] ?? "");
    $esp = trim($_POST["especialidad"] ?? "");

    if (
        $n !== "" &&
        $a !== "" &&
        filter_var($mail, FILTER_VALIDATE_EMAIL) &&
        $esp !== ""
    ) {
        $conn->begin_transaction();

        try {
            $hash = password_hash("Profesor2026*", PASSWORD_DEFAULT);
            $rol_id = 2;

            $s = $conn->prepare(
                "INSERT INTO usuarios
                (nombre, apellido, correo, password, rol_id, estado)
                VALUES (?, ?, ?, ?, ?, 'Activo')"
            );

            $s->bind_param("ssssi", $n, $a, $mail, $hash, $rol_id);
            $s->execute();

            $uid = $conn->insert_id;

            $s = $conn->prepare(
                "INSERT INTO profesores (usuario_id, especialidad)
                 VALUES (?, ?)"
            );

            $s->bind_param("is", $uid, $esp);
            $s->execute();

            $conn->commit();
            $msg = "Profesor registrado correctamente. Contraseña inicial: Profesor2026*";

        } catch (Throwable $e) {
            $conn->rollback();
            $msg = "No se pudo registrar. Verifica que el correo no esté repetido.";
        }

    } else {
        $msg = "Completa los datos correctamente.";
    }
}

$rows = $conn->query(
    "SELECT p.id, u.nombre, u.apellido, u.correo, p.especialidad
     FROM profesores p
     JOIN usuarios u ON u.id = p.usuario_id
     ORDER BY p.id DESC"
);

require_once __DIR__ . "/header.php";
?>

<div class="mb-3">
    <span class="eyebrow">GESTIÓN ACADÉMICA</span>
    <h2 class="fw-bold">Profesores</h2>
</div>

<?php if ($msg): ?>
    <div class="alert alert-info"><?= e($msg) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold">Registrar profesor</h5>

        <form method="post" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Nombres</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Apellidos</label>
                <input type="text" name="apellido" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="correo" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Especialidad</label>
                <input type="text" name="especialidad" class="form-control" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    Guardar profesor
                </button>
            </div>
        </form>

        <small class="text-muted">
            Contraseña inicial para los nuevos profesores: Profesor2026*
        </small>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Listado de profesores</h5>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Profesor</th>
                        <th>Correo</th>
                        <th>Especialidad</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($rows): ?>
                        <?php while ($r = $rows->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($r["id"]) ?></td>
                                <td><?= e($r["nombre"] . " " . $r["apellido"]) ?></td>
                                <td><?= e($r["correo"]) ?></td>
                                <td><?= e($r["especialidad"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>

