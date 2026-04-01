<div style="max-width:720px">

    <div class="d-flex align-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/produits" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-circle"></i>
            <div><?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?></div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <span class="card-title"><?= $produit ? 'Modifier le produit' : 'Nouveau produit' ?></span>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= APP_URL ?>/produits/<?= $produit ? 'update' : 'store' ?>">
                <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                <?php if ($produit && !empty($produit['id'])): ?>
                    <input type="hidden" name="id" value="<?= (int)$produit['id'] ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Code produit *</label>
                        <input type="text" name="code_produit" class="form-control text-mono"
                               value="<?= htmlspecialchars($produit['code_produit'] ?? '') ?>"
                               placeholder="ex: PROD-0001" required>
                        <span class="form-hint">SKU ou référence unique</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom du produit *</label>
                        <input type="text" name="nom" class="form-control"
                               value="<?= htmlspecialchars($produit['nom'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"><?= htmlspecialchars($produit['description'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Catégorie</label>
                        <select name="categorie_id" class="form-select">
                            <option value="">— Aucune —</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($produit['categorie_id'] ?? '') == $c['id'] ? 'selected':'' ?>>
                                    <?= htmlspecialchars($c['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fournisseur</label>
                        <select name="fournisseur_id" class="form-select">
                            <option value="">— Aucun —</option>
                            <?php foreach ($fournisseurs as $f): ?>
                                <option value="<?= $f['id'] ?>" <?= ($produit['fournisseur_id'] ?? '') == $f['id'] ? 'selected':'' ?>>
                                    <?= htmlspecialchars($f['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prix d'achat (Ar)</label>
                        <input type="number" name="prix_achat" class="form-control"
                               value="<?= htmlspecialchars($produit['prix_achat'] ?? '0') ?>"
                               min="0" step="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Prix de vente (Ar)</label>
                        <input type="number" name="prix_vente" class="form-control"
                               value="<?= htmlspecialchars($produit['prix_vente'] ?? '0') ?>"
                               min="0" step="1" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stock initial</label>
                        <input type="number" name="quantite_stock" class="form-control"
                               value="<?= (int)($produit['quantite_stock'] ?? 0) ?>"
                               min="0" <?= $produit ? 'readonly style="background:var(--surface-2)"' : '' ?>>
                        <?php if ($produit): ?>
                            <span class="form-hint">Modifiable uniquement via les mouvements de stock.</span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Seuil d'alerte</label>
                        <input type="number" name="seuil_alerte" class="form-control"
                               value="<?= (int)($produit['seuil_alerte'] ?? 5) ?>" min="0" required>
                        <span class="form-hint">Déclenche une alerte en dessous de cette quantité.</span>
                    </div>
                </div>

                <div class="form-group" style="max-width:200px">
                    <label class="form-label">Unité de mesure</label>
                    <input type="text" name="unite" class="form-control"
                           value="<?= htmlspecialchars($produit['unite'] ?? 'unité') ?>"
                           placeholder="unité, kg, litre…">
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> <?= $produit ? 'Enregistrer' : 'Créer le produit' ?>
                    </button>
                    <a href="<?= APP_URL ?>/produits" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
