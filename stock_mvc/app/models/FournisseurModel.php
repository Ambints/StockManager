<?php
require_once CORE_PATH . '/Model.php';

class FournisseurModel extends Model
{
    protected string $table = 'fournisseurs';

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO fournisseurs (nom, contact, telephone, email, adresse)
            VALUES (:nom, :contact, :tel, :email, :adr)
        ");
        return $stmt->execute([
            ':nom'     => $data['nom'],
            ':contact' => $data['contact'] ?: null,
            ':tel'     => $data['telephone'] ?: null,
            ':email'   => $data['email'] ?: null,
            ':adr'     => $data['adresse'] ?: null,
        ]);
    }
}
