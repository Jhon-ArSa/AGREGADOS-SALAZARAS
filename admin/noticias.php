<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id        = (int)($_POST['id'] ?? 0);
        $titulo    = trim($_POST['titulo']    ?? '');
        $resumen   = trim($_POST['resumen']   ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        $activo    = isset($_POST['activo']) ? 1 : 0;

        if ($titulo === '') {
            flash('error', 'El título es obligatorio.');
        } else {
            $img = upload_img('imagen', 'noticias');
            if ($id > 0) {
                if ($img) {
                    $pdo->prepare("UPDATE noticias SET titulo=?,resumen=?,contenido=?,imagen=?,activo=? WHERE id=?")
                        ->execute([$titulo,$resumen,$contenido,$img,$activo,$id]);
                } else {
                    $pdo->prepare("UPDATE noticias SET titulo=?,resumen=?,contenido=?,activo=? WHERE id=?")
                        ->execute([$titulo,$resumen,$contenido,$activo,$id]);
                }
                flash('success', 'Noticia actualizada correctamente.');
            } else {
                $pdo->prepare("INSERT INTO noticias (titulo,resumen,contenido,imagen,activo) VALUES (?,?,?,?,?)")
                    ->execute([$titulo,$resumen,$contenido,$img,$activo]);
                flash('success', 'Noticia publicada correctamente.');
            }
        }
    } elseif ($action === 'toggle') {
        $pdo->prepare("UPDATE noticias SET activo = 1 - activo WHERE id = ?")->execute([(int)$_POST['id']]);
        flash('success', 'Estado actualizado.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM noticias WHERE id = ?")->execute([(int)$_POST['id']]);
        flash('success', 'Noticia eliminada.');
    }

    header('Location: noticias.php');
    exit;
}

$editItem = null;
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $st = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
    $st->execute([(int)$_GET['id']]);
    $editItem = $st->fetch() ?: null;
}
$noticias = $pdo->query("SELECT * FROM noticias ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'Noticias';
require_once __DIR__ . '/includes/header.php';
$f = get_flash();
?>

<?php if ($f): ?>
<div class="alert alert-<?= $f['type'] ?>">
  <i class="ri-<?= $f['type']==='success'?'check':'error-warning' ?>-line"></i>
  <?= htmlspecialchars($f['msg']) ?>
</div>
<?php endif; ?>

<div class="panel">
  <div class="ph">
    <h2><?= $editItem ? 'Editar Noticia' : 'Nueva Noticia' ?></h2>
    <?php if ($editItem): ?>
    <a href="noticias.php" class="btn btn-ghost btn-sm"><i class="ri-close-line"></i> Cancelar</a>
    <?php endif; ?>
  </div>
  <div class="pb">
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)($editItem['id'] ?? 0) ?>">

      <div style="display:grid;gap:18px">
        <div class="fg">
          <label>Título *</label>
          <input type="text" name="titulo" class="fc" required
                 value="<?= htmlspecialchars($editItem['titulo'] ?? '') ?>">
        </div>
        <div class="fg">
          <label>Resumen <span style="font-weight:400;color:#64748b">(se muestra en la tarjeta del sitio)</span></label>
          <textarea name="resumen" class="fc" rows="3"><?= htmlspecialchars($editItem['resumen'] ?? '') ?></textarea>
        </div>
        <div class="fg">
          <label>Contenido completo <span style="font-weight:400;color:#64748b">(opcional)</span></label>
          <textarea name="contenido" class="fc" rows="7"><?= htmlspecialchars($editItem['contenido'] ?? '') ?></textarea>
        </div>
        <div class="fgrid">
          <div class="fg">
            <label>Imagen <?= $editItem ? '<span style="font-weight:400;color:#64748b">(vacío = no cambiar)</span>' : '' ?></label>
            <input type="file" name="imagen" class="fc" accept="image/jpeg,image/png,image/webp">
            <small>Máx. 5 MB · JPG, PNG, WEBP</small>
            <?php if (!empty($editItem['imagen'])): ?>
            <img src="../<?= htmlspecialchars($editItem['imagen']) ?>" class="thumb" style="margin-top:8px" alt="imagen actual">
            <?php endif; ?>
          </div>
          <div class="fg" style="display:flex;align-items:center;gap:10px;padding-top:26px">
            <input type="checkbox" id="activo" name="activo" value="1"
                   style="width:18px;height:18px;accent-color:#ff6b35;cursor:pointer"
                   <?= ($editItem['activo'] ?? 1) ? 'checked' : '' ?>>
            <label for="activo" style="margin:0;cursor:pointer">Visible en el sitio web</label>
          </div>
        </div>
      </div>

      <div style="margin-top:22px">
        <button type="submit" class="btn btn-primary">
          <i class="ri-save-line"></i>
          <?= $editItem ? 'Guardar cambios' : 'Publicar noticia' ?>
        </button>
      </div>
    </form>
  </div>
</div>

<div class="panel">
  <div class="ph">
    <h2>Noticias publicadas
      <span style="color:#64748b;font-size:.85rem;font-weight:400">(<?= count($noticias) ?>)</span>
    </h2>
  </div>
  <div class="tw">
    <table>
      <thead>
        <tr>
          <th>Imagen</th><th>Título</th><th>Resumen</th>
          <th>Fecha</th><th>Estado</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$noticias): ?>
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px">
          Aún no hay noticias. Crea la primera arriba.
        </td></tr>
      <?php else: ?>
      <?php foreach ($noticias as $n): ?>
        <tr>
          <td>
            <?php if ($n['imagen']): ?>
            <img src="../<?= htmlspecialchars($n['imagen']) ?>" class="thumb" alt="">
            <?php else: ?>
            <div class="nothumb"><i class="ri-image-line"></i></div>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($n['titulo']) ?></strong></td>
          <td style="max-width:220px;color:#64748b">
            <?= htmlspecialchars(mb_substr($n['resumen'] ?? '', 0, 65)) ?>
            <?= mb_strlen($n['resumen'] ?? '') > 65 ? '…' : '' ?>
          </td>
          <td><?= date('d/m/Y', strtotime($n['created_at'])) ?></td>
          <td><span class="badge badge-<?= $n['activo']?'on':'off' ?>"><?= $n['activo']?'Activo':'Inactivo' ?></span></td>
          <td>
            <div class="tda">
              <a href="?action=edit&id=<?= $n['id'] ?>" class="btn btn-warning btn-sm">
                <i class="ri-edit-line"></i> Editar
              </a>
              <form method="post" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm">
                  <i class="ri-eye-<?= $n['activo']?'off':'' ?>-line"></i>
                  <?= $n['activo']?'Ocultar':'Mostrar' ?>
                </button>
              </form>
              <form method="post" style="display:inline"
                    onsubmit="return confirm('¿Eliminar esta noticia?')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
