<div style="max-width:600px">

    <div class="d-flex align-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/mouvements" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-circle"></i>
            <div><?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?></div>
        </div>
    <?php endif; ?>

    <?php
    $isEntree = $type === 'entree';
    $typesDisponibles = $isEntree
        ? ['entree' => 'Entrée (réapprovisionnement)', 'ajustement' => 'Ajustement d\'inventaire']
        : ['sortie' => 'Vente', 'perte' => 'Perte / Casse'];
    $iconClass = $isEntree ? 'success' : 'primary';
    $iconName  = $isEntree ? 'arrow-down-circle' : 'arrow-up-circle';
    ?>

    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="bi bi-<?= $iconName ?>" style="color:var(--<?= $iconClass ?>)"></i>
                <?= $isEntree ? 'Entrée de stock' : 'Sortie de stock' ?>
            </span>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= APP_URL ?>/mouvements/store">
                <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">

                <div class="form-group">
                    <label class="form-label">Produit *</label>
                    <select name="produit_id" class="form-select" required id="produitSelect"
                            onchange="loadProduitInfo(this.value)">
                        <option value="">— Sélectionner un produit —</option>
                        <?php foreach ($produits as $p): ?>
                            <option value="<?= $p['id'] ?>"
                                data-stock="<?= $p['quantite_stock'] ?>"
                                data-unite="<?= htmlspecialchars($p['unite']) ?>"
                                data-prix="<?= $p['prix_vente'] ?>"
                                <?= ($old['produit_id'] ?? '') == $p['id'] ? 'selected':'' ?>>
                                [<?= htmlspecialchars($p['code_produit']) ?>] <?= htmlspecialchars($p['nom']) ?>
                                — Stock: <?= $p['quantite_stock'] ?> <?= htmlspecialchars($p['unite']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Info stock temps réel -->
                <div id="stockInfo" style="display:none;margin-bottom:16px">
                    <div class="alert alert-warning" style="margin:0">
                        <i class="bi bi-info-circle"></i>
                        Stock actuel : <strong id="stockQte">—</strong> <span id="stockUnite"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Type de mouvement *</label>
                    <select name="type_mouvement" class="form-select" required>
                        <?php foreach ($typesDisponibles as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ($old['type_mouvement'] ?? $val) === $val ? 'selected':'' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Quantité *</label>
                        <input type="number" name="quantite" class="form-control"
                               value="<?= (int)($old['quantite'] ?? 1) ?>"
                               min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prix unitaire (Ar)</label>
                        <input type="number" name="prix_unitaire" id="prixInput" class="form-control"
                               value="<?= htmlspecialchars($old['prix_unitaire'] ?? '') ?>"
                               min="0" step="1" placeholder="Optionnel">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Référence document</label>
                        <input type="text" name="reference_doc" class="form-control text-mono"
                               value="<?= htmlspecialchars($old['reference_doc'] ?? '') ?>"
                               placeholder="N° facture, bon livraison…">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Motif</label>
                        <input type="text" name="motif" class="form-control"
                               value="<?= htmlspecialchars($old['motif'] ?? '') ?>"
                               placeholder="Raison du mouvement">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-<?= $iconClass ?>">
                        <i class="bi bi-check-lg"></i> Enregistrer le mouvement
                    </button>
                    <a href="<?= APP_URL ?>/mouvements" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadProduitInfo(id) {
    const sel  = document.getElementById('produitSelect');
    const opt  = sel.options[sel.selectedIndex];
    const info = document.getElementById('stockInfo');
    if (!id) { info.style.display = 'none'; return; }
    document.getElementById('stockQte').textContent   = opt.dataset.stock;
    document.getElementById('stockUnite').textContent = opt.dataset.unite;
    document.getElementById('prixInput').value        = opt.dataset.prix || '';
    info.style.display = 'block';
}
// Init si valeur pré-sélectionnée
document.addEventListener('DOMContentLoaded', () => {
    const s = document.getElementById('produitSelect');
    if (s.value) loadProduitInfo(s.value);
});
</script>
