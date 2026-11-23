<?php
require_once "config/auth.php"; require_roles(["Administrador"]);
$msg="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
$n=trim($_POST["nombre"]??"");$a=trim($_POST["apellido"]??"");$mail=trim($_POST["correo"]??"");$esp=trim($_POST["especialidad"]??"");
if($n&&$a&&filter_var($mail,FILTER_VALIDATE_EMAIL)){
$hash=password_hash("Profesor2026*",PASSWORD_DEFAULT);$conn->begin_transaction();
try{$s=$conn->prepare("INSERT INTO usuarios(nombre,apellido,correo,clave,rol_id) VALUES(?,?,?,?,2)");$s->bind_param("ssss",$n,$a,$mail,$hash);$s->execute();$uid=$conn->insert_id;$s=$conn->prepare("INSERT INTO profesores(usuario_id,especialidad) VALUES(?,?)");$s->bind_param("is",$uid,$esp);$s->execute();$conn->commit();$msg="Profesor registrado. Contraseña inicial: Profesor2026*";}catch(Throwable $e){$conn->rollback();$msg="No se pudo registrar. Verifica que el correo no esté repetido.";}
}else $msg="Completa los datos correctamente.";
}
$rows=$conn->query("SELECT p.id,u.nombre,u.apellido,u.correo,p.especialidad FROM profesores p JOIN usuarios u ON u.id=p.usuario_id ORDER BY p.id DESC");
include "includes/header.php"; ?>
<div class="mb-3"><span class="eyebrow">GESTIÓN ACADÉMICA</span><h2 class="fw-bold">Profesores</h2></div>
<?php if($msg):?><div class="alert alert-info"><?=e($msg)?></div><?php endif;?>
<div class="card border-0 shadow-sm mb-4"><div class="card-body"><h5 class="fw-bold">Registrar profesor</h5><form method="post" class="row g-3">
<div class="col-md-3"><label class="form-label">Nombres</label><input class="form-control" name="nombre" required></div><div class="col-md-3"><label class="form-label">Apellidos</label><input class="form-control" name="apellido" required></div><div class="col-md-3"><label class="form-label">Correo</label><input class="form-control" type="email" name="correo" required></div><div class="col-md-3"><label class="form-label">Especialidad</label><input class="form-control" name="especialidad"></div><div class="col-12"><button class="btn btn-primary">Guardar profesor</button></div></form></div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>#</th><th>Profesor</th><th>Correo</th><th>Especialidad</th></tr></thead><tbody><?php while($r=$rows->fetch_assoc()):?><tr><td><?=$r["id"]?></td><td><?=e($r["nombre"]." ".$r["apellido"])?></td><td><?=e($r["correo"])?></td><td><?=e($r["especialidad"])?></td></tr><?php endwhile;?></tbody></table></div></div>
<?php include "includes/footer.php"; ?>
