<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/models/UtilisateurModel.php';

class UtilisateurController extends Controller
{
    private UtilisateurModel $model;

    public function __construct()
    {
        $this->model = new UtilisateurModel();
    }

    /** GET /utilisateurs */
    public function index(): void
    {
        $this->requireRole('admin');
        $this->render('utilisateurs/index', [
            'title'        => 'Gestion des utilisateurs',
            'utilisateurs' => $this->model->findAll('nom'),
            'flash'        => self::getFlash(),
        ]);
    }

    /** GET /utilisateurs/create */
    public function create(): void
    {
        $this->requireRole('admin');
        $this->render('utilisateurs/form', ['title' => 'Nouvel utilisateur', 'user' => null]);
    }

    /** POST /utilisateurs/store */
    public function store(): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $data   = $this->collectForm();
        $errors = $this->validateForm($data);

        if (!empty($errors)) {
            $this->render('utilisateurs/form', ['title' => 'Nouvel utilisateur', 'user' => $data, 'errors' => $errors]);
            return;
        }

        $data['mot_de_passe_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        unset($data['password'], $data['password_confirm']);

        if ($this->model->create($data)) {
            $this->flash('success', 'Utilisateur créé avec succès.');
        } else {
            $this->flash('danger', 'Erreur lors de la création.');
        }
        $this->redirect('utilisateurs');
    }

    /** GET /utilisateurs/edit?id= */
    public function edit(): void
    {
        $this->requireRole('admin');
        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->model->findById($id);
        if (!$user) { $this->redirect('utilisateurs'); }

        $this->render('utilisateurs/form', ['title' => 'Modifier l\'utilisateur', 'user' => $user]);
    }

    /** POST /utilisateurs/update */
    public function update(): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $id   = (int)($_POST['id'] ?? 0);
        $data = $this->collectForm();
        $errors = $this->validateForm($data, $id);

        if (!empty($errors)) {
            $data['id'] = $id;
            $this->render('utilisateurs/form', ['title' => 'Modifier l\'utilisateur', 'user' => $data, 'errors' => $errors]);
            return;
        }

        // Mise à jour du mot de passe seulement si renseigné
        if (!empty($data['password'])) {
            $data['mot_de_passe_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        unset($data['password'], $data['password_confirm']);

        if ($this->model->update($id, $data)) {
            $this->flash('success', 'Utilisateur mis à jour.');
        } else {
            $this->flash('danger', 'Erreur lors de la mise à jour.');
        }
        $this->redirect('utilisateurs');
    }

    /** POST /utilisateurs/delete */
    public function delete(): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)$_SESSION['user']['id']) {
            $this->flash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('utilisateurs');
            return;
        }

        if ($this->model->deleteById($id)) {
            $this->flash('success', 'Utilisateur supprimé.');
        } else {
            $this->flash('danger', 'Erreur lors de la suppression.');
        }
        $this->redirect('utilisateurs');
    }

    private function collectForm(): array
    {
        return [
            'nom'              => trim($_POST['nom']              ?? ''),
            'prenom'           => trim($_POST['prenom']           ?? ''),
            'email'            => trim($_POST['email']            ?? ''),
            'role'             => $_POST['role']                  ?? 'vendeur',
            'actif'            => isset($_POST['actif']) ? true : false,
            'password'         => $_POST['password']              ?? '',
            'password_confirm' => $_POST['password_confirm']      ?? '',
        ];
    }

    private function validateForm(array $data, int $excludeId = 0): array
    {
        $errors = [];
        if (empty($data['nom']))    $errors[] = 'Le nom est requis.';
        if (empty($data['prenom'])) $errors[] = 'Le prénom est requis.';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if (!in_array($data['role'], ['admin', 'gestionnaire', 'vendeur'], true)) {
            $errors[] = 'Rôle invalide.';
        }
        if ($excludeId === 0 && empty($data['password'])) {
            $errors[] = 'Le mot de passe est requis pour un nouvel utilisateur.';
        }
        if (!empty($data['password']) && strlen($data['password']) < 8) {
            $errors[] = 'Le mot de passe doit comporter au moins 8 caractères.';
        }
        if (!empty($data['password']) && $data['password'] !== $data['password_confirm']) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        if (!empty($data['email']) && (new UtilisateurModel())->emailExiste($data['email'], $excludeId)) {
            $errors[] = 'Cet email est déjà utilisé.';
        }
        return $errors;
    }
}
