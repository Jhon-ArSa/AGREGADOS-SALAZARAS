<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $valores = $_POST['valor'] ?? [];
    $stmt = $pdo->prepare("UPDATE estadisticas SET valor = ? WHERE clave = ?");
    foreach ($valores as $clave => $val) {
        // Sanear la clave: solo letras minúsculas y guiones bajos
        $clave = preg_replace('/[^a-z_]/', '', strtolower($clave));
        if ($clave !== '') $stmt->execute([(int)$val, $clave]);
    }
    flash('success', 'Estadísticas actualizadas correctamente. El sitio web ya refleja los nuevos valores.');
    header('Location: estadisticas.php');
    exit;
}

$stats = $pdo->query("SELECT * FROM estadisticas ORDER BY id")->fetchAll();

$pageTitle = 'Estadísticas';
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
  <div class="ph"><h2>Contadores del sitio web</h2></div>
  <div class="pb">
    <p style="color:#64748b;margin-bottom:28px;font-size:.9rem;line-height:1.6">
      Estos valores aparecen animados en la sección <strong>"Sobre Nosotros"</strong> del sitio web.
      Actualízalos cada vez que tengas nuevos clientes o proyectos finalizados.
    </p>
    <form method="post">
      <?= csrf_field() ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:28px">
        <?php foreach ($stats as $s): ?>
        <div style="background:#f8fafc;border-radius:14px;padding:22px;border:1.5px solid #e2e8f0">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
            <i class="<?= htmlspecialchars($s['icono']) ?>" style="font-size:1.7rem;color:#ff6b35"></i>
            <strong><?= htmlspecialchars($s['label']) ?></strong>
          </div>
          <input type="number" name="valor[<?= htmlspecialchars($s['clave']) ?>]"
                 class="fc" value="<?= (int)$s['valor'] ?>" min="0" required
                 style="font-size:1.2rem;font-weight:700;text-align:center">
        </div>
        <?php endforeach; ?>
      </div>
      <button type="submit" class="btn btn-primary">
        <i class="ri-save-line"></i> Guardar y publicar en el sitio web
      </button>
    </form>
  </div>
</div>

<div class="panel">
  <div class="ph"><h2>Vista previa de los contadores</h2></div>
  <div class="pb">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px">
      <?php foreach ($stats as $s): ?>
      <div style="text-align:center;padding:24px;background:#fff8f5;border-radius:14px;border:1.5px solid #ffe0d0">
        <i class="<?= htmlspecialchars($s['icono']) ?>" style="font-size:2rem;color:#ff6b35;display:block;margin-bottom:8px"></i>
        <strong style="font-size:2rem;display:block;color:#1a2332"><?= number_format((int)$s['valor']) ?></strong>
        <span style="font-size:.82rem;color:#64748b"><?= htmlspecialchars($s['label']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
