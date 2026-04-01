<div style="max-width:900px">
    <div class="d-flex align-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/produits" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
        <?php if (in_array($_SESSION['user']['role'], ['admin','gestionnaire'])): ?>
            <a href="<?= APP_URL ?>/produits/edit?id=<?= $produit['id'] ?>" class="btn btn-outline btn-sm"><i class="bi bi-pencil"></i> Modifier</a>
        <?php endif; ?>
        <a href="<?= APP_URL ?>/mouvements/entree?produit=<?= $produit['id'] ?>" class="btn btn-success btn-sm"><i class="bi bi-arrow-down-circle"></i> Entrée</a>
        <a href="<?= APP_URL ?>/mouvements/sortie?produit=<?= $produit['id'] ?>" class="btn btn-primary btn-sm"><i class="bi bi-arrow-up-circle"></i> Sortie</a>
    </div>

    <div class="grid-2 mb-4">
        <!-- Infos produit -->
        <div class="card">
            <div class="card-header"><span class="card-title">Informations</span></div>
            <div class="card-body">
                <?php
                $cls = $produit['quantite_stock'] == 0 ? 'danger'
                    : ($produit['quantite_stock'] <= $produit['seuil_alerte'] ? 'warning' : 'success');
                ?>
                <div style="margin-bottom:16px">
                    <div class="text-sm text-muted mb-1">Code produit</div>
                    <div class="text-mono fw-600"><?= htmlspecialchars($produit['code_produit']) ?></div>
                </div>
                <div style="margin-bottom:16px">
                    <div class="text-sm text-muted mb-1">Stock actuel</div>
                    <div style="font-size:28px;font-weight:700;font-family:var(--mono);color:var(--<?= $cls ?>)">
                        <?= $produit['quantite_stock'] ?>
                        <span style="font-size:14px;font-weight:400"><?= htmlspecialchars($produit['unite']) ?></span>
                    </div>
                    <?php if ($produit['quantite_stock'] <= $produit['seuil_alerte']): ?>
                        <div class="text-sm" style="color:var(--<?= $cls ?>);margin-top:4px">
                            Seuil d'alerte : <?= $produit['seuil_alerte'] ?> <?= htmlspecialchars($produit['unite']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-row" style="margin-bottom:0">
                    <div>
                        <div class="text-sm text-muted mb-1">Prix d'achat</div>
                        <div class="text-mono"><?= number_format((float)$produit['prix_achat'], 0, ',', ' ') ?> Ar</div>
                    </div>
                    <div>
                        <div class="text-sm text-muted mb-1">Prix de vente</div>
                        <div class="text-mono fw-600"><?= number_format((float)$produit['prix_vente'], 0, ',', ' ') ?> Ar</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta -->
        <div class="card">
            <div class="card-header"><span class="card-title">Détails</span></div>
            <div class="card-body">
                <?php foreach ([
                    'Catégorie'    => $produit['categorie_nom']   ?? '—',
                    'Fournisseur'  => $produit['fournisseur_nom'] ?? '—',
                    'Unité'        => $produit['unite'],
                    'Seuil alerte' => $produit['seuil_alerte'] . ' ' . $produit['unite'],
                ] as $label => $val): ?>
                <div style="margin-bottom:14px">
                    <div class="text-sm text-muted mb-1"><?= $label ?></div>
                    <div><?= htmlspecialchars($val) ?></div>
                </div>
                <?php endforeach; ?>
                <?php if ($produit['description']): ?>
                <div>
                    <div class="text-sm text-muted mb-1">Description</div>
                    <div style="font-size:13px"><?= nl2br(htmlspecialchars($produit['description'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Historique des mouvements -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Historique des mouvements</span>
        </div>
        <div class="table-wrap">
            <?php if (empty($mouvements)): ?>
                <div class="empty-state"><i class="bi bi-clock-history"></i><p>Aucun mouvement enregistré.</p></div>
            <?php else: ?>
            <table>
                <thead><tr><th>Date</th><th>Type</th><th>Qté</th><th>Avant</th><th>Après</th><th>Prix unit.</th><th>Motif</th><th>Ref</th><th>Par</th></tr></thead>
                <tbody>
                <?php foreach ($mouvements as $m): ?>
                <tr>
                    <td class="text-sm text-muted text-mono"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                    <td>
                        <?= match($m['type_mouvement']) {
                            'entree'     => '<span class="badge badge-success">Entrée</span>',
                            'sortie'     => '<span class="badge badge-info">Vente</span>',
                            'perte'      => '<span class="badge badge-danger">Perte</span>',
                            'ajustement' => '<span class="badge badge-warning">Ajust.</span>',
                            default      => '<span class="badge badge-neutral">' . htmlspecialchars($m['type_mouvement']) . '</span>',
                        } ?>
                    </td>
                    <td class="text-mono fw-600"><?= $m['quantite'] ?></td>
                    <td class="text-mono text-muted"><?= $m['quantite_avant'] ?></td>
                    <td class="text-mono"><?= $m['quantite_apres'] ?></td>
                    <td class="text-sm text-mono"><?= $m['prix_unitaire'] ? number_format((float)$m['prix_unitaire'], 0, ',', ' ') . ' Ar' : '—' ?></td>
                    <td class="text-sm"><?= htmlspecialchars($m['motif'] ?? '—') ?></td>
                    <td class="text-sm text-mono"><?= htmlspecialchars($m['reference_doc'] ?? '—') ?></td>
                    <td class="text-sm"><?= htmlspecialchars($m['utilisateur_nom']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
