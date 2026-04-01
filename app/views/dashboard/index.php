<?php
// Helper local
function mvtBadge(string $type): string {
    return match($type) {
        'entree'     => '<span class="badge badge-success"><i class="bi bi-arrow-down-circle"></i>Entrée</span>',
        'sortie'     => '<span class="badge badge-info"><i class="bi bi-arrow-up-circle"></i>Vente</span>',
        'perte'      => '<span class="badge badge-danger"><i class="bi bi-dash-circle"></i>Perte</span>',
        'ajustement' => '<span class="badge badge-warning"><i class="bi bi-pencil"></i>Ajust.</span>',
        default      => '<span class="badge badge-neutral">' . htmlspecialchars($type) . '</span>',
    };
}
?>

<div class="page-header">
    <div class="page-heading">
        <h2>Vue d'ensemble du stock</h2>
        <p>Suivez vos produits, mouvements et alertes depuis un tableau central.</p>
    </div>
    <div class="page-actions">
        <a href="<?= APP_URL ?>/produits" class="btn btn-outline"><i class="bi bi-boxes"></i> Produits</a>
        <a href="<?= APP_URL ?>/mouvements" class="btn btn-outline"><i class="bi bi-arrow-left-right"></i> Mouvements</a>
        <a href="<?= APP_URL ?>/alertes" class="btn btn-outline"><i class="bi bi-bell"></i> Alertes</a>
    </div>
</div>

<div class="quick-links">
    <a href="<?= APP_URL ?>/produits/create" class="quick-link-card">
        <i class="bi bi-plus-circle"></i><span>Ajouter un produit</span>
    </a>
    <a href="<?= APP_URL ?>/mouvements/entree" class="quick-link-card">
        <i class="bi bi-arrow-down-circle"></i><span>Entrée de stock</span>
    </a>
    <a href="<?= APP_URL ?>/mouvements/sortie" class="quick-link-card">
        <i class="bi bi-arrow-up-circle"></i><span>Sortie / vente</span>
    </a>
    <a href="<?= APP_URL ?>/categories" class="quick-link-card">
        <i class="bi bi-tags"></i><span>Catégories</span>
    </a>
</div>

<!-- Stat cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-boxes"></i></div>
        <div>
            <div class="stat-value"><?= $totalProduits ?></div>
            <div class="stat-label">Produits actifs</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-exclamation-triangle"></i></div>
        <div>
            <div class="stat-value"><?= $totalStockCritique ?></div>
            <div class="stat-label">Stock critique</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
        <div>
            <div class="stat-value"><?= $totalRuptures ?></div>
            <div class="stat-label">Ruptures de stock</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-currency-dollar"></i></div>
        <div>
            <div class="stat-value"><?= number_format((float)($statsJour['ca_jour'] ?? 0), 0, ',', ' ') ?></div>
            <div class="stat-label">CA aujourd'hui (Ar)</div>
        </div>
    </div>
</div>

<!-- Quick actions -->
<div class="d-flex gap-2 mb-4">
    <a href="<?= APP_URL ?>/mouvements/entree" class="btn btn-success"><i class="bi bi-arrow-down-circle"></i> Entrée stock</a>
    <a href="<?= APP_URL ?>/mouvements/sortie" class="btn btn-primary"><i class="bi bi-arrow-up-circle"></i> Vente / Sortie</a>
    <a href="<?= APP_URL ?>/produits/create"   class="btn btn-outline"><i class="bi bi-plus-circle"></i> Nouveau produit</a>
</div>

<div class="grid-2">

    <!-- Produits en alerte -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-exclamation-triangle" style="color:var(--warning)"></i> Alertes stock</span>
            <a href="<?= APP_URL ?>/alertes" class="btn btn-sm btn-outline">Voir tout</a>
        </div>
        <?php if (empty($produitsCritiques)): ?>
            <div class="empty-state"><i class="bi bi-check-circle" style="color:var(--success)"></i><p>Aucun produit en alerte</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Produit</th><th>Stock</th><th>Statut</th></tr></thead>
                <tbody>
                <?php foreach ($produitsCritiques as $p):
                    $pct = $p['seuil_alerte'] > 0 ? min(100, round(($p['quantite_stock'] / $p['seuil_alerte']) * 100)) : 0;
                    $cls = $p['statut_stock'] === 'rupture' ? 'danger' : 'warning';
                ?>
                <tr>
                    <td>
                        <a href="<?= APP_URL ?>/produits/show?id=<?= $p['id'] ?>" style="font-weight:500"><?= htmlspecialchars($p['nom']) ?></a>
                        <div class="text-sm text-muted text-mono"><?= htmlspecialchars($p['code_produit']) ?></div>
                    </td>
                    <td>
                        <div class="fw-600"><?= $p['quantite_stock'] ?> <span class="text-muted text-sm"><?= htmlspecialchars($p['unite']) ?></span></div>
                        <div class="stock-bar mt-1"><div class="stock-bar-fill <?= $cls ?>" style="width:<?= $pct ?>%"></div></div>
                    </td>
                    <td>
                        <?php if ($p['statut_stock'] === 'rupture'): ?>
                            <span class="badge badge-danger">Rupture</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Critique</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Derniers mouvements -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-clock-history" style="color:var(--accent)"></i> Derniers mouvements</span>
            <a href="<?= APP_URL ?>/mouvements" class="btn btn-sm btn-outline">Journal complet</a>
        </div>
        <?php if (empty($derniersMovements)): ?>
            <div class="empty-state"><i class="bi bi-inbox"></i><p>Aucun mouvement enregistré</p></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Produit</th><th>Type</th><th>Qté</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($derniersMovements as $m): ?>
                <tr>
                    <td style="font-weight:500;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        <?= htmlspecialchars($m['produit_nom']) ?>
                    </td>
                    <td><?= mvtBadge($m['type_mouvement']) ?></td>
                    <td class="text-mono"><?= $m['quantite'] ?></td>
                    <td class="text-sm text-muted"><?= date('d/m H:i', strtotime($m['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>
