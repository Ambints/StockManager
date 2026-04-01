<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/models/FournisseurModel.php';

class FournisseurController extends Controller
{
    private FournisseurModel $model;
    public function __construct() { $this->model = new FournisseurModel(); }

    public function index(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $this->render('fournisseurs/index', [
            'title'        => 'Fournisseurs',
            'fournisseurs' => $this->model->findAll('nom'),
            'flash'        => self::getFlash(),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $this->validateCsrf();
        $data = [
            'nom'       => trim($_POST['nom']       ?? ''),
            'contact'   => trim($_POST['contact']   ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'email'     => trim($_POST['email']     ?? ''),
            'adresse'   => trim($_POST['adresse']   ?? ''),
        ];
        if (!empty($data['nom']) && $this->model->create($data)) {
            $this->flash('success', 'Fournisseur ajouté.');
        } else {
            $this->flash('danger', 'Erreur lors de l\'ajout.');
        }
        $this->redirect('fournisseurs');
    }

    public function delete(): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($this->model->deleteById($id)) {
            $this->flash('success', 'Fournisseur supprimé.');
        } else {
            $this->flash('danger', 'Impossible (produits liés).');
        }
        $this->redirect('fournisseurs');
    }
}
