
<?php
require_once __DIR__ . "/auth.php";
require_roles(["Administrador", "Profesor"]);

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && current_role() === "Administrador") {
    $t = trim($_POST["titulo"] ?? "");
    $d = trim($_POST["descripcion"] ?? "");
    $f = $_POST["fecha"] ?? "";

    if ($t !== "" && $f !== "") {
        $s = $conn->prepare(
            "INSERT INTO calendario (titulo, descripcion, fecha)
             VALUES (?, ?, ?)"
        );

        if ($s) {
            $s->bind_param("sss", $t, $d, $f);

            $msg = $s->execute()
                ? "Evento agregado correctamente."
                : "No se pudo guardar el evento.";
        } else {
            $msg = "No se pudo preparar el registro.";
        }
    } else {
        $msg = "Completa el título y la fecha.";
    }
}

$rows = $conn->query("SELECT * FROM calendario ORDER BY fecha ASC");

require_once __DIR__ . "/header.php";
?>

<div class="mb-3">
    <span class="eyebrow">ACTIVIDADES INSTITUCIONALES</span>
    <h2 class="fw-bold">Calendario escolar</h2>
</div>

<?php if ($msg): ?>
    <div class="alert alert-info"><?= e($msg) ?></div>
<?php endif; ?>

<?php if (current_role() === "Administrador"): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold">Agregar evento</h5>

            <form method="post" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Título</label>
                    <input class="form-control" name="titulo" required>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Descripción</label>
                    <input class="form-control" name="descripcion">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Fecha</label>
                    <input class="form-control" type="date" name="fecha" required>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        Añadir
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Eventos programados</h5>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($rows): ?>
                        <?php while ($r = $rows->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($r["id"]) ?></td>
                                <td><?= e($r["titulo"]) ?></td>
                                <td><?= e($r["descripcion"]) ?></td>
                                <td><?= e($r["fecha"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>
