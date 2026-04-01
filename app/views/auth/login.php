<div class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <div class="brand-icon"><i class="bi bi-box-seam-fill"></i></div>
            <span class="brand-name">StockManager</span>
        </div>

        <h2 style="font-size:20px;font-weight:600;margin-bottom:6px;">Connexion</h2>
        <p style="font-size:13px;color:var(--text-3);margin-bottom:24px;">Accédez à votre espace de gestion.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="margin-bottom:16px;">
                <i class="bi bi-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $e): ?>
                        <div><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= APP_URL ?>/login">
            <input type="hidden" name="csrf_token" value="<?= Controller::csrfToken() ?>">

            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($email ?? '') ?>"
                       placeholder="admin@boutique.mg" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Mot de passe</label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="••••••••" required style="padding-right:40px;">
                    <button type="button" onclick="togglePwd()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-3);">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </button>
        </form>
    </div>
</div>
<script>
function togglePwd() {
    const i = document.getElementById('password');
    const e = document.getElementById('eyeIcon');
    if (i.type === 'password') {
        i.type = 'text';
        e.className = 'bi bi-eye-slash';
    } else {
        i.type = 'password';
        e.className = 'bi bi-eye';
    }
}
</script>
