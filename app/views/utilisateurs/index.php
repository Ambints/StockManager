<div class="page-header">
    <div class="page-heading">
        <h2>Gestion des utilisateurs</h2>
        <p>Administrez les accès, rôles et statuts de compte.</p>
    </div>
    <div class="page-actions">
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="<?= APP_URL ?>/utilisateurs/create" class="btn btn-primary"><i class="bi bi-person-plus"></i> Nouvel utilisateur</a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($utilisateurs)): ?>
            <div class="empty-state"><i class="bi bi-people"></i><p>Aucun utilisateur.</p></div>
        <?php else: ?>
        <table>
            <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Créé le</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($utilisateurs as $u): ?>
            <tr>
                <td>
                    <div class="d-flex align-center gap-2">
                        <div class="user-avatar" style="width:28px;height:28px;font-size:11px;background:var(--accent)">
                            <?= strtoupper(substr($u['prenom'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-600"><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></div>
                        </div>
                    </div>
                </td>
                <td class="text-mono text-sm"><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <?php $roleColors = ['admin'=>'danger','gestionnaire'=>'warning','vendeur'=>'info']; ?>
                    <span class="badge badge-<?= $roleColors[$u['role']] ?? 'neutral' ?>"><?= ucfirst($u['role']) ?></span>
                </td>
                <td>
                    <?php if ($u['actif']): ?>
                        <span class="badge badge-success">Actif</span>
                    <?php else: ?>
                        <span class="badge badge-neutral">Inactif</span>
                    <?php endif; ?>
                </td>
                <td class="text-sm text-muted"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="<?= APP_URL ?>/utilisateurs/edit?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline btn-icon"><i class="bi bi-pencil"></i></a>
                        <?php if ($u['id'] !== (int)$_SESSION['user']['id']): ?>
                        <form method="POST" action="<?= APP_URL ?>/utilisateurs/delete" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
