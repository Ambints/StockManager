<?php
/**
 * Contrôleur de base — toutes les classes controller en héritent
 */
abstract class Controller
{
    /**
     * Charge et affiche une vue avec son layout
     *
     * @param string $view   Chemin relatif : 'dossier/fichier' (sans .php)
     * @param array  $data   Variables à injecter dans la vue
     * @param string $layout Layout à utiliser (default: 'main')
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        // Extraction des variables pour la vue
        extract($data, EXTR_SKIP);

        // Capture du contenu de la vue
        ob_start();
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            ob_end_clean();
            throw new RuntimeException("Vue introuvable : {$view}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Injection dans le layout
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Redirection HTTP
     */
    protected function redirect(string $path): void
    {
        $target = APP_URL . '/' . ltrim($path, '/');
        // #region agent log
        debugLog('initial', 'H4', 'core/Controller.php:45', 'HTTP redirect target', [
            'path' => $path,
            'target' => $target,
        ]);
        // #endregion
        header('Location: ' . $target);
        exit;
    }

    /**
     * Vérifie que l'utilisateur est connecté — redirige sinon
     */
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
        }
    }

    /**
     * Vérifie qu'un rôle est autorisé — redirige sinon
     */
    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        $userRole = $_SESSION['user']['role'] ?? '';
        if (!in_array($userRole, $roles, true)) {
            $this->redirect('dashboard');
        }
    }

    /**
     * Retourne une réponse JSON (pour les appels AJAX)
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Stocke un message flash en session
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Récupère et supprime le message flash
     */
    public static function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Valide le token CSRF
     */
    protected function validateCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('CSRF token invalide.');
        }
    }

    /**
     * Génère ou retourne le token CSRF de la session
     */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
