<!-- ============================================================
     PROFILE PAGE - Mon Profil
     ============================================================ -->
<style>
.profile-page {
    padding: 32px;
    max-width: 900px;
    margin: 0 auto;
}
.profile-header {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 20px;
    padding: 40px;
    display: flex;
    align-items: center;
    gap: 32px;
    margin-bottom: 32px;
    border: 1px solid #bae6fd;
    position: relative;
    overflow: hidden;
}
.profile-header::before {
    content: '';
    position: absolute;
    right: -50px;
    top: -50px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(0,95,204,0.1) 0%, transparent 70%);
    border-radius: 50%;
}
.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #005FCC, #004499);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 700;
    color: white;
    font-family: Poppins, sans-serif;
    flex-shrink: 0;
    position: relative;
    z-index: 2;
}
.profile-info {
    position: relative;
    z-index: 2;
}
.profile-info h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 8px 0;
    font-family: Poppins, sans-serif;
}
.profile-info .role {
    font-size: 15px;
    color: #64748b;
    margin: 0 0 12px 0;
}
.profile-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}
.profile-badge.active {
    background: #d1fae5;
    color: #065f46;
}
.profile-badge.inactive, .profile-badge.suspendu {
    background: #fee2e2;
    color: #991b1b;
}
.section-card {
    background: white;
    border-radius: 16px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    border: 1px solid #f1f5f9;
}
.section-card h2 {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a2e;
    margin: 0 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #f1f5f9;
    font-family: Poppins, sans-serif;
    display: flex;
    align-items: center;
    gap: 10px;
}
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}
.info-item {
    background: #f8fafc;
    border-radius: 10px;
    padding: 16px;
}
.info-item label {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-bottom: 6px;
    font-weight: 500;
}
.info-item p {
    margin: 0;
    font-size: 15px;
    color: #1a1a2e;
    font-weight: 500;
    font-family: Poppins, sans-serif;
}
.action-buttons {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: Poppins, sans-serif;
    border: none;
}
.btn-primary {
    background: #005FCC;
    color: white;
}
.btn-primary:hover {
    background: #004499;
}
.btn-secondary {
    background: #f1f5f9;
    color: #475569;
}
.btn-secondary:hover {
    background: #e2e8f0;
}
</style>

<?php
// Afficher le message flash
$flashData = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$flashMessage = is_array($flashData) ? ($flashData['message'] ?? '') : ($flashData ?? '');
$flashType = is_array($flashData) ? ($flashData['type'] ?? 'info') : 'info';
?>

<?php if (!empty($flashMessage)): ?>
<div style="padding: 14px 20px; margin-bottom: 24px; border-radius: 10px; <?= $flashType === 'success' ? 'background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;' : 'background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;' ?>">
    <?= htmlspecialchars($flashMessage) ?>
</div>
<?php endif; ?>

<div class="profile-page">
    <!-- En-tête du profil -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?= mb_strtoupper(mb_substr($user['prenom'] ?? '', 0, 1) . mb_substr($user['nom'] ?? '', 0, 1)) ?>
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></h1>
            <p class="role"><?= htmlspecialchars($roleLabel) ?></p>
            <span class="profile-badge <?= $user['statut'] ?? 'actif' ?>">
                <i data-lucide="<?= ($user['statut'] ?? 'actif') === 'actif' ? 'check-circle' : 'x-circle' ?>" style="width:14px;height:14px;"></i>
                <?= $statutLabel ?>
            </span>
        </div>
    </div>

    <!-- Section informations personnelles -->
    <div class="section-card">
        <h2>
            <i data-lucide="user" style="width:20px;height:20px;color:#005FCC;"></i>
            Informations personnelles
        </h2>
        <div class="info-grid">
            <div class="info-item">
                <label>Nom</label>
                <p><?= htmlspecialchars($user['nom'] ?? 'N/A') ?></p>
            </div>
            <div class="info-item">
                <label>Prénom</label>
                <p><?= htmlspecialchars($user['prenom'] ?? 'N/A') ?></p>
            </div>
            <div class="info-item">
                <label>Email</label>
                <p><?= htmlspecialchars($user['email'] ?? 'N/A') ?></p>
            </div>
            <div class="info-item">
                <label>Téléphone</label>
                <p><?= htmlspecialchars($user['telephone'] ?? 'N/A') ?></p>
            </div>
        </div>
    </div>

    <!-- Section informations du compte -->
    <div class="section-card">
        <h2>
            <i data-lucide="shield" style="width:20px;height:20px;color:#005FCC;"></i>
            Informations du compte
        </h2>
        <div class="info-grid">
            <div class="info-item">
                <label>Date de création</label>
                <p><?= $dateCreation ?></p>
            </div>
            <div class="info-item">
                <label>Dernière connexion</label>
                <p><?= $derniereConnexion ?></p>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="action-buttons">
        <a href="<?= BASE_PATH ?>/admin" class="btn btn-secondary">
            <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
            Retour au tableau de bord
        </a>
        <a href="<?= BASE_PATH ?>/profile/edit" class="btn btn-primary">
            <i data-lucide="edit" style="width:16px;height:16px;"></i>
            Modifier le profil
        </a>
    </div>
</div>