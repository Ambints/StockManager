<?php
/**
 * Routeur — analyse l'URL et instancie le bon controller/action
 */
class Router
{
    public function dispatch(): void
    {
        // Récupération du chemin depuis l'URL
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $rawUri = $uri;

        // Supprime le préfixe de base (pour sous-dossier Apache)
        $basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        // Nettoyage : query string et slashes en trop
        $uri = trim(parse_url($uri, PHP_URL_PATH) ?? '', '/');

        // #region agent log
        debugLog('initial', 'H2', 'core/Router.php:24', 'Router normalized URI', [
            'raw_uri' => $rawUri,
            'base_path' => $basePath,
            'normalized_uri' => $uri,
        ]);
        // #endregion

        // Résolution de la route
        $routes = ROUTES;
        $route  = $routes[$uri] ?? null;

        // #region agent log
        debugLog('initial', 'H3', 'core/Router.php:33', 'Route lookup result', [
            'uri' => $uri,
            'route_found' => $route !== null,
            'controller' => $route['controller'] ?? null,
            'action' => $route['action'] ?? null,
        ]);
        // #endregion

        if ($route === null) {
            $this->notFound();
            return;
        }

        // Verrou global : tant que l'utilisateur n'est pas authentifié,
        // seules les routes de connexion restent accessibles.
        $publicRoutes = ['', 'login'];
        if (!isset($_SESSION['user']) && !in_array($uri, $publicRoutes, true)) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $controllerName = $route['controller'];
        $action         = $route['action'];

        // Chargement du controller
        $file = APP_PATH . '/controllers/' . $controllerName . '.php';
        if (!file_exists($file)) {
            $this->notFound();
            return;
        }

        require_once $file;

        if (!class_exists($controllerName)) {
            $this->notFound();
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            $this->notFound();
            return;
        }

        $controller->$action();
    }

    private function notFound(): void
    {
        http_response_code(404);
        require_once APP_PATH . '/views/errors/404.php';
    }
}
