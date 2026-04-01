<div class="page-header">
    <div class="page-heading">
        <h2>Fournisseurs</h2>
        <p>Centralisez les partenaires d'approvisionnement de vos produits.</p>
    </div>
    <div class="page-actions">
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="<?= APP_URL ?>/produits" class="btn btn-outline"><i class="bi bi-boxes"></i> Produits</a>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><span class="card-title">Fournisseurs</span></div>
        <div class="table-wrap">
            <?php if (empty($fournisseurs)): ?>
                <div class="empty-state"><i class="bi bi-truck"></i><p>Aucun fournisseur.</p></div>
            <?php else: ?>
            <table>
                <thead><tr><th>Nom</th><th>Contact</th><th>Téléphone</th><th>Email</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($fournisseurs as $f): ?>
                <tr>
                    <td class="fw-600"><?= htmlspecialchars($f['nom']) ?></td>
                    <td><?= htmlspecialchars($f['contact'] ?? '—') ?></td>
                    <td class="text-mono text-sm"><?= htmlspecialchars($f['telephone'] ?? '—') ?></td>
                    <td class="text-sm"><?= htmlspecialchars($f['email'] ?? '—') ?></td>
                    <td>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <form method="POST" action="<?= APP_URL ?>/fournisseurs/delete" onsubmit="return confirm('Supprimer ?')">
                            <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="card" style="align-self:start">
        <div class="card-header"><span class="card-title">Nouveau fournisseur</span></div>
        <div class="card-body">
            <form method="POST" action="<?= APP_URL ?>/fournisseurs/store">
                <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" required placeholder="Raison sociale">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Personne de contact</label>
                        <input type="text" name="contact" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Ajouter</button>
            </form>
        </div>
    </div>
</div>
