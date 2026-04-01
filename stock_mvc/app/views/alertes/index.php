<div class="page-header">
    <div class="page-heading">
        <h2>Alertes de stock</h2>
        <p>Traitez rapidement les ruptures et niveaux critiques.</p>
    </div>
    <div class="page-actions">
        <a href="<?= APP_URL ?>/dashboard" class="btn btn-outline"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="<?= APP_URL ?>/produits" class="btn btn-outline"><i class="bi bi-boxes"></i> Produits</a>
        <a href="<?= APP_URL ?>/mouvements" class="btn btn-outline"><i class="bi bi-arrow-left-right"></i> Mouvements</a>
        <a href="?statut=non_resolues" class="btn <?= $statut !== 'toutes' ? 'btn-primary' : 'btn-outline' ?>">
            Non résolues <?php if ($nbNonResolues): ?><span class="badge badge-danger" style="margin-left:4px"><?= $nbNonResolues ?></span><?php endif; ?>
        </a>
        <a href="?statut=toutes" class="btn <?= $statut === 'toutes' ? 'btn-primary' : 'btn-outline' ?>">
            Toutes les alertes
        </a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($alertes)): ?>
            <div class="empty-state">
                <i class="bi bi-check-circle" style="color:var(--success)"></i>
                <p><?= $statut !== 'toutes' ? 'Aucune alerte non résolue. Tout est en ordre !' : 'Aucune alerte.' ?></p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>Date</th><th>Produit</th><th>Type</th><th>Message</th><th>Statut</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($alertes as $a): ?>
            <tr>
                <td class="text-sm text-muted text-mono"><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></td>
                <td>
                    <a href="<?= APP_URL ?>/produits/show?id=<?= $a['produit_id'] ?>" class="fw-600">
                        <?= htmlspecialchars($a['produit_nom']) ?>
                    </a>
                    <div class="text-sm text-muted text-mono"><?= htmlspecialchars($a['code_produit']) ?></div>
                </td>
                <td>
                    <?php if ($a['type_alerte'] === 'rupture_stock'): ?>
                        <span class="badge badge-danger"><i class="bi bi-x-circle"></i> Rupture</span>
                    <?php elseif ($a['type_alerte'] === 'stock_critique'): ?>
                        <span class="badge badge-warning"><i class="bi bi-exclamation-triangle"></i> Critique</span>
                    <?php else: ?>
                        <span class="badge badge-info"><?= htmlspecialchars($a['type_alerte']) ?></span>
                    <?php endif; ?>
                </td>
                <td style="font-size:12.5px;max-width:300px"><?= htmlspecialchars($a['message']) ?></td>
                <td>
                    <?php if ($a['resolue']): ?>
                        <span class="badge badge-success"><i class="bi bi-check"></i> Résolue</span>
                        <div class="text-sm text-muted mt-1"><?= htmlspecialchars($a['resolue_par_nom'] ?? '') ?></div>
                    <?php else: ?>
                        <span class="badge badge-danger">En cours</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$a['resolue']): ?>
                        <form method="POST" action="<?= APP_URL ?>/alertes/resoudre">
                            <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-check-lg"></i> Résoudre
                            </button>
                        </form>
                    <?php else: ?>—<?php endif; ?>
                </td>
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
        <a href="?page=<?= $i ?>&statut=<?= urlencode($statut) ?>" class="<?= $i == $pagination['page'] ? 'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
