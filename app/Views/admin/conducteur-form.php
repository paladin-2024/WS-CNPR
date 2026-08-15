<?php
// Variables injectées par le contrôleur
$conducteur = $conducteur ?? null;
$isEdit = !empty($conducteur) && isset($conducteur['id']);
?>

<style>
    /* Toast notifications */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        padding: 14px 20px;
        border-radius: 8px;
        color: white;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 280px;
    }

    .toast-success {
        background: linear-gradient(135deg, #10B981, #059669);
    }

    .toast-error {
        background: linear-gradient(135deg, #EF4444, #DC2626);
    }

    .toast-warning {
        background: linear-gradient(135deg, #F59E0B, #D97706);
    }

    .toast svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    .toast.hiding {
        animation: slideOut 0.3s ease forwards;
    }

    /* Image preview */
    .file-preview-container {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .file-preview-item {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px dashed #E2E8F0;
        background: #F8FAFC;
    }

    .file-preview-item.has-file {
        border-style: solid;
    }

    .file-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .file-preview-item .placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #94A3B8;
    }

    .file-preview-item .placeholder svg {
        width: 32px;
        height: 32px;
        margin-bottom: 4px;
    }

    .file-preview-item .placeholder span {
        font-size: 11px;
    }

    .file-preview-item .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border: none;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .file-preview-item.has-file .remove-btn {
        display: flex;
    }

    .form-page {
        padding: 24px;
        max-width: 900px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #1A2744;
        margin: 0;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-secondary {
        background: white;
        color: #374151;
        border: 1px solid #E2E8F0;
    }

    .btn-secondary:hover {
        background: #F8FAFC;
    }

    .btn-primary {
        background: #3B82F6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563EB;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        padding: 24px;
    }

    .form-section {
        margin-bottom: 24px;
    }

    .form-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1A2744;
        margin: 0 0 16px 0;
        padding-bottom: 8px;
        border-bottom: 1px solid #E2E8F0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-group label .required {
        color: #DC2626;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background: white;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-group input::placeholder {
        color: #94A3B8;
    }

    .form-group .help-text {
        font-size: 12px;
        color: #64748B;
        margin-top: 4px;
    }

    .form-group.file-input {
        text-align: center;
        padding: 20px;
        border: 2px dashed #E2E8F0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .form-group.file-input:hover {
        border-color: #3B82F6;
        background: #F8FAFC;
    }

    .form-group.file-input input {
        display: none;
    }

    .form-group.file-input svg {
        width: 40px;
        height: 40px;
        color: #94A3B8;
        margin-bottom: 8px;
    }

    .form-group.file-input p {
        margin: 0;
        font-size: 14px;
        color: #64748B;
    }

    .form-actions {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .alert-error {
        background: #FEF2F2;
        color: #DC2626;
        border: 1px solid #FECACA;
    }

    .alert-success {
        background: #ECFDF5;
        color: #059669;
        border: 1px solid #A7F3D0;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: span 1;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<div class="form-page">
    <div class="page-header">
        <h1 class="page-title"><?= $isEdit ? 'Modifier' : 'Nouveau' ?> Conducteur</h1>
        <a href="<?= BASE_PATH ?>/admin/conducteurs" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour à la liste
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="<?= BASE_PATH ?>/admin/conducteurs/save" enctype="multipart/form-data" id="conducteurForm">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $conducteur['id'] ?>">
            <?php endif; ?>

            <!-- Informations personnelles -->
            <div class="form-section">
                <h3 class="form-section-title">Informations personnelles</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom et Postnom <span class="required">*</span></label>
                        <input type="text" name="nom" id="nom" required value="<?= htmlspecialchars($conducteur['nom'] ?? '') ?>" placeholder="Nom de famille">
                    </div>
                    <div class="form-group">
                        <label>Prénom <span class="required">*</span></label>
                        <input type="text" name="prenom" id="prenom" required value="<?= htmlspecialchars($conducteur['prenom'] ?? '') ?>" placeholder="Prénom">
                    </div>
                    <div class="form-group">
                        <label>Date de naissance <span class="required">*</span></label>
                        <input type="date" name="date_naissance" id="date_naissance" required value="<?= htmlspecialchars($conducteur['date_naissance'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Lieu de naissance</label>
                        <input type="text" name="lieu_naissance" id="lieu_naissance" value="<?= htmlspecialchars($conducteur['lieu_naissance'] ?? '') ?>" placeholder="Ville/Lieu de naissance">
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" id="telephone" value="<?= htmlspecialchars($conducteur['telephone'] ?? '') ?>" placeholder="+243...">
                    </div>
                    <div class="form-group">
                        <label>Adresse</label>
                        <input type="text" name="adresse" id="adresse" value="<?= htmlspecialchars($conducteur['adresse'] ?? '') ?>" placeholder="Adresse complète">
                    </div>
                </div>
            </div>

            <!-- Permis de conduire -->
            <div class="form-section">
                <h3 class="form-section-title">Permis de conduire</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Numéro de permis</label>
                        <input type="text" name="numero_permis" id="numero_permis" value="<?= htmlspecialchars($conducteur['numero_permis'] ?? '') ?>" placeholder="N° permis de conduire">
                    </div>
                    <div class="form-group">
                        <label>Catégorie <span class="required">*</span></label>
                        <select name="categorie_permis" id="categorie_permis" required>
                            <option value="">Sélectionner</option>
                            <option value="A" <?= ($conducteur['categorie_permis'] ?? '') === 'A' ? 'selected' : '' ?>>A - Moto</option>
                            <option value="B" <?= ($conducteur['categorie_permis'] ?? 'B') === 'B' ? 'selected' : '' ?>>B - Voiture</option>
                            <option value="C" <?= ($conducteur['categorie_permis'] ?? '') === 'C' ? 'selected' : '' ?>>C - Camion</option>
                            <option value="D" <?= ($conducteur['categorie_permis'] ?? '') === 'D' ? 'selected' : '' ?>>D - Bus</option>
                            <option value="E" <?= ($conducteur['categorie_permis'] ?? '') === 'E' ? 'selected' : '' ?>>E - Remorque</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date d'expiration du permis</label>
                        <input type="date" name="date_expiration_permis" id="date_expiration_permis" value="<?= htmlspecialchars($conducteur['date_expiration_permis'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Association et Syndicat -->
            <div class="form-section">
                <h3 class="form-section-title">Association et Syndicat</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Association</label>
                        <input type="text" name="association" id="association" value="<?= htmlspecialchars($conducteur['association'] ?? '') ?>" placeholder="Nom de l'association">
                    </div>
                    <div class="form-group">
                        <label>Syndicat</label>
                        <input type="text" name="syndicat" id="syndicat" value="<?= htmlspecialchars($conducteur['syndicat'] ?? '') ?>" placeholder="Nom du syndicat">
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="form-section">
                <h3 class="form-section-title">Documents</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Photo du conducteur</label>
                        <div class="file-preview-container">
                            <div class="file-preview-item" id="preview_photo_url" onclick="document.getElementById('photo_url').click()">
                                <input type="file" name="photo_url" id="photo_url" accept="image/*" style="display:none" onchange="previewFile(this, 'preview_photo_url')">
                                <div class="placeholder">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Cliquez pour ajouter</span>
                                </div>
                                <img src="" alt="Preview" style="display:none">
                                <button type="button" class="remove-btn" onclick="event.stopPropagation(); removeFile('preview_photo_url')">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <span class="help-text">JPG, PNG - Max 2MB</span>
                    </div>
                    <div class="form-group">
                        <label>Pièce d'identité</label>
                        <div class="file-preview-container">
                            <div class="file-preview-item" id="preview_photo_piece_identite" onclick="document.getElementById('photo_piece_identite').click()">
                                <input type="file" name="photo_piece_identite" id="photo_piece_identite" accept="image/*" style="display:none" onchange="previewFile(this, 'preview_photo_piece_identite')">
                                <div class="placeholder">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Cliquez pour ajouter</span>
                                </div>
                                <img src="" alt="Preview" style="display:none">
                                <button type="button" class="remove-btn" onclick="event.stopPropagation(); removeFile('preview_photo_piece_identite')">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <span class="help-text">JPG, PNG - Max 5MB</span>
                    </div>
                </div>
            </div>

            <!-- Statut (uniquement pour modification) -->
            <?php if ($isEdit): ?>
            <div class="form-section">
                <h3 class="form-section-title">Statut</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Statut du conducteur</label>
                        <select name="statut" id="statut">
                            <option value="actif" <?= ($conducteur['statut'] ?? 'actif') === 'actif' ? 'selected' : '' ?>>Actif</option>
                            <option value="suspendu" <?= ($conducteur['statut'] ?? '') === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                            <option value="expire" <?= ($conducteur['statut'] ?? '') === 'expire' ? 'selected' : '' ?>>Expiré</option>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="<?= BASE_PATH ?>/admin/conducteurs" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?= $isEdit ? 'Mettre à jour' : 'Enregistrer' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Toast notifications
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icons = {
        success: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
        error: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
        warning: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
    };
    
    toast.innerHTML = icons[type] + message;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// File preview
function previewFile(input, previewId) {
    const file = input.files[0];
    if (!file) return;
    
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('img');
    const placeholder = preview.querySelector('.placeholder');
    
    const reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result;
        img.style.display = 'block';
        placeholder.style.display = 'none';
        preview.classList.add('has-file');
    };
    reader.readAsDataURL(file);
}

function removeFile(previewId) {
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('img');
    const placeholder = preview.querySelector('.placeholder');
    const input = preview.querySelector('input[type="file"]');
    
    input.value = '';
    img.src = '';
    img.style.display = 'none';
    placeholder.style.display = 'flex';
    preview.classList.remove('has-file');
}

// Initialize existing images on page load
function initExistingImages() {
    <?php if (!empty($conducteur) && !empty($conducteur['photo_url'])): ?>
    const photoUrl = '<?= BASE_PATH ?>/public/<?= htmlspecialchars($conducteur['photo_url']) ?>';
    const photoPreview = document.getElementById('preview_photo_url');
    if (photoPreview) {
        const img = photoPreview.querySelector('img');
        const placeholder = photoPreview.querySelector('.placeholder');
        img.src = photoUrl;
        img.style.display = 'block';
        placeholder.style.display = 'none';
        photoPreview.classList.add('has-file');
    }
    <?php endif; ?>
    
    <?php if (!empty($conducteur) && !empty($conducteur['photo_piece_identite'])): ?>
    const photoPiece = '<?= BASE_PATH ?>/public/<?= htmlspecialchars($conducteur['photo_piece_identite']) ?>';
    const piecePreview = document.getElementById('preview_photo_piece_identite');
    if (piecePreview) {
        const img = piecePreview.querySelector('img');
        const placeholder = piecePreview.querySelector('.placeholder');
        img.src = photoPiece;
        img.style.display = 'block';
        placeholder.style.display = 'none';
        piecePreview.classList.add('has-file');
    }
    <?php endif; ?>
}

// Check for success message in URL
window.addEventListener('DOMContentLoaded', function() {
    initExistingImages();
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
        showToast(urlParams.get('success'), 'success');
    }
    if (urlParams.get('error')) {
        showToast(urlParams.get('error'), 'error');
    }
});
</script>
