<?php
/**
 * Point d'entrée unique de l'application (Front Controller)
 * Toutes les requêtes passent par ce fichier via .htaccess
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
define('CORE_PATH', ROOT_PATH . '/core');

// Chargement automatique des classes (PSR-4 simplifié)
spl_autoload_register(function (string $class): void {
    $paths = [
        CORE_PATH . '/' . $class . '.php',
        APP_PATH  . '/controllers/' . $class . '.php',
        APP_PATH  . '/models/'      . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

// Démarrage de la session sécurisée
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// #region agent log
debugLog('initial', 'H1', 'public/index.php:39', 'Front controller reached', [
    'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? '',
    'https'       => isset($_SERVER['HTTPS']) ? 'on' : 'off',
]);
// #endregion

// Régénération de l'ID de session toutes les 30 min (protection fixation)
if (!isset($_SESSION['last_regen']) || time() - $_SESSION['last_regen'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}

// Lancement du routeur
$router = new Router();
$router->dispatch();
