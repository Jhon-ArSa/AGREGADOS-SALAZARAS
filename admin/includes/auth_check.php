<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Regenerar ID periódicamente (anti session-fixation)
if (empty($_SESSION['last_regen']) || time() - $_SESSION['last_regen'] > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="'.htmlspecialchars($_SESSION['csrf']).'">';
}

function csrf_verify(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? '')) {
        http_response_code(403);
        die('Petición no válida.');
    }
}

function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array {
    if (empty($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

/**
 * Sube una imagen a uploads/<folder>/ y retorna la ruta relativa al webroot.
 */
function upload_img(string $field, string $folder): ?string {
    if (empty($_FILES[$field]['tmp_name'])) return null;
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES[$field]['tmp_name']);
    finfo_close($finfo);

    $mimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($mimes[$mime])) return null;
    if ($_FILES[$field]['size'] > 5 * 1024 * 1024) return null;

    $dir = __DIR__.'/../../uploads/'.$folder.'/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $name = uniqid('', true).'.'.$mimes[$mime];
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dir.$name)) return null;

    return 'uploads/'.$folder.'/'.$name;
}
