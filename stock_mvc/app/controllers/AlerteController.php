<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/models/AlerteModel.php';

class AlerteController extends Controller
{
    private AlerteModel $model;

    public function __construct()
    {
        $this->model = new AlerteModel();
    }

    /** GET /alertes */
    public function index(): void
    {
        $this->requireAuth();

        $page   = max(1, (int)($_GET['page']   ?? 1));
        $statut = $_GET['statut'] ?? 'non_resolues';

        $result = $this->model->getListe($statut, $page);

        $this->render('alertes/index', [
            'title'      => 'Alertes de stock',
            'alertes'    => $result['items'],
            'pagination' => $result,
            'statut'     => $statut,
            'nbNonResolues' => $this->model->countNonResolues(),
            'flash'      => self::getFlash(),
        ]);
    }

    /** POST /alertes/resoudre */
    public function resoudre(): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);

        if ($this->model->marquerResolue($id, (int)$_SESSION['user']['id'])) {
            $this->flash('success', 'Alerte marquée comme résolue.');
        } else {
            $this->flash('danger', 'Impossible de résoudre cette alerte.');
        }
        $this->redirect('alertes');
    }
}
