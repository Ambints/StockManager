<?php
/**
 * Configuration générale de l'application
 * Modifier ce fichier selon l'environnement (dev / prod)
 */

// ── Environnement ─────────────────────────────────────────
define('APP_ENV',   'development');   // 'development' | 'production'
define('APP_NAME',  'StockManager');
define('APP_URL',   'http://localhost/stock_mvc');
define('APP_ASSET_URL', APP_URL . '/public');
define('APP_DEBUG', APP_ENV === 'development');

if (!function_exists('debugLog')) {
    function debugLog(string $runId, string $hypothesisId, string $location, string $message, array $data = []): void
    {
        $entry = [
            'sessionId'    => '2db629',
            'runId'        => $runId,
            'hypothesisId' => $hypothesisId,
            'location'     => $location,
            'message'      => $message,
            'data'         => $data,
            'timestamp'    => (int) round(microtime(true) * 1000),
        ];

        @file_put_contents(ROOT_PATH . '/debug-2db629.log', json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    }
}

// ── Session ───────────────────────────────────────────────
define('SESSION_LIFETIME', 3600);     // 1 heure en secondes

// ── Affichage des erreurs ─────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors',     '1');
    ini_set('error_log',      ROOT_PATH . '/logs/error.log');
}

// ── Pagination ────────────────────────────────────────────
define('ITEMS_PER_PAGE', 20);

// ── Routes disponibles ────────────────────────────────────
// Format: 'chemin' => ['controller' => 'NomController', 'action' => 'nomMethode']
define('ROUTES', [
    // Auth
    ''              => ['controller' => 'AuthController',      'action' => 'login'],
    'login'         => ['controller' => 'AuthController',      'action' => 'login'],
    'logout'        => ['controller' => 'AuthController',      'action' => 'logout'],

    // Dashboard
    'dashboard'     => ['controller' => 'DashboardController', 'action' => 'index'],

    // Produits
    'produits'             => ['controller' => 'ProduitController', 'action' => 'index'],
    'produits/create'      => ['controller' => 'ProduitController', 'action' => 'create'],
    'produits/store'       => ['controller' => 'ProduitController', 'action' => 'store'],
    'produits/edit'        => ['controller' => 'ProduitController', 'action' => 'edit'],
    'produits/update'      => ['controller' => 'ProduitController', 'action' => 'update'],
    'produits/delete'      => ['controller' => 'ProduitController', 'action' => 'delete'],
    'produits/show'        => ['controller' => 'ProduitController', 'action' => 'show'],

    // Mouvements
    'mouvements'           => ['controller' => 'MouvementController', 'action' => 'index'],
    'mouvements/entree'    => ['controller' => 'MouvementController', 'action' => 'entree'],
    'mouvements/sortie'    => ['controller' => 'MouvementController', 'action' => 'sortie'],
    'mouvements/store'     => ['controller' => 'MouvementController', 'action' => 'store'],

    // Alertes
    'alertes'              => ['controller' => 'AlerteController', 'action' => 'index'],
    'alertes/resoudre'     => ['controller' => 'AlerteController', 'action' => 'resoudre'],

    // Utilisateurs (admin)
    'utilisateurs'         => ['controller' => 'UtilisateurController', 'action' => 'index'],
    'utilisateurs/create'  => ['controller' => 'UtilisateurController', 'action' => 'create'],
    'utilisateurs/store'   => ['controller' => 'UtilisateurController', 'action' => 'store'],
    'utilisateurs/edit'    => ['controller' => 'UtilisateurController', 'action' => 'edit'],
    'utilisateurs/update'  => ['controller' => 'UtilisateurController', 'action' => 'update'],
    'utilisateurs/delete'  => ['controller' => 'UtilisateurController', 'action' => 'delete'],

    // Catégories
    'categories'           => ['controller' => 'CategorieController', 'action' => 'index'],
    'categories/store'     => ['controller' => 'CategorieController', 'action' => 'store'],
    'categories/delete'    => ['controller' => 'CategorieController', 'action' => 'delete'],

    // Fournisseurs
    'fournisseurs'         => ['controller' => 'FournisseurController', 'action' => 'index'],
    'fournisseurs/store'   => ['controller' => 'FournisseurController', 'action' => 'store'],
    'fournisseurs/delete'  => ['controller' => 'FournisseurController', 'action' => 'delete'],
]);
