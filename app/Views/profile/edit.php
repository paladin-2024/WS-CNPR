<!-- ============================================================
     PROFILE EDIT - Modifier mon profil
     ============================================================ -->
<style>
.profile-edit-page {
    padding: 32px;
    max-width: 800px;
    margin: 0 auto;
}
.page-header {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 32px;
    border: 1px solid #bae6fd;
}
.page-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 8px 0;
    font-family: Poppins, sans-serif;
    display: flex;
    align-items: center;
    gap: 12px;
}
.page-header p {
    font-size: 15px;
    color: #64748b;
    margin: 0;
}
.form-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    border: 1px solid #f1f5f9;
}
.form-group {
    margin-bottom: 24px;
}
.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
    font-family: Poppins, sans-serif;
}
.form-group label span {
    color: #dc2626;
}
.form-input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    color: #1e293b;
    background: #f8fafc;
    transition: all 0.2s ease;
    font-family: inherit;
}
.form-input:focus {
    outline: none;
    border-color: #005FCC;
    background: white;
    box-shadow: 0 0 0 4px rgba(0,95,204,0.1);
}
.form-input:disabled {
    background: #f1f5f9;
    color: #94a3b8;
    cursor: not-allowed;
}
.form-input::placeholder {
    color: #94a3b8;
}
.form-hint {
    font-size: 12px;
    color: #64748b;
    margin-top: 6px;
}
.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
@media (max-width: 640px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
.btn-group {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 24px;
    border-top: 1px solid #f1f5f9;
    margin-top: 8px;
}
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
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
.error-message {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 24px;
    font-size: 14px;
}
.success-message {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 14px 18px;
    border-radius: 10px;
    margin-bottom: 24px;
    font-size: 14px;
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
<div class="<?= $flashType === 'success' ? 'success-message' : 'error-message' ?>">
    <?= htmlspecialchars($flashMessage) ?>
</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="error-message">
    <strong>Veuillez corriger les erreurs suivantes :</strong>
    <ul style="margin: 8px 0 0 20px;">
        <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="profile-edit-page">
    <!-- En-tête -->
    <div class="page-header">
        <h1>
            <i data-lucide="user-cog" style="width:28px;height:28px;color:#005FCC;"></i>
            Modifier mon profil
        </h1>
        <p>Mettez à jour vos informations personnelles</p>
    </div>

    <!-- Formulaire -->
    <div class="form-card">
        <form method="POST" action="<?= BASE_PATH ?>/profile/update">
            <!-- Nom et Prénom -->
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom <span>*</span></label>
                    <input type="text" id="nom" name="nom" class="form-input" 
                           value="<?= htmlspecialchars($user['nom'] ?? '') ?>" 
                           placeholder="Votre nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom <span>*</span></label>
                    <input type="text" id="prenom" name="prenom" class="form-input" 
                           value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" 
                           placeholder="Votre prénom" required>
                </div>
            </div>

            <!-- Email et Téléphone -->
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email <span>*</span></label>
                    <input type="email" id="email" name="email" class="form-input" 
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                           placeholder="votre@email.com" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="form-input" 
                           value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" 
                           placeholder="+261 32 XX XXX XX">
                    <span class="form-hint">Format: +261 3X XXX XX XX</span>
                </div>
            </div>

            <!-- Nom d'utilisateur (lecture seule) -->
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" class="form-input" 
                       value="<?= htmlspecialchars($user['username'] ?? '') ?>" 
                       disabled>
                <span class="form-hint">Le nom d'utilisateur ne peut pas être modifié</span>
            </div>

            <!-- Boutons -->
            <div class="btn-group">
                <a href="<?= BASE_PATH ?>/profile" class="btn btn-secondary">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" style="width:16px;height:16px;"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>