<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/models/ProduitModel.php';
require_once APP_PATH  . '/models/CategorieModel.php';
require_once APP_PATH  . '/models/FournisseurModel.php';

class ProduitController extends Controller
{
    private ProduitModel    $model;
    private CategorieModel  $catModel;
    private FournisseurModel $fourModel;

    public function __construct()
    {
        $this->model     = new ProduitModel();
        $this->catModel  = new CategorieModel();
        $this->fourModel = new FournisseurModel();
    }

    /** GET /produits */
    public function index(): void
    {
        $this->requireAuth();

        $page      = max(1, (int)($_GET['page']      ?? 1));
        $search    = trim($_GET['search']    ?? '');
        $categorie = (int)($_GET['categorie'] ?? 0);
        $statut    = $_GET['statut'] ?? '';

        $result = $this->model->search($search, $categorie, $statut, $page);

        $this->render('produits/index', [
            'title'      => 'Produits',
            'produits'   => $result['items'],
            'pagination' => $result,
            'categories' => $this->catModel->findAll('nom'),
            'search'     => $search,
            'categorie'  => $categorie,
            'statut'     => $statut,
            'flash'      => self::getFlash(),
        ]);
    }

    /** GET /produits/show?id= */
    public function show(): void
    {
        $this->requireAuth();
        $id      = (int)($_GET['id'] ?? 0);
        $produit = $this->model->findById($id);
        if (!$produit) { $this->redirect('produits'); }

        $this->render('produits/show', [
            'title'   => 'Détail produit',
            'produit' => $produit,
            'mouvements' => (new \MouvementModel())->getByProduit($id, 15),
        ]);
    }

    /** GET /produits/create */
    public function create(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $this->render('produits/form', [
            'title'       => 'Nouveau produit',
            'produit'     => null,
            'categories'  => $this->catModel->findAll('nom'),
            'fournisseurs'=> $this->fourModel->findAll('nom'),
        ]);
    }

    /** POST /produits/store */
    public function store(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $this->validateCsrf();

        $data   = $this->collectForm();
        $errors = $this->validateForm($data);

        if (!empty($errors)) {
            $this->render('produits/form', [
                'title'       => 'Nouveau produit',
                'produit'     => $data,
                'errors'      => $errors,
                'categories'  => $this->catModel->findAll('nom'),
                'fournisseurs'=> $this->fourModel->findAll('nom'),
            ]);
            return;
        }

        if ($this->model->create($data)) {
            $this->flash('success', 'Produit créé avec succès.');
        } else {
            $this->flash('danger', 'Erreur lors de la création du produit.');
        }
        $this->redirect('produits');
    }

    /** GET /produits/edit?id= */
    public function edit(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $id      = (int)($_GET['id'] ?? 0);
        $produit = $this->model->findById($id);
        if (!$produit) { $this->redirect('produits'); }

        $this->render('produits/form', [
            'title'       => 'Modifier le produit',
            'produit'     => $produit,
            'categories'  => $this->catModel->findAll('nom'),
            'fournisseurs'=> $this->fourModel->findAll('nom'),
        ]);
    }

    /** POST /produits/update */
    public function update(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $this->validateCsrf();

        $id   = (int)($_POST['id'] ?? 0);
        $data = $this->collectForm();
        $errors = $this->validateForm($data, $id);

        if (!empty($errors)) {
            $data['id'] = $id;
            $this->render('produits/form', [
                'title'       => 'Modifier le produit',
                'produit'     => $data,
                'errors'      => $errors,
                'categories'  => $this->catModel->findAll('nom'),
                'fournisseurs'=> $this->fourModel->findAll('nom'),
            ]);
            return;
        }

        if ($this->model->update($id, $data)) {
            $this->flash('success', 'Produit mis à jour.');
        } else {
            $this->flash('danger', 'Erreur lors de la mise à jour.');
        }
        $this->redirect('produits');
    }

    /** POST /produits/delete */
    public function delete(): void
    {
        $this->requireRole('admin', 'gestionnaire');
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($this->model->desactiver($id)) {
            $this->flash('success', 'Produit désactivé.');
        } else {
            $this->flash('danger', 'Impossible de désactiver ce produit.');
        }
        $this->redirect('produits');
    }

    // ── Helpers privés ────────────────────────────────────

    private function collectForm(): array
    {
        return [
            'code_produit'  => trim($_POST['code_produit']   ?? ''),
            'nom'           => trim($_POST['nom']             ?? ''),
            'description'   => trim($_POST['description']     ?? ''),
            'categorie_id'  => $_POST['categorie_id']  ?: null,
            'fournisseur_id'=> $_POST['fournisseur_id'] ?: null,
            'prix_achat'    => str_replace(',', '.', $_POST['prix_achat']  ?? '0'),
            'prix_vente'    => str_replace(',', '.', $_POST['prix_vente']  ?? '0'),
            'quantite_stock'=> (int)($_POST['quantite_stock'] ?? 0),
            'seuil_alerte'  => (int)($_POST['seuil_alerte']   ?? 5),
            'unite'         => trim($_POST['unite'] ?? 'unité'),
        ];
    }

    private function validateForm(array $data, int $excludeId = 0): array
    {
        $errors = [];
        if (empty($data['code_produit'])) $errors[] = 'Le code produit est requis.';
        if (empty($data['nom']))          $errors[] = 'Le nom du produit est requis.';
        if ((float)$data['prix_vente'] < 0) $errors[] = 'Le prix de vente ne peut pas être négatif.';
        if ((int)$data['seuil_alerte']  < 0) $errors[] = 'Le seuil d\'alerte ne peut pas être négatif.';

        // Unicité du code produit
        if (!empty($data['code_produit']) && $this->model->codeExiste($data['code_produit'], $excludeId)) {
            $errors[] = 'Ce code produit est déjà utilisé.';
        }

        return $errors;
    }
}
