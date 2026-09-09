<?php
$page  = basename($_SERVER['PHP_SELF'], '.php');
$title = $pageTitle ?? 'Panel Admin';
$nav   = [
    'dashboard'    => ['ri-dashboard-3-line', 'Dashboard'],
    'materiales'   => ['ri-stack-line',       'Materiales'],
    'noticias'     => ['ri-newspaper-line',    'Noticias'],
    'estadisticas' => ['ri-bar-chart-2-line',  'Estadísticas'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?> · Admin Agregados</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
:root{--p:#ff6b35;--p2:#e65a2e;--sb:#1e2d3d;--bg:#f0f4f8;--wh:#fff;--tx:#1a2332;--mu:#64748b;--br:#e2e8f0;--sh:0 2px 10px rgba(0,0,0,.08);--sh2:0 8px 24px rgba(0,0,0,.14)}
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',system-ui,sans-serif}
body{background:var(--bg);color:var(--tx)}
a{text-decoration:none;color:inherit}
/* Layout */
.admin-wrap{display:flex;min-height:100vh}
/* Sidebar */
.sidebar{width:240px;min-height:100vh;background:var(--sb);display:flex;flex-direction:column;position:fixed;top:0;left:0;z-index:200;transition:transform .3s ease}
.sb-brand{display:flex;align-items:center;gap:10px;padding:22px 20px;color:#fff;border-bottom:1px solid rgba(255,255,255,.07)}
.sb-brand i{color:var(--p);font-size:1.7rem}
.sb-brand span{font-size:1.2rem;font-weight:700}
.sb-nav{flex:1;padding:10px 0;display:flex;flex-direction:column;gap:2px;overflow-y:auto}
.sb-nav a{display:flex;align-items:center;gap:12px;padding:12px 20px;color:#94a3b8;font-size:.9rem;border-left:3px solid transparent;transition:all .2s}
.sb-nav a:hover,.sb-nav a.active{color:#fff;background:rgba(255,107,53,.15);border-left-color:var(--p)}
.sb-nav a i{font-size:1.15rem;flex-shrink:0}
.sb-bottom{padding:10px 0;border-top:1px solid rgba(255,255,255,.07)}
.sb-ext{display:flex;align-items:center;gap:10px;padding:11px 20px;color:#94a3b8;font-size:.85rem;transition:color .2s}
.sb-ext:hover{color:#fff}
.sb-logout{display:flex;align-items:center;gap:10px;padding:11px 20px;color:#f87171;font-size:.85rem;transition:background .2s}
.sb-logout:hover{background:rgba(248,113,113,.1)}
/* Admin body */
.admin-body{margin-left:240px;flex:1;display:flex;flex-direction:column;min-height:100vh}
.topbar{background:var(--wh);padding:0 28px;height:64px;display:flex;align-items:center;gap:16px;box-shadow:var(--sh);position:sticky;top:0;z-index:100}
.menu-btn{display:none;background:none;border:none;cursor:pointer;font-size:1.4rem;color:var(--tx)}
.topbar-title{font-size:1.2rem;font-weight:700;flex:1}
.topbar-user{display:flex;align-items:center;gap:8px;color:var(--mu);font-size:.875rem}
.topbar-user i{font-size:1.15rem;color:var(--p)}
.admin-main{padding:28px;flex:1}
/* Cards */
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:20px;margin-bottom:28px}
.sc{background:var(--wh);border-radius:14px;padding:22px;box-shadow:var(--sh);display:flex;align-items:center;gap:16px}
.sc-icon{width:50px;height:50px;border-radius:12px;background:rgba(255,107,53,.1);display:flex;align-items:center;justify-content:center;color:var(--p);font-size:1.4rem;flex-shrink:0}
.sc h3{font-size:1.6rem;font-weight:700;line-height:1.1}
.sc p{color:var(--mu);font-size:.8rem;margin-top:3px}
/* Panel */
.panel{background:var(--wh);border-radius:14px;box-shadow:var(--sh);margin-bottom:24px;overflow:hidden}
.ph{padding:18px 24px;border-bottom:1px solid var(--br);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.ph h2{font-size:1rem;font-weight:700}
.pb{padding:24px}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;border:none;cursor:pointer;font-size:.875rem;font-weight:600;transition:all .2s;text-decoration:none}
.btn-primary{background:var(--p);color:#fff}
.btn-primary:hover{background:var(--p2);color:#fff}
.btn-danger{background:#fee2e2;color:#ef4444}
.btn-danger:hover{background:#ef4444;color:#fff}
.btn-warning{background:#fef3c7;color:#d97706}
.btn-warning:hover{background:#d97706;color:#fff}
.btn-ghost{background:#f1f5f9;color:var(--tx)}
.btn-ghost:hover{background:#e2e8f0}
.btn-sm{padding:6px 12px;font-size:.8rem}
/* Tables */
.tw{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.875rem}
th{background:#f8fafc;padding:11px 16px;text-align:left;font-weight:600;color:var(--mu);font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid var(--br);white-space:nowrap}
td{padding:13px 16px;border-bottom:1px solid var(--br);vertical-align:middle}
tr:last-child td{border-bottom:none}
tbody tr:hover{background:#f8fafc}
.tda{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
/* Forms */
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.full{grid-column:1/-1}
.fg{margin-bottom:0}
label{display:block;margin-bottom:5px;font-weight:600;font-size:.875rem}
.fc{width:100%;padding:10px 14px;border:1.5px solid var(--br);border-radius:8px;font-size:.875rem;color:var(--tx);background:var(--wh);font-family:inherit;transition:border-color .2s,box-shadow .2s}
.fc:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px rgba(255,107,53,.12)}
textarea.fc{resize:vertical;min-height:90px}
small{display:block;margin-top:4px;color:var(--mu);font-size:.78rem}
/* Alerts */
.alert{padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:.875rem;display:flex;align-items:center;gap:10px}
.alert-success{background:#d1fae5;color:#065f46}
.alert-error{background:#fee2e2;color:#991b1b}
/* Badges */
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:600}
.badge-on{background:#d1fae5;color:#059669}
.badge-off{background:#fee2e2;color:#ef4444}
/* Thumbnails */
.thumb{width:62px;height:50px;object-fit:cover;border-radius:8px;border:1px solid var(--br)}
.nothumb{width:62px;height:50px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:var(--mu);font-size:1.2rem;border:1px solid var(--br)}
/* Quick links */
.quick-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:28px}
.qa{display:flex;flex-direction:column;align-items:center;gap:10px;padding:22px 16px;background:var(--wh);border-radius:14px;box-shadow:var(--sh);color:var(--tx);font-size:.85rem;font-weight:600;transition:all .25s;text-align:center}
.qa:hover{transform:translateY(-4px);box-shadow:var(--sh2);color:var(--p)}
.qa i{font-size:1.8rem;color:var(--p)}
/* Responsive */
@media(max-width:900px){
  .menu-btn{display:flex}
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:translateX(0)}
  .admin-body{margin-left:0}
  .fgrid{grid-template-columns:1fr}
}
</style>
</head>
<body>
<div class="admin-wrap">

  <aside class="sidebar" id="sidebar">
    <div class="sb-brand">
      <i class="ri-home-5-fill"></i>
      <span>Agregados</span>
    </div>
    <nav class="sb-nav">
      <?php foreach ($nav as $slug => [$ico, $lbl]): ?>
      <a href="<?= $slug ?>.php" class="<?= $page === $slug ? 'active' : '' ?>">
        <i class="<?= $ico ?>"></i> <span><?= $lbl ?></span>
      </a>
      <?php endforeach; ?>
    </nav>
    <div class="sb-bottom">
      <a href="../index.php" target="_blank" class="sb-ext">
        <i class="ri-external-link-line"></i> Ver sitio web
      </a>
      <a href="logout.php" class="sb-logout">
        <i class="ri-logout-box-r-line"></i> Cerrar sesión
      </a>
    </div>
  </aside>

  <div class="admin-body">
    <header class="topbar">
      <button class="menu-btn" id="menu-toggle" aria-label="Menú">
        <i class="ri-menu-line"></i>
      </button>
      <h1 class="topbar-title"><?= htmlspecialchars($title) ?></h1>
      <div class="topbar-user">
        <i class="ri-user-circle-line"></i>
        <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
      </div>
    </header>
    <main class="admin-main">
