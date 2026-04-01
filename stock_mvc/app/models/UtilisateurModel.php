<?php
require_once CORE_PATH . '/Model.php';

class UtilisateurModel extends Model
{
    protected string $table = 'utilisateurs';

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM utilisateurs WHERE LOWER(email) = LOWER(:email) LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function emailExiste(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM utilisateurs WHERE email = :email AND id != :id"
        );
        $stmt->execute([':email' => $email, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe_hash, role, actif)
            VALUES (:nom, :prenom, :email, :hash, :role, :actif)
        ");
        return $stmt->execute([
            ':nom'    => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email'  => $data['email'],
            ':hash'   => $data['mot_de_passe_hash'],
            ':role'   => $data['role'],
            ':actif'  => $data['actif'] ? 'TRUE' : 'FALSE',
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $setParts = [
            "nom = :nom", "prenom = :prenom", "email = :email",
            "role = :role", "actif = :actif", "updated_at = NOW()"
        ];
        $params = [
            ':nom'    => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email'  => $data['email'],
            ':role'   => $data['role'],
            ':actif'  => $data['actif'] ? 'TRUE' : 'FALSE',
            ':id'     => $id,
        ];

        if (!empty($data['mot_de_passe_hash'])) {
            $setParts[] = "mot_de_passe_hash = :hash";
            $params[':hash'] = $data['mot_de_passe_hash'];
        }

        $sql  = "UPDATE utilisateurs SET " . implode(', ', $setParts) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
