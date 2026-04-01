<?php
require_once CORE_PATH . '/Model.php';

class CategorieModel extends Model
{
    protected string $table = 'categories';

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (nom, description) VALUES (:nom, :desc)"
        );
        return $stmt->execute([':nom' => $data['nom'], ':desc' => $data['description'] ?: null]);
    }
}
