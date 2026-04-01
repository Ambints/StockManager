<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'StockManager') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= APP_ASSET_URL ?>/css/app.css">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-box-seam-fill"></i></div>
        <span class="brand-name">StockManager</span>
    </div>

    <?php $currentUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); ?>
    <?php
    // Determine current page segment
    $basePath = trim(parse_url(APP_URL, PHP_URL_PATH), '/');
    $seg = trim(str_replace($basePath, '', $currentUri), '/');
    $seg = explode('/', $seg)[0];
    ?>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-label">Principal</span>
            <a href="<?= APP_URL ?>/dashboard" class="nav-item <?= $seg === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i><span>Tableau de bord</span>
            </a>
        </div>

        <div class="nav-section">
            <span class="nav-label">Stock</span>
            <a href="<?= APP_URL ?>/produits" class="nav-item <?= $seg === 'produits' ? 'active' : '' ?>">
                <i class="bi bi-boxes"></i><span>Produits</span>
            </a>
            <a href="<?= APP_URL ?>/mouvements" class="nav-item <?= $seg === 'mouvements' ? 'active' : '' ?>">
                <i class="bi bi-arrow-left-right"></i><span>Mouvements</span>
            </a>
            <a href="<?= APP_URL ?>/alertes" class="nav-item <?= $seg === 'alertes' ? 'active' : '' ?>">
                <i class="bi bi-bell"></i><span>Alertes</span>
                <?php
                $nbAlerts = (new AlerteModel())->countNonResolues();
                if ($nbAlerts > 0): ?>
                    <span class="badge-nav"><?= $nbAlerts ?></span>
                <?php endif; ?>
            </a>
        </div>

        <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
        <div class="nav-section">
            <span class="nav-label">Administration</span>
            <a href="<?= APP_URL ?>/utilisateurs" class="nav-item <?= $seg === 'utilisateurs' ? 'active' : '' ?>">
                <i class="bi bi-people"></i><span>Utilisateurs</span>
            </a>
            <a href="<?= APP_URL ?>/categories" class="nav-item <?= $seg === 'categories' ? 'active' : '' ?>">
                <i class="bi bi-tags"></i><span>Catégories</span>
            </a>
            <a href="<?= APP_URL ?>/fournisseurs" class="nav-item <?= $seg === 'fournisseurs' ? 'active' : '' ?>">
                <i class="bi bi-truck"></i><span>Fournisseurs</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['user']['prenom'] ?? 'U', 0, 1)) ?></div>
            <div class="user-meta">
                <div class="user-name"><?= htmlspecialchars(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? '')) ?></div>
                <div class="user-role"><?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?></div>
            </div>
        </div>
        <a href="<?= APP_URL ?>/logout" class="btn-logout" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
    </div>
</aside>

<!-- Main content -->
<div class="main-wrapper">
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="page-title"><?= htmlspecialchars($title ?? '') ?></h1>
        <div class="topbar-right">
            <span class="topbar-date"><?= date('d/m/Y') ?></span>
        </div>
    </header>

    <main class="page-content">
        <?php
        $flash = $flash ?? Controller::getFlash();
        if ($flash): ?>
            <div class="alert-toast alert-toast--<?= $flash['type'] ?>" id="flashMsg">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($flash['message']) ?>
                <button onclick="this.parentElement.remove()" class="toast-close">×</button>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</div>

<script>
// Sidebar toggle mobile
document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});
// Auto-dismiss flash after 4s
setTimeout(() => { document.getElementById('flashMsg')?.remove(); }, 4000);
</script>
<script src="<?= APP_ASSET_URL ?>/js/app.js"></script>
</body>
</html>
