<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/models/MouvementModel.php';
require_once APP_PATH  . '/models/ProduitModel.php';

class MouvementController extends Controller
{
    private MouvementModel $model;
    private ProduitModel   $produitModel;

    public function __construct()
    {
        $this->model        = new MouvementModel();
        $this->produitModel = new ProduitModel();
    }

    /** GET /mouvements — Journal des mouvements */
    public function index(): void
    {
        $this->requireAuth();

        $page  = max(1, (int)($_GET['page'] ?? 1));
        $type  = $_GET['type']  ?? '';
        $debut = $_GET['debut'] ?? '';
        $fin   = $_GET['fin']   ?? '';

        $result = $this->model->getJournal($type, $debut, $fin, $page);

        $this->render('mouvements/index', [
            'title'      => 'Journal des mouvements',
            'mouvements' => $result['items'],
            'pagination' => $result,
            'type'       => $type,
            'debut'      => $debut,
            'fin'        => $fin,
            'flash'      => self::getFlash(),
        ]);
    }

    /** GET /mouvements/entree — Formulaire d'entrée de stock */
    public function entree(): void
    {
        $this->requireAuth();
        $produits = $this->produitModel->findAll('nom');
        $this->render('mouvements/form', [
            'title'    => 'Entrée de stock (Réapprovisionnement)',
            'type'     => 'entree',
            'produits' => $produits,
        ]);
    }

    /** GET /mouvements/sortie — Formulaire de sortie de stock */
    public function sortie(): void
    {
        $this->requireAuth();
        $produits = $this->produitModel->findAll('nom');
        $this->render('mouvements/form', [
            'title'    => 'Sortie de stock (Vente / Perte)',
            'type'     => 'sortie',
            'produits' => $produits,
        ]);
    }

    /** POST /mouvements/store — Enregistrement d'un mouvement */
    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $produitId    = (int)($_POST['produit_id']    ?? 0);
        $typeMvt      = $_POST['type_mouvement']      ?? '';
        $quantite     = (int)($_POST['quantite']       ?? 0);
        $prixUnitaire = str_replace(',', '.', $_POST['prix_unitaire'] ?? '0');
        $motif        = trim($_POST['motif']            ?? '');
        $refDoc       = trim($_POST['reference_doc']   ?? '');

        // Validation
        $errors = [];
        $allowedTypes = ['entree', 'sortie', 'perte', 'ajustement'];

        if ($produitId <= 0)                        $errors[] = 'Veuillez sélectionner un produit.';
        if (!in_array($typeMvt, $allowedTypes, true)) $errors[] = 'Type de mouvement invalide.';
        if ($quantite <= 0)                         $errors[] = 'La quantité doit être supérieure à 0.';

        if (empty($errors)) {
            $produit = $this->produitModel->findById($produitId);

            if (!$produit) {
                $errors[] = 'Produit introuvable.';
            } elseif (in_array($typeMvt, ['sortie', 'perte']) && $quantite > $produit['quantite_stock']) {
                $errors[] = "Stock insuffisant. Disponible : {$produit['quantite_stock']} {$produit['unite']}.";
            }
        }

        if (!empty($errors)) {
            $produits = $this->produitModel->findAll('nom');
            $view = in_array($typeMvt, ['sortie', 'perte']) ? 'sortie' : 'entree';
            $this->render("mouvements/form", [
                'title'    => $view === 'sortie' ? 'Sortie de stock' : 'Entrée de stock',
                'type'     => $view,
                'produits' => $produits,
                'errors'   => $errors,
                'old'      => $_POST,
            ]);
            return;
        }

        $ok = $this->model->enregistrer([
            'produit_id'    => $produitId,
            'utilisateur_id'=> $_SESSION['user']['id'],
            'type_mouvement'=> $typeMvt,
            'quantite'      => $quantite,
            'prix_unitaire' => $prixUnitaire ?: null,
            'motif'         => $motif ?: null,
            'reference_doc' => $refDoc ?: null,
        ]);

        if ($ok) {
            $this->flash('success', 'Mouvement enregistré avec succès.');
        } else {
            $this->flash('danger', 'Erreur lors de l\'enregistrement du mouvement.');
        }
        $this->redirect('mouvements');
    }
}
