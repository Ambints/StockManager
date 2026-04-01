<div style="max-width:560px">
    <div class="d-flex align-center gap-3 mb-4">
        <a href="<?= APP_URL ?>/utilisateurs" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-circle"></i>
            <div><?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?></div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <span class="card-title"><?= $user ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' ?></span>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= APP_URL ?>/utilisateurs/<?= $user ? 'update' : 'store' ?>">
                <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">
                <?php if ($user && !empty($user['id'])): ?>
                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse email *</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Rôle *</label>
                        <select name="role" class="form-select" required>
                            <option value="vendeur"      <?= ($user['role'] ?? 'vendeur') === 'vendeur'      ? 'selected':'' ?>>Vendeur</option>
                            <option value="gestionnaire" <?= ($user['role'] ?? '') === 'gestionnaire' ? 'selected':'' ?>>Gestionnaire</option>
                            <option value="admin"        <?= ($user['role'] ?? '') === 'admin'        ? 'selected':'' ?>>Administrateur</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <div style="display:flex;align-items:center;height:38px;margin-top:6px;gap:10px">
                            <input type="checkbox" name="actif" id="actif" value="1"
                                   <?= ($user['actif'] ?? true) ? 'checked' : '' ?> style="width:16px;height:16px">
                            <label for="actif" style="cursor:pointer;font-size:13px">Compte actif</label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Mot de passe <?= $user ? '(laisser vide = inchangé)' : '*' ?></label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Min. 8 caractères" <?= !$user ? 'required' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirm" class="form-control" placeholder="Répéter le mot de passe">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> <?= $user ? 'Enregistrer' : 'Créer l\'utilisateur' ?>
                    </button>
                    <a href="<?= APP_URL ?>/utilisateurs" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
