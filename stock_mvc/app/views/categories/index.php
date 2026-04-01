<div class="page-header">
    <div class="page-heading">
        <h2>Catégories produits</h2>
        <p>Structurez votre catalogue pour faciliter les recherches et rapports.</p>
    </div>
    <div class="page-actions">
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="<?= APP_URL ?>/produits" class="btn btn-outline"><i class="bi bi-boxes"></i> Produits</a>
    </div>
</div>

<div class="grid-2">
    <!-- Liste -->
    <div class="card">
        <div class="card-header"><span class="card-title">Catégories existantes</span></div>
        <div class="table-wrap">
            <?php if (empty($categories)): ?>
                <div class="empty-state"><i class="bi bi-tags"></i><p>Aucune catégorie.</p></div>
            <?php else: ?>
            <table>
                <thead><tr><th>Nom</th><th>Description</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td class="fw-600"><?= htmlspecialchars($c['nom']) ?></td>
                    <td class="text-sm text-muted"><?= htmlspecialchars($c['description'] ?? '—') ?></td>
                    <td>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <form method="POST" action="<?= APP_URL ?>/categories/delete" onsubmit="return confirm('Supprimer ?')">
                            <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
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

    <!-- Formulaire -->
    <div class="card" style="align-self:start">
        <div class="card-header"><span class="card-title">Nouvelle catégorie</span></div>
        <div class="card-body">
            <form method="POST" action="<?= APP_URL ?>/categories/store">
                <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" required placeholder="ex: Électronique">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Créer</button>
            </form>
        </div>
    </div>
</div>
