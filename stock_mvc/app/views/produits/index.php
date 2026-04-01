<?php
function stockClass(int $stock, int $seuil): string {
    if ($stock === 0)         return 'danger';
    if ($stock <= $seuil)     return 'warning';
    return 'ok';
}
?>

<div class="page-header">
    <div class="page-heading">
        <h2>Catalogue produits</h2>
        <p>Consultez les niveaux de stock et accédez rapidement aux opérations.</p>
    </div>
    <div class="page-actions">
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="<?= APP_URL ?>/mouvements" class="btn btn-outline"><i class="bi bi-arrow-left-right"></i> Mouvements</a>
        <?php if (in_array($_SESSION['user']['role'], ['admin','gestionnaire'])): ?>
            <a href="<?= APP_URL ?>/produits/create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nouveau produit</a>
        <?php endif; ?>
    </div>
</div>

<!-- Filtres -->
<form method="GET" action="<?= APP_URL ?>/produits" class="filters-bar mb-3">
    <input type="text" name="search" class="form-control filter-search"
           placeholder="Rechercher (nom, code)…" value="<?= htmlspecialchars($search) ?>">
    <select name="categorie" class="form-select" style="min-width:160px">
        <option value="">Toutes catégories</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $categorie == $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="statut" class="form-select" style="min-width:140px">
        <option value="">Tous statuts</option>
        <option value="normal"   <?= $statut === 'normal'   ? 'selected':'' ?>>Normal</option>
        <option value="critique" <?= $statut === 'critique' ? 'selected':'' ?>>Critique</option>
        <option value="rupture"  <?= $statut === 'rupture'  ? 'selected':'' ?>>Rupture</option>
    </select>
    <button type="submit" class="btn btn-outline"><i class="bi bi-search"></i> Filtrer</button>
    <?php if ($search || $categorie || $statut): ?>
        <a href="<?= APP_URL ?>/produits" class="btn btn-outline"><i class="bi bi-x"></i> Réinitialiser</a>
    <?php endif; ?>
    <span class="text-sm text-muted" style="margin-left:auto"><?= $pagination['total'] ?> produit(s)</span>
</form>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($produits)): ?>
            <div class="empty-state"><i class="bi bi-boxes"></i><p>Aucun produit trouvé.</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Prix vente</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($produits as $p):
                $cls = stockClass((int)$p['quantite_stock'], (int)$p['seuil_alerte']);
                $pct = $p['seuil_alerte'] > 0
                    ? min(100, round(($p['quantite_stock'] / ($p['seuil_alerte'] * 3)) * 100))
                    : ($p['quantite_stock'] > 0 ? 100 : 0);
            ?>
            <tr>
                <td><span class="text-mono text-sm"><?= htmlspecialchars($p['code_produit']) ?></span></td>
                <td>
                    <a href="<?= APP_URL ?>/produits/show?id=<?= $p['id'] ?>" class="fw-600">
                        <?= htmlspecialchars($p['nom']) ?>
                    </a>
                    <?php if ($p['fournisseur_nom']): ?>
                        <div class="text-sm text-muted"><?= htmlspecialchars($p['fournisseur_nom']) ?></div>
                    <?php endif; ?>
                </td>
                <td><?= $p['categorie_nom'] ? '<span class="badge badge-neutral">'.htmlspecialchars($p['categorie_nom']).'</span>' : '—' ?></td>
                <td class="text-mono"><?= number_format((float)$p['prix_vente'], 0, ',', ' ') ?> Ar</td>
                <td>
                    <div class="fw-600"><?= $p['quantite_stock'] ?> <span class="text-muted text-sm"><?= htmlspecialchars($p['unite']) ?></span></div>
                    <div class="stock-bar mt-1"><div class="stock-bar-fill <?= $cls ?>" style="width:<?= $pct ?>%"></div></div>
                </td>
                <td>
                    <?php if ($p['quantite_stock'] == 0): ?>
                        <span class="badge badge-danger">Rupture</span>
                    <?php elseif ($p['quantite_stock'] <= $p['seuil_alerte']): ?>
                        <span class="badge badge-warning">Critique</span>
                    <?php else: ?>
                        <span class="badge badge-success">Normal</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="<?= APP_URL ?>/produits/show?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline btn-icon" title="Voir"><i class="bi bi-eye"></i></a>
                        <?php if (in_array($_SESSION['user']['role'], ['admin','gestionnaire'])): ?>
                        <a href="<?= APP_URL ?>/produits/edit?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline btn-icon" title="Modifier"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="<?= APP_URL ?>/produits/delete" onsubmit="return confirm('Désactiver ce produit ?')">
                            <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Désactiver"><i class="bi bi-trash"></i></button>
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

<!-- Pagination -->
<?php if ($pagination['pages'] > 1): ?>
<div class="pagination">
    <?php if ($pagination['page'] > 1): ?>
        <a href="?page=<?= $pagination['page']-1 ?>&search=<?= urlencode($search) ?>&categorie=<?= $categorie ?>&statut=<?= $statut ?>">‹</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&categorie=<?= $categorie ?>&statut=<?= $statut ?>"
           class="<?= $i == $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($pagination['page'] < $pagination['pages']): ?>
        <a href="?page=<?= $pagination['page']+1 ?>&search=<?= urlencode($search) ?>&categorie=<?= $categorie ?>&statut=<?= $statut ?>">›</a>
    <?php endif; ?>
    <span class="pagination-info"><?= $pagination['total'] ?> résultats</span>
</div>
<?php endif; ?>
