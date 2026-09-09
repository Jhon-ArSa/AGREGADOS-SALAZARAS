<?php
// Ajustar antes de desplegar en producción
define('DB_HOST', 'localhost');
define('DB_NAME', 'salazaras');
define('DB_USER', 'root');
define('DB_PASS', 'admin');

try {
    $pdo = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('DB Error: '.$e->getMessage());
    die('<p style="font:1rem sans-serif;text-align:center;padding:40px;color:#c00">
        No se pudo conectar con la base de datos.<br>
        Revisa los datos en <code>db/config.php</code>.
    </p>');
}
