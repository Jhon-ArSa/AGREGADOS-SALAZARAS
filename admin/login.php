<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../db/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user !== '' && $pass !== '') {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_usuarios WHERE username = ? LIMIT 1");
        $stmt->execute([$user]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($pass, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['last_regen']     = time();
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar sesión · Admin Agregados</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',system-ui,sans-serif}
body{background:#f0f4f8;display:flex;align-items:center;justify-content:center;min-height:100vh}
.card{background:#fff;border-radius:20px;padding:44px 40px;width:100%;max-width:400px;box-shadow:0 8px 30px rgba(0,0,0,.12)}
.logo{text-align:center;margin-bottom:32px}
.logo i{font-size:3rem;color:#ff6b35;display:block}
.logo h1{font-size:1.5rem;margin-top:8px;color:#1a2332}
.logo p{color:#64748b;font-size:.875rem;margin-top:4px}
.fg{margin-bottom:18px}
label{display:block;margin-bottom:6px;font-weight:600;font-size:.875rem;color:#1a2332}
.fc{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.9rem;font-family:inherit;transition:border-color .2s,box-shadow .2s}
.fc:focus{outline:none;border-color:#ff6b35;box-shadow:0 0 0 3px rgba(255,107,53,.12)}
.btn{width:100%;padding:13px;background:#ff6b35;color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:600;cursor:pointer;transition:background .2s;margin-top:6px;display:flex;align-items:center;justify-content:center;gap:8px}
.btn:hover{background:#e65a2e}
.err{background:#fee2e2;color:#991b1b;padding:11px 16px;border-radius:10px;font-size:.875rem;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.setup{text-align:center;margin-top:20px;font-size:.82rem;color:#64748b}
.setup a{color:#ff6b35;text-decoration:none}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <i class="ri-home-5-fill"></i>
    <h1>Agregados</h1>
    <p>Panel de Administración</p>
  </div>

  <?php if ($error): ?>
  <div class="err"><i class="ri-error-warning-line"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" autocomplete="on">
    <div class="fg">
      <label for="username">Usuario</label>
      <input type="text" id="username" name="username" class="fc"
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
             autocomplete="username" required autofocus>
    </div>
    <div class="fg">
      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" class="fc"
             autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn">
      <i class="ri-login-box-line"></i> Ingresar
    </button>
  </form>

  <p class="setup">¿Primera vez? <a href="setup.php">Crear cuenta de administrador</a></p>
</div>
</body>
</html>
