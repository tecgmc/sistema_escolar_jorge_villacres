
<?php
require_once __DIR__ . "/auth.php";
require_roles(["Administrador"]);

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $nivel = trim($_POST["nivel"] ?? "");
    $paralelo = trim($_POST["paralelo"] ?? "");
    $periodo = trim($_POST["periodo_lectivo"] ?? "2026-2027");

    if ($nombre !== "" && $nivel !== "" && $paralelo !== "") {
        $s = $conn->prepare(
            "INSERT INTO cursos (nombre, nivel, paralelo, periodo_lectivo)
             VALUES (?, ?, ?, ?)"
        );

        if ($s) {
            $s->bind_param("ssss", $nombre, $nivel, $paralelo, $periodo);

            $msg = $s->execute()
                ? "Curso registrado correctamente."
                : "No se pudo registrar el curso.";
        } else {
            $msg = "No se pudo preparar el registro.";
        }
    } else {
        $msg = "Completa todos los campos.";
    }
}

$rows = $conn->query(
    "SELECT * FROM cursos ORDER BY nivel, paralelo"
);

require_once __DIR__ . "/header.php";
?>

<div class="mb-3">
    <span class="eyebrow">ORGANIZACIÓN ACADÉMICA</span>
    <h2 class="fw-bold">Cursos</h2>
</div>

<?php if ($msg): ?>
    <div class="alert alert-info"><?= e($msg) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold">Registrar curso</h5>

        <form method="post" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Nombre del curso</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Nivel</label>
                <input type="text" name="nivel" class="form-control" placeholder="Ej. 4.º EGB" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Paralelo</label>
                <input type="text" name="paralelo" class="form-control" placeholder="Ej. A" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Período lectivo</label>
                <input type="text" name="periodo_lectivo" class="form-control" value="2026-2027" required>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Guardar curso
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Listado de cursos</h5>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Curso</th>
                        <th>Nivel</th>
                        <th>Paralelo</th>
                        <th>Período lectivo</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($rows): ?>
                        <?php while ($r = $rows->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($r["id"]) ?></td>
                                <td><?= e($r["nombre"]) ?></td>
                                <td><?= e($r["nivel"]) ?></td>
                                <td><?= e($r["paralelo"]) ?></td>
                                <td><?= e($r["periodo_lectivo"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>
