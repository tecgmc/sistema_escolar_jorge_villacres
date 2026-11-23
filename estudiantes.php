<?php
require_once "config/auth.php";
require_roles(["Administrador","Profesor"]);
$msg="";
if ($_SERVER["REQUEST_METHOD"]==="POST" && current_role()==="Administrador") {
    $nombre=trim($_POST["nombre"]??""); $apellido=trim($_POST["apellido"]??"");
    $correo=trim($_POST["correo"]??""); $cedula=trim($_POST["cedula"]??"");
    $curso=(int)($_POST["curso_id"]??0);
    if ($nombre && $apellido && filter_var($correo,FILTER_VALIDATE_EMAIL)) {
        $hash=password_hash("Estudiante2026*",PASSWORD_DEFAULT);
        $conn->begin_transaction();
        try {
            $s=$conn->prepare("INSERT INTO usuarios(nombre,apellido,correo,clave,rol_id) VALUES(?,?,?,?,3)");
            $s->bind_param("ssss",$nombre,$apellido,$correo,$hash); $s->execute(); $uid=$conn->insert_id;
            $s=$conn->prepare("INSERT INTO estudiantes(usuario_id,curso_id,cedula) VALUES(?,?,?)");
            $s->bind_param("iis",$uid,$curso,$cedula); $s->execute();
            $conn->commit(); $msg="Estudiante registrado. Contraseña inicial: Estudiante2026*";
        } catch(Throwable $ex) { $conn->rollback(); $msg="No se pudo registrar. Verifica que el correo y la cédula no estén repetidos."; }
    } else $msg="Completa los campos obligatorios con un correo válido.";
}
$sql="SELECT e.id,u.nombre,u.apellido,u.correo,e.cedula,c.nombre AS curso FROM estudiantes e JOIN usuarios u ON u.id=e.usuario_id LEFT JOIN cursos c ON c.id=e.curso_id ORDER BY e.id DESC";
$rows=$conn->query($sql); $courses=$conn->query("SELECT id,nombre FROM cursos ORDER BY id");
include "includes/header.php";
?>
<div class="d-flex justify-content-between align-items-center mb-3"><div><span class="eyebrow">GESTIÓN ACADÉMICA</span><h2 class="fw-bold">Estudiantes</h2></div></div>
<?php if($msg): ?><div class="alert alert-info"><?=e($msg)?></div><?php endif; ?>
<?php if(current_role()==="Administrador"): ?>
<div class="card border-0 shadow-sm mb-4"><div class="card-body"><h5 class="fw-bold">Registrar estudiante</h5>
<form method="post" class="row g-3">
<div class="col-md-3"><label class="form-label">Nombres</label><input class="form-control" name="nombre" required></div>
<div class="col-md-3"><label class="form-label">Apellidos</label><input class="form-control" name="apellido" required></div>
<div class="col-md-3"><label class="form-label">Correo</label><input class="form-control" type="email" name="correo" required></div>
<div class="col-md-3"><label class="form-label">Cédula</label><input class="form-control" name="cedula"></div>
<div class="col-md-6"><label class="form-label">Curso</label><select class="form-select" name="curso_id"><option value="0">Sin asignar</option><?php while($c=$courses->fetch_assoc()): ?><option value="<?=$c["id"]?>"><?=e($c["nombre"])?></option><?php endwhile; ?></select></div>
<div class="col-12"><button class="btn btn-primary">Guardar estudiante</button></div></form></div></div>
<?php endif; ?>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>#</th><th>Estudiante</th><th>Correo</th><th>Cédula</th><th>Curso</th></tr></thead><tbody>
<?php while($r=$rows->fetch_assoc()): ?><tr><td><?=$r["id"]?></td><td><?=e($r["nombre"]." ".$r["apellido"])?></td><td><?=e($r["correo"])?></td><td><?=e($r["cedula"])?></td><td><?=e($r["curso"]??"Sin asignar")?></td></tr><?php endwhile; ?>
</tbody></table></div></div>
<?php include "includes/footer.php"; ?>
