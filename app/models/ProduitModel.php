<?php
require_once CORE_PATH . '/Model.php';

class ProduitModel extends Model
{
    protected string $table = 'produits';

    /** Recherche avec filtres et pagination */
    public function search(string $search, int $categorieId, string $statut, int $page): array
    {
        $conditions = ['p.actif = TRUE'];
        $params     = [];

        if (!empty($search)) {
            $conditions[] = "(p.nom ILIKE :search OR p.code_produit ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($categorieId > 0) {
            $conditions[] = "p.categorie_id = :cat";
            $params[':cat'] = $categorieId;
        }
        if ($statut === 'critique') {
            $conditions[] = "p.quantite_stock <= p.seuil_alerte AND p.quantite_stock > 0";
        } elseif ($statut === 'rupture') {
            $conditions[] = "p.quantite_stock = 0";
        } elseif ($statut === 'normal') {
            $conditions[] = "p.quantite_stock > p.seuil_alerte";
        }

        $where  = implode(' AND ', $conditions);
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $countSql = "SELECT COUNT(*) FROM produits p WHERE {$where}";
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT p.*, c.nom AS categorie_nom, f.nom AS fournisseur_nom
                FROM produits p
                LEFT JOIN categories   c ON p.categorie_id   = c.id
                LEFT JOIN fournisseurs f ON p.fournisseur_id  = f.id
                WHERE {$where}
                ORDER BY p.nom ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  ITEMS_PER_PAGE, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,        PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int)ceil($total / ITEMS_PER_PAGE),
            'page'  => $page,
        ];
    }

    /** Produits sous seuil pour le dashboard */
    public function getStockCritique(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM v_produits_stock_critique LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countActifs(): int
    {
        return $this->count('actif = TRUE');
    }

    public function countStockCritique(): int
    {
        return $this->count(
            'actif = TRUE AND quantite_stock <= seuil_alerte AND quantite_stock > 0'
        );
    }

    public function countRuptures(): int
    {
        return $this->count('actif = TRUE AND quantite_stock = 0');
    }

    public function codeExiste(string $code, int $excludeId = 0): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM produits WHERE code_produit = :code AND id != :id"
        );
        $stmt->execute([':code' => $code, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO produits
                (code_produit, nom, description, categorie_id, fournisseur_id,
                 prix_achat, prix_vente, quantite_stock, seuil_alerte, unite)
            VALUES
                (:code, :nom, :desc, :cat, :four,
                 :pa, :pv, :qs, :sa, :unite)
        ");
        return $stmt->execute([
            ':code'  => $data['code_produit'],
            ':nom'   => $data['nom'],
            ':desc'  => $data['description'] ?: null,
            ':cat'   => $data['categorie_id'] ?: null,
            ':four'  => $data['fournisseur_id'] ?: null,
            ':pa'    => $data['prix_achat'],
            ':pv'    => $data['prix_vente'],
            ':qs'    => $data['quantite_stock'],
            ':sa'    => $data['seuil_alerte'],
            ':unite' => $data['unite'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE produits SET
                code_produit   = :code,
                nom            = :nom,
                description    = :desc,
                categorie_id   = :cat,
                fournisseur_id = :four,
                prix_achat     = :pa,
                prix_vente     = :pv,
                seuil_alerte   = :sa,
                unite          = :unite,
                updated_at     = NOW()
            WHERE id = :id
        ");
        return $stmt->execute([
            ':code'  => $data['code_produit'],
            ':nom'   => $data['nom'],
            ':desc'  => $data['description'] ?: null,
            ':cat'   => $data['categorie_id'] ?: null,
            ':four'  => $data['fournisseur_id'] ?: null,
            ':pa'    => $data['prix_achat'],
            ':pv'    => $data['prix_vente'],
            ':sa'    => $data['seuil_alerte'],
            ':unite' => $data['unite'],
            ':id'    => $id,
        ]);
    }

    public function desactiver(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE produits SET actif = FALSE, updated_at = NOW() WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }
}
