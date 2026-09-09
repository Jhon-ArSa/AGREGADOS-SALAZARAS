<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../db/config.php';

$cntMat = (int)$pdo->query("SELECT COUNT(*) FROM materiales WHERE activo=1")->fetchColumn();
$cntNot = (int)$pdo->query("SELECT COUNT(*) FROM noticias   WHERE activo=1")->fetchColumn();
$statCS = (int)$pdo->query("SELECT valor FROM estadisticas WHERE clave='clientes_satisfechos'")->fetchColumn();
$statPE = (int)$pdo->query("SELECT valor FROM estadisticas WHERE clave='proyectos_ejecutados'")->fetchColumn();
$recMat = $pdo->query("SELECT nombre, activo, created_at FROM materiales ORDER BY id DESC LIMIT 5")->fetchAll();
$recNot = $pdo->query("SELECT titulo, activo, created_at FROM noticias   ORDER BY id DESC LIMIT 5")->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
$f = get_flash();
?>

<?php if ($f): ?>
<div class="alert alert-<?= $f['type'] ?>">
  <i class="ri-<?= $f['type']==='success'?'check':'error-warning' ?>-line"></i>
  <?= htmlspecialchars($f['msg']) ?>
</div>
<?php endif; ?>

<div class="cards">
  <div class="sc"><div class="sc-icon"><i class="ri-stack-line"></i></div>
    <div><h3><?= $cntMat ?></h3><p>Materiales activos</p></div></div>
  <div class="sc"><div class="sc-icon"><i class="ri-newspaper-line"></i></div>
    <div><h3><?= $cntNot ?></h3><p>Noticias activas</p></div></div>
  <div class="sc"><div class="sc-icon"><i class="ri-user-heart-fill"></i></div>
    <div><h3><?= $statCS ?></h3><p>Clientes satisfechos</p></div></div>
  <div class="sc"><div class="sc-icon"><i class="ri-checkbox-circle-fill"></i></div>
    <div><h3><?= $statPE ?></h3><p>Proyectos ejecutados</p></div></div>
</div>

<div class="quick-grid">
  <a href="materiales.php" class="qa"><i class="ri-add-circle-line"></i>Agregar Material</a>
  <a href="noticias.php"   class="qa"><i class="ri-add-circle-line"></i>Nueva Noticia</a>
  <a href="estadisticas.php" class="qa"><i class="ri-edit-line"></i>Actualizar Estadísticas</a>
  <a href="../index.php" target="_blank" class="qa"><i class="ri-eye-line"></i>Ver Sitio Web</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

  <div class="panel">
    <div class="ph"><h2>Últimos Materiales</h2>
      <a href="materiales.php" class="btn btn-ghost btn-sm">Ver todos</a></div>
    <div class="tw"><table>
      <thead><tr><th>Nombre</th><th>Estado</th><th>Fecha</th></tr></thead>
      <tbody>
      <?php foreach ($recMat as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['nombre']) ?></td>
        <td><span class="badge badge-<?= $r['activo']?'on':'off' ?>"><?= $r['activo']?'Activo':'Inactivo' ?></span></td>
        <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$recMat): ?><tr><td colspan="3" style="color:#94a3b8;text-align:center;padding:20px">Sin materiales</td></tr><?php endif; ?>
      </tbody>
    </table></div>
  </div>

  <div class="panel">
    <div class="ph"><h2>Últimas Noticias</h2>
      <a href="noticias.php" class="btn btn-ghost btn-sm">Ver todas</a></div>
    <div class="tw"><table>
      <thead><tr><th>Título</th><th>Estado</th><th>Fecha</th></tr></thead>
      <tbody>
      <?php foreach ($recNot as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['titulo']) ?></td>
        <td><span class="badge badge-<?= $r['activo']?'on':'off' ?>"><?= $r['activo']?'Activo':'Inactivo' ?></span></td>
        <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$recNot): ?><tr><td colspan="3" style="color:#94a3b8;text-align:center;padding:20px">Sin noticias</td></tr><?php endif; ?>
      </tbody>
    </table></div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
