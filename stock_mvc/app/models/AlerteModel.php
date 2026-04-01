<?php
require_once CORE_PATH . '/Model.php';

class AlerteModel extends Model
{
    protected string $table = 'alertes';

    public function getListe(string $statut, int $page): array
    {
        $where  = $statut === 'non_resolues' ? 'a.resolue = FALSE' : '1=1';
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $countSql = "SELECT COUNT(*) FROM alertes a WHERE {$where}";
        $total    = (int)$this->pdo->query($countSql)->fetchColumn();

        $stmt = $this->pdo->prepare("
            SELECT a.*, p.nom AS produit_nom, p.code_produit, p.quantite_stock, p.seuil_alerte,
                   u.prenom || ' ' || u.nom AS resolue_par_nom
            FROM alertes a
            JOIN produits p ON a.produit_id = p.id
            LEFT JOIN utilisateurs u ON a.resolue_par = u.id
            WHERE {$where}
            ORDER BY a.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        $stmt->bindValue(':lim', ITEMS_PER_PAGE, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,        PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int)ceil($total / ITEMS_PER_PAGE),
            'page'  => $page,
        ];
    }

    public function countNonResolues(): int
    {
        return $this->count('resolue = FALSE');
    }

    public function marquerResolue(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE alertes
            SET resolue = TRUE, resolue_par = :uid, resolue_at = NOW()
            WHERE id = :id AND resolue = FALSE
        ");
        return $stmt->execute([':uid' => $userId, ':id' => $id]);
    }
}
