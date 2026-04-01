<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/models/ProduitModel.php';
require_once APP_PATH  . '/models/MouvementModel.php';
require_once APP_PATH  . '/models/AlerteModel.php';

class DashboardController extends Controller
{
    /** GET /dashboard */
    public function index(): void
    {
        $this->requireAuth();

        $produitModel    = new ProduitModel();
        $mouvementModel  = new MouvementModel();
        $alerteModel     = new AlerteModel();

        $data = [
            'title'               => 'Tableau de bord',
            'totalProduits'       => $produitModel->countActifs(),
            'totalStockCritique'  => $produitModel->countStockCritique(),
            'totalRuptures'       => $produitModel->countRuptures(),
            'alertesNonResolues'  => $alerteModel->countNonResolues(),
            'produitsCritiques'   => $produitModel->getStockCritique(8),
            'derniersMovements'   => $mouvementModel->getDerniers(10),
            'statsJour'           => $mouvementModel->getStatsDuJour(),
        ];

        $this->render('dashboard/index', $data);
    }
}
