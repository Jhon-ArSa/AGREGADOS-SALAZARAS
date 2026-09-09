<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../db/config.php';

// ── POST: manejar acciones antes de cualquier output ─────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id     = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $desc   = trim($_POST['descripcion'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;
        $orden  = (int)($_POST['orden'] ?? 0);

        if ($nombre === '') {
            flash('error', 'El nombre es obligatorio.');
        } else {
            $img = upload_img('imagen', 'materiales');
            if ($id > 0) {
                if ($img) {
                    $pdo->prepare("UPDATE materiales SET nombre=?,descripcion=?,imagen=?,activo=?,orden=? WHERE id=?")
                        ->execute([$nombre,$desc,$img,$activo,$orden,$id]);
                } else {
                    $pdo->prepare("UPDATE materiales SET nombre=?,descripcion=?,activo=?,orden=? WHERE id=?")
                        ->execute([$nombre,$desc,$activo,$orden,$id]);
                }
                flash('success', 'Material actualizado correctamente.');
            } else {
                $pdo->prepare("INSERT INTO materiales (nombre,descripcion,imagen,activo,orden) VALUES (?,?,?,?,?)")
                    ->execute([$nombre,$desc,$img,$activo,$orden]);
                flash('success', 'Material añadido correctamente.');
            }
        }
    } elseif ($action === 'toggle') {
        $pdo->prepare("UPDATE materiales SET activo = 1 - activo WHERE id = ?")->execute([(int)$_POST['id']]);
        flash('success', 'Estado actualizado.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM materiales WHERE id = ?")->execute([(int)$_POST['id']]);
        flash('success', 'Material eliminado.');
    }

    header('Location: materiales.php');
    exit;
}

// ── GET: cargar datos ────────────────────────────────────
$editItem = null;
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $st = $pdo->prepare("SELECT * FROM materiales WHERE id = ?");
    $st->execute([(int)$_GET['id']]);
    $editItem = $st->fetch() ?: null;
}
$materiales = $pdo->query("SELECT * FROM materiales ORDER BY orden, id")->fetchAll();

$pageTitle = 'Materiales';
require_once __DIR__ . '/includes/header.php';
$f = get_flash();
?>

<?php if ($f): ?>
<div class="alert alert-<?= $f['type'] ?>">
  <i class="ri-<?= $f['type']==='success'?'check':'error-warning' ?>-line"></i>
  <?= htmlspecialchars($f['msg']) ?>
</div>
<?php endif; ?>

<!-- Formulario añadir / editar -->
<div class="panel">
  <div class="ph">
    <h2><?= $editItem ? 'Editar Material' : 'Añadir Nuevo Material' ?></h2>
    <?php if ($editItem): ?>
    <a href="materiales.php" class="btn btn-ghost btn-sm"><i class="ri-close-line"></i> Cancelar</a>
    <?php endif; ?>
  </div>
  <div class="pb">
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)($editItem['id'] ?? 0) ?>">

      <div class="fgrid">
        <div class="fg">
          <label>Nombre del material *</label>
          <input type="text" name="nombre" class="fc" required
                 value="<?= htmlspecialchars($editItem['nombre'] ?? '') ?>">
        </div>
        <div class="fg">
          <label>Orden (posición en el sitio)</label>
          <input type="number" name="orden" class="fc" min="0"
                 value="<?= (int)($editItem['orden'] ?? 0) ?>">
        </div>
        <div class="fg full">
          <label>Descripción</label>
          <textarea name="descripcion" class="fc"><?= htmlspecialchars($editItem['descripcion'] ?? '') ?></textarea>
        </div>
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

      <div style="margin-top:22px">
        <button type="submit" class="btn btn-primary">
          <i class="ri-save-line"></i>
          <?= $editItem ? 'Guardar cambios' : 'Añadir material' ?>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Lista de materiales -->
<div class="panel">
  <div class="ph">
    <h2>Todos los Materiales
      <span style="color:#64748b;font-size:.85rem;font-weight:400">(<?= count($materiales) ?>)</span>
    </h2>
  </div>
  <div class="tw">
    <table>
      <thead>
        <tr>
          <th>Imagen</th><th>Nombre</th><th>Descripción</th>
          <th>Orden</th><th>Estado</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$materiales): ?>
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px">
          No hay materiales. Añade el primero arriba.
        </td></tr>
      <?php else: ?>
      <?php foreach ($materiales as $m): ?>
        <tr>
          <td>
            <?php if ($m['imagen']): ?>
            <img src="../<?= htmlspecialchars($m['imagen']) ?>" class="thumb" alt="">
            <?php else: ?>
            <div class="nothumb"><i class="ri-image-line"></i></div>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($m['nombre']) ?></strong></td>
          <td style="max-width:240px;color:#64748b">
            <?= htmlspecialchars(mb_substr($m['descripcion'] ?? '', 0, 70)) ?>
            <?= mb_strlen($m['descripcion'] ?? '') > 70 ? '…' : '' ?>
          </td>
          <td><?= (int)$m['orden'] ?></td>
          <td><span class="badge badge-<?= $m['activo']?'on':'off' ?>"><?= $m['activo']?'Activo':'Inactivo' ?></span></td>
          <td>
            <div class="tda">
              <a href="?action=edit&id=<?= $m['id'] ?>" class="btn btn-warning btn-sm">
                <i class="ri-edit-line"></i> Editar
              </a>
              <form method="post" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-sm">
                  <i class="ri-eye-<?= $m['activo']?'off':'' ?>-line"></i>
                  <?= $m['activo']?'Ocultar':'Mostrar' ?>
                </button>
              </form>
              <form method="post" style="display:inline"
                    onsubmit="return confirm('¿Eliminar «<?= htmlspecialchars(addslashes($m['nombre'])) ?>»?')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $m['id'] ?>">
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
