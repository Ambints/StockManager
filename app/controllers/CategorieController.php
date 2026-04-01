<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/models/CategorieModel.php';

class CategorieController extends Controller
{
    private CategorieModel $model;
    public function __construct() { $this->model = new CategorieModel(); }

    public function index(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $this->render('categories/index', [
            'title'      => 'Catégories',
            'categories' => $this->model->findAll('nom'),
            'flash'      => self::getFlash(),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $this->validateCsrf();
        $nom  = trim($_POST['nom'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if (!empty($nom) && $this->model->create(['nom' => $nom, 'description' => $desc])) {
            $this->flash('success', 'Catégorie créée.');
        } else {
            $this->flash('danger', 'Erreur ou nom déjà existant.');
        }
        $this->redirect('categories');
    }

    public function delete(): void
    {
        $this->requireRole('admin');
        $this->validateCsrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($this->model->deleteById($id)) {
            $this->flash('success', 'Catégorie supprimée.');
        } else {
            $this->flash('danger', 'Impossible de supprimer (des produits y sont liés).');
        }
        $this->redirect('categories');
    }
}
