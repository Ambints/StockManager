<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/models/UtilisateurModel.php';

class AuthController extends Controller
{
    private UtilisateurModel $model;

    public function __construct()
    {
        $this->model = new UtilisateurModel();
    }

    /** GET /login */
    public function login(): void
    {
        // Déjà connecté → dashboard
        if (isset($_SESSION['user'])) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
            return;
        }

        $this->render('auth/login', ['title' => 'Connexion'], 'auth');
    }

    /** Traitement du formulaire de connexion */
    private function handleLogin(): void
    {
        $this->validateCsrf();

        $email = trim($_POST['email']    ?? '');
        $mdp   = (string)($_POST['password'] ?? '');

        $errors = [];
        if (empty($email)) $errors[] = 'L\'adresse email est requise.';
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Le format de l\'adresse email est invalide.';
        }
        if ($mdp === '')   $errors[] = 'Le mot de passe est requis.';

        if (empty($errors)) {
            $user = $this->model->findByEmail($email);
            $isActive = $this->isUserActive($user['actif'] ?? false);

            if ($user && $isActive && password_verify($mdp, $user['mot_de_passe_hash'])) {
                // Régénérer l'ID de session après authentification réussie
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id'     => $user['id'],
                    'nom'    => $user['nom'],
                    'prenom' => $user['prenom'],
                    'email'  => $user['email'],
                    'role'   => $user['role'],
                ];
                $_SESSION['last_regen'] = time();

                $this->redirect('dashboard');
                return;
            }

            $errors[] = $user && !$isActive
                ? 'Votre compte est inactif. Contactez un administrateur.'
                : 'Email ou mot de passe incorrect.';
        }

        $this->render('auth/login', [
            'title'  => 'Connexion',
            'errors' => $errors,
            'email'  => htmlspecialchars($email, ENT_QUOTES),
        ], 'auth');
    }

    private function isUserActive(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }

        $normalized = strtolower(trim((string)$value));
        return in_array($normalized, ['1', 'true', 't', 'yes', 'y', 'on'], true);
    }

    /** GET /logout */
    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('login');
    }
}
