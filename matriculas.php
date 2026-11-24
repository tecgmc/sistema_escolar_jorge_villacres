
<?php
require_once __DIR__ . "/auth.php";
require_login();

$msg = "";
$role = current_role();

if ($_SERVER["REQUEST_METHOD"] === "POST" && $role === "Estudiante") {
    $uid = (int) $_SESSION["usuario"]["id"];

    $s = $conn->prepare("SELECT id FROM estudiantes WHERE usuario_id = ?");
    $s->bind_param("i", $uid);
    $s->execute();
    $st = $s->get_result()->fetch_assoc();

    if ($st) {
        $eid = (int) $st["id"];

        $q = $conn->prepare(
            "INSERT INTO solicitudes_matricula
            (estudiante_id, estado, observacion)
            VALUES (?, 'Pendiente', 'Solicitud enviada desde el sistema')"
        );

        $q->bind_param("i", $eid);

        $msg = $q->execute()
            ? "Solicitud de matrícula enviada."
            : "No se pudo enviar la solicitud.";
    } else {
        $msg = "No existe una ficha de estudiante asociada a este usuario.";
    }
}

if ($role === "Estudiante") {
    $uid = (int) $_SESSION["usuario"]["id"];

    $s = $conn->prepare(
        "SELECT sm.*
         FROM solicitudes_matricula sm
         JOIN estudiantes e ON e.id = sm.estudiante_id
         WHERE e.usuario_id = ?
         ORDER BY sm.fecha_solicitud DESC"
    );

    $s->bind_param("i", $uid);
    $s->execute();
    $rows = $s->get_result();
} else {
    $rows = $conn->query(
        "SELECT sm.*, u.nombre, u.apellido
         FROM solicitudes_matricula sm
         JOIN estudiantes e ON e.id = sm.estudiante_id
         JOIN usuarios u ON u.id = e.usuario_id
         ORDER BY sm.fecha_solicitud DESC"
    );
}

require_once __DIR__ . "/header.php";
?>

<div class="mb-3">
    <span class="eyebrow">GESTIÓN DE MATRÍCULAS</span>
    <h2 class="fw-bold">Solicitudes de matrícula</h2>
</div>

<?php if ($msg): ?>
    <div class="alert alert-info"><?= e($msg) ?></div>
<?php endif; ?>

<?php if ($role === "Estudiante"): ?>
    <form method="post" class="mb-4">
        <button type="submit" class="btn btn-primary">
            Enviar solicitud de matrícula
        </button>
    </form>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <?php if ($role !== "Estudiante"): ?>
                        <th>Estudiante</th>
                    <?php endif; ?>
                    <th>Fecha de solicitud</th>
                    <th>Estado</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows): ?>
                    <?php while ($r = $rows->fetch_assoc()): ?>
                        <tr>
                            <?php if ($role !== "Estudiante"): ?>
                                <td><?= e($r["nombre"] . " " . $r["apellido"]) ?></td>
                            <?php endif; ?>
                            <td><?= e($r["fecha_solicitud"]) ?></td>
                            <td>
                                <span class="badge text-bg-warning">
                                    <?= e($r["estado"]) ?>
                                </span>
                            </td>
                            <td><?= e($r["observacion"]) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>
