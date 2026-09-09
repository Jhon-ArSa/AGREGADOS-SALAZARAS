<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db/config.php';

// Solo permitir si NO existe ningún admin todavía
$count = (int)$pdo->query("SELECT COUNT(*) FROM admin_usuarios")->fetchColumn();
if ($count > 0) {
    header('Location: login.php');
    exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user  = trim($_POST['username'] ?? '');
    $pass  = $_POST['password']  ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if (strlen($user) < 3)    $error = 'El usuario debe tener al menos 3 caracteres.';
    elseif (strlen($pass) < 8) $error = 'La contraseña debe tener al menos 8 caracteres.';
    elseif ($pass !== $pass2)  $error = 'Las contraseñas no coinciden.';
    else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admin_usuarios (username, password_hash) VALUES (?, ?)")
            ->execute([$user, $hash]);
        $success = '¡Cuenta creada! Ya puedes iniciar sesión.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Configuración inicial · Admin Agregados</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',system-ui,sans-serif}
body{background:#f0f4f8;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
.card{background:#fff;border-radius:20px;padding:44px 40px;width:100%;max-width:430px;box-shadow:0 8px 30px rgba(0,0,0,.12)}
.logo{text-align:center;margin-bottom:28px}
.logo i{font-size:2.8rem;color:#ff6b35;display:block}
.logo h1{font-size:1.4rem;margin-top:8px;color:#1a2332}
.logo p{color:#64748b;font-size:.85rem;margin-top:4px}
.fg{margin-bottom:16px}
label{display:block;margin-bottom:5px;font-weight:600;font-size:.875rem;color:#1a2332}
.fc{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.9rem;font-family:inherit;transition:border-color .2s}
.fc:focus{outline:none;border-color:#ff6b35;box-shadow:0 0 0 3px rgba(255,107,53,.12)}
.btn{width:100%;padding:13px;background:#ff6b35;color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:600;cursor:pointer;transition:background .2s;margin-top:6px}
.btn:hover{background:#e65a2e}
.msg{padding:11px 16px;border-radius:10px;font-size:.875rem;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.msg-err{background:#fee2e2;color:#991b1b}
.msg-ok{background:#d1fae5;color:#065f46}
.link{text-align:center;margin-top:14px;font-size:.85rem}
.link a{color:#ff6b35;text-decoration:none}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <i class="ri-settings-3-line"></i>
    <h1>Configuración inicial</h1>
    <p>Crea tu cuenta de administrador</p>
  </div>

  <?php if ($error): ?>
  <div class="msg msg-err"><i class="ri-error-warning-line"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
  <div class="msg msg-ok"><i class="ri-check-line"></i><?= htmlspecialchars($success) ?></div>
  <p class="link"><a href="login.php">Ir al panel de inicio de sesión →</a></p>
  <?php else: ?>
  <form method="post">
    <div class="fg">
      <label>Nombre de usuario</label>
      <input type="text" name="username" class="fc"
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
    </div>
    <div class="fg">
      <label>Contraseña <span style="color:#64748b;font-weight:400">(mínimo 8 caracteres)</span></label>
      <input type="password" name="password" class="fc" required>
    </div>
    <div class="fg">
      <label>Confirmar contraseña</label>
      <input type="password" name="password2" class="fc" required>
    </div>
    <button type="submit" class="btn">Crear cuenta y continuar</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>
