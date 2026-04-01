<?php
require_once CORE_PATH . '/Model.php';

class MouvementModel extends Model
{
    protected string $table = 'mouvements_stock';

    /**
     * Enregistre un mouvement et met à jour le stock dans une transaction
     */
    public function enregistrer(array $data): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Lecture du stock actuel avec verrou
            $stmt = $this->pdo->prepare(
                "SELECT quantite_stock FROM produits WHERE id = :id FOR UPDATE"
            );
            $stmt->execute([':id' => $data['produit_id']]);
            $stockActuel = (int)$stmt->fetchColumn();

            // Calcul du nouveau stock
            $isEntree = ($data['type_mouvement'] === 'entree' || $data['type_mouvement'] === 'ajustement');
            $newStock = $isEntree
                ? $stockActuel + $data['quantite']
                : $stockActuel - $data['quantite'];

            if ($newStock < 0) {
                $this->pdo->rollBack();
                return false;
            }

            // Insertion du mouvement
            $stmt = $this->pdo->prepare("
                INSERT INTO mouvements_stock
                    (produit_id, utilisateur_id, type_mouvement, quantite,
                     quantite_avant, quantite_apres, prix_unitaire, motif, reference_doc)
                VALUES
                    (:pid, :uid, :type, :qte, :qavant, :qapres, :pu, :motif, :ref)
            ");
            $stmt->execute([
                ':pid'    => $data['produit_id'],
                ':uid'    => $data['utilisateur_id'],
                ':type'   => $data['type_mouvement'],
                ':qte'    => $data['quantite'],
                ':qavant' => $stockActuel,
                ':qapres' => $newStock,
                ':pu'     => $data['prix_unitaire'],
                ':motif'  => $data['motif'],
                ':ref'    => $data['reference_doc'],
            ]);

            // Mise à jour du stock produit
            $stmt = $this->pdo->prepare(
                "UPDATE produits SET quantite_stock = :qs, updated_at = NOW() WHERE id = :id"
            );
            $stmt->execute([':qs' => $newStock, ':id' => $data['produit_id']]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            if (APP_DEBUG) error_log('MouvementModel::enregistrer - ' . $e->getMessage());
            return false;
        }
    }

    /** Journal paginé avec filtres */
    public function getJournal(string $type, string $debut, string $fin, int $page): array
    {
        $conditions = ['1=1'];
        $params     = [];

        if (!empty($type)) {
            $conditions[] = "m.type_mouvement = :type";
            $params[':type'] = $type;
        }
        if (!empty($debut)) {
            $conditions[] = "m.created_at >= :debut";
            $params[':debut'] = $debut . ' 00:00:00';
        }
        if (!empty($fin)) {
            $conditions[] = "m.created_at <= :fin";
            $params[':fin'] = $fin . ' 23:59:59';
        }

        $where  = implode(' AND ', $conditions);
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $countSql = "SELECT COUNT(*) FROM mouvements_stock m WHERE {$where}";
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT m.*, p.nom AS produit_nom, p.code_produit, p.unite,
                       u.prenom || ' ' || u.nom AS utilisateur_nom
                FROM mouvements_stock m
                JOIN produits     p ON m.produit_id     = p.id
                JOIN utilisateurs u ON m.utilisateur_id = u.id
                WHERE {$where}
                ORDER BY m.created_at DESC
                LIMIT :lim OFFSET :off";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
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

    public function getDerniers(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("
            SELECT m.*, p.nom AS produit_nom, p.unite,
                   u.prenom || ' ' || u.nom AS utilisateur_nom
            FROM mouvements_stock m
            JOIN produits     p ON m.produit_id     = p.id
            JOIN utilisateurs u ON m.utilisateur_id = u.id
            ORDER BY m.created_at DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByProduit(int $produitId, int $limit = 20): array
    {
        $stmt = $this->pdo->prepare("
            SELECT m.*, u.prenom || ' ' || u.nom AS utilisateur_nom
            FROM mouvements_stock m
            JOIN utilisateurs u ON m.utilisateur_id = u.id
            WHERE m.produit_id = :pid
            ORDER BY m.created_at DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':pid', $produitId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit,     PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStatsDuJour(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                COUNT(*) FILTER (WHERE type_mouvement = 'entree')  AS entrees,
                COUNT(*) FILTER (WHERE type_mouvement = 'sortie')  AS sorties,
                COUNT(*) FILTER (WHERE type_mouvement = 'perte')   AS pertes,
                COALESCE(SUM(quantite * prix_unitaire) FILTER (WHERE type_mouvement = 'sortie'), 0) AS ca_jour
            FROM mouvements_stock
            WHERE created_at >= CURRENT_DATE
        ");
        $stmt->execute();
        return $stmt->fetch() ?: [];
    }
}
