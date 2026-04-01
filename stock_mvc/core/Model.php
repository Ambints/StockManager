<?php
/**
 * Modèle de base — toutes les classes model en héritent
 * Fournit un accès PDO et des helpers communs
 */
abstract class Model
{
    protected PDO    $pdo;
    protected string $table  = '';
    protected string $pk     = 'id';

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    // ── Méthodes CRUD génériques ──────────────────────────

    /**
     * Récupère tous les enregistrements
     */
    public function findAll(string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $stmt = $this->pdo->query(
            "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}"
        );
        return $stmt->fetchAll();
    }

    /**
     * Récupère un enregistrement par sa clé primaire
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->pk} = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Supprime un enregistrement par sa clé primaire
     */
    public function deleteById(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM {$this->table} WHERE {$this->pk} = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Compte les enregistrements (avec filtre optionnel)
     */
    public function count(string $where = '', array $params = []): int
    {
        $sql  = "SELECT COUNT(*) FROM {$this->table}";
        $sql .= $where ? " WHERE {$where}" : '';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Pagination — retourne un tableau [items, total, pages]
     */
    public function paginate(
        int    $page       = 1,
        int    $perPage    = ITEMS_PER_PAGE,
        string $where      = '',
        array  $params     = [],
        string $orderBy    = 'id',
        string $direction  = 'DESC'
    ): array {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $offset    = ($page - 1) * $perPage;
        $total     = $this->count($where, $params);

        $sql  = "SELECT * FROM {$this->table}";
        $sql .= $where ? " WHERE {$where}" : '';
        $sql .= " ORDER BY {$orderBy} {$direction}";
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
            'page'  => $page,
        ];
    }

    // ── Helpers de sécurité ───────────────────────────────

    /**
     * Nettoie une chaîne pour l'affichage HTML (XSS)
     */
    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
