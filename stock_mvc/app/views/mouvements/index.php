<?php
function mvtBadgeM(string $type): string {
    return match($type) {
        'entree'     => '<span class="badge badge-success">Entrée</span>',
        'sortie'     => '<span class="badge badge-info">Vente</span>',
        'perte'      => '<span class="badge badge-danger">Perte</span>',
        'ajustement' => '<span class="badge badge-warning">Ajustement</span>',
        default      => '<span class="badge badge-neutral">' . htmlspecialchars($type) . '</span>',
    };
}
?>

<div class="page-header">
    <div class="page-heading">
        <h2>Journal des mouvements</h2>
        <p>Historique complet des entrées, ventes, pertes et ajustements.</p>
    </div>
    <div class="page-actions">
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="<?= APP_URL ?>/produits" class="btn btn-outline"><i class="bi bi-boxes"></i> Produits</a>
        <a href="<?= APP_URL ?>/mouvements/entree" class="btn btn-success"><i class="bi bi-arrow-down-circle"></i> Entrée stock</a>
        <a href="<?= APP_URL ?>/mouvements/sortie" class="btn btn-primary"><i class="bi bi-arrow-up-circle"></i> Vente / Sortie</a>
    </div>
</div>

<!-- Filtres -->
<form method="GET" action="<?= APP_URL ?>/mouvements" class="filters-bar mb-3">
    <select name="type" class="form-select" style="min-width:150px">
        <option value="">Tous les types</option>
        <option value="entree"     <?= $type === 'entree'     ? 'selected':'' ?>>Entrées</option>
        <option value="sortie"     <?= $type === 'sortie'     ? 'selected':'' ?>>Ventes</option>
        <option value="perte"      <?= $type === 'perte'      ? 'selected':'' ?>>Pertes</option>
        <option value="ajustement" <?= $type === 'ajustement' ? 'selected':'' ?>>Ajustements</option>
    </select>
    <input type="date" name="debut" class="form-control" value="<?= htmlspecialchars($debut) ?>" style="width:auto">
    <input type="date" name="fin"   class="form-control" value="<?= htmlspecialchars($fin) ?>"   style="width:auto">
    <button type="submit" class="btn btn-outline"><i class="bi bi-search"></i> Filtrer</button>
    <?php if ($type || $debut || $fin): ?>
        <a href="<?= APP_URL ?>/mouvements" class="btn btn-outline"><i class="bi bi-x"></i></a>
    <?php endif; ?>
    <span class="text-sm text-muted" style="margin-left:auto"><?= $pagination['total'] ?> mouvement(s)</span>
</form>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($mouvements)): ?>
            <div class="empty-state"><i class="bi bi-arrow-left-right"></i><p>Aucun mouvement trouvé.</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th><th>Produit</th><th>Type</th><th>Qté</th>
                    <th>Avant</th><th>Après</th><th>Prix unit.</th>
                    <th>Réf. doc</th><th>Utilisateur</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($mouvements as $m): ?>
            <tr>
                <td class="text-sm text-muted text-mono"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                <td>
                    <a href="<?= APP_URL ?>/produits/show?id=<?= $m['produit_id'] ?>" class="fw-600">
                        <?= htmlspecialchars($m['produit_nom']) ?>
                    </a>
                    <div class="text-sm text-muted text-mono"><?= htmlspecialchars($m['code_produit']) ?></div>
                </td>
                <td><?= mvtBadgeM($m['type_mouvement']) ?></td>
                <td class="text-mono fw-600"><?= $m['quantite'] ?> <span class="text-muted"><?= htmlspecialchars($m['unite']) ?></span></td>
                <td class="text-mono text-muted"><?= $m['quantite_avant'] ?></td>
                <td class="text-mono"><?= $m['quantite_apres'] ?></td>
                <td class="text-mono text-sm"><?= $m['prix_unitaire'] ? number_format((float)$m['prix_unitaire'], 0, ',', ' ') . ' Ar' : '—' ?></td>
                <td class="text-sm"><?= htmlspecialchars($m['reference_doc'] ?? '—') ?></td>
                <td class="text-sm"><?= htmlspecialchars($m['utilisateur_nom']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php if ($pagination['pages'] > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
        <a href="?page=<?= $i ?>&type=<?= urlencode($type) ?>&debut=<?= urlencode($debut) ?>&fin=<?= urlencode($fin) ?>"
           class="<?= $i == $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
