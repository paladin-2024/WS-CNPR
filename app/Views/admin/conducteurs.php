<?php
// Variables injectées par le contrôleur
$conducteurs = $conducteurs ?? [];

function getStatutMeta($statut) {
    $metas = [
        'actif' => ['label' => 'Actif', 'color' => '#059669', 'bg' => '#ECFDF5'],
        'suspendu' => ['label' => 'Suspendu', 'color' => '#DC2626', 'bg' => '#FEF2F2'],
        'expire' => ['label' => 'Expiré', 'color' => '#D97706', 'bg' => '#FFFBEB'],
    ];
    return $metas[$statut] ?? ['label' => $statut, 'color' => '#64748B', 'bg' => '#F1F5F9'];
}

function getCategoryColor($categorie) {
    $colors = ['A' => '#7C3AED', 'B' => '#3B82F6', 'C' => '#059669', 'D' => '#D97706', 'E' => '#EF4444'];
    return $colors[$categorie] ?? '#64748B';
}

function formatDate($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}
?>

<style>
    /* Toast notifications - Professional Animation */
    .toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
    }

    .toast {
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        font-size: 14px;
        font-weight: 500;
        font-family: 'Segoe UI', system-ui, sans-serif;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2), 0 0 0 1px rgba(255,255,255,0.1);
        animation: toastSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 420px;
        backdrop-filter: blur(10px);
        pointer-events: auto;
    }

    .toast-success { 
        background: linear-gradient(135deg, #10B981 0%, #059669 50%, #047857 100%);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    
    .toast-error { 
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 50%, #B91C1C 100%);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .toast svg { 
        width: 22px; 
        height: 22px; 
        flex-shrink: 0;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }

    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: rgba(255,255,255,0.4);
        border-radius: 0 0 12px 12px;
        animation: progressShrink 4s linear forwards;
    }

    @keyframes toastSlideIn {
        0% { 
            transform: translateX(120%); 
            opacity: 0;
            scale: 0.9;
        }
        100% { 
            transform: translateX(0); 
            opacity: 1;
            scale: 1;
        }
    }

    @keyframes toastSlideOut {
        0% { 
            transform: translateX(0); 
            opacity: 1;
            scale: 1;
        }
        100% { 
            transform: translateX(120%); 
            opacity: 0;
            scale: 0.9;
        }
    }

    @keyframes progressShrink {
        from { width: 100%; }
        to { width: 0%; }
    }

    .conducteurs-page {
        padding: 24px;
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
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
    }

    .btn-primary {
        background: #3B82F6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563EB;
    }

    .btn svg {
        width: 18px;
        height: 18px;
    }

    /* Filters */
    .filters-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 200px;
        max-width: 400px;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
    }

    .search-box input:focus {
        border-color: #3B82F6;
    }

    .search-box svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #94A3B8;
    }

    .filter-select {
        padding: 10px 16px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background: white;
        min-width: 150px;
    }

    /* Table */
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .data-table th {
        text-align: left;
        padding: 14px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
    }

    .data-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: #334155;
        border-bottom: 1px solid #F1F5F9;
    }

    .data-table tr:hover td {
        background: #F8FAFC;
    }

    .conducteur-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .conducteur-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3B82F6, #8B5CF6);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .conducteur-name {
        font-weight: 500;
        color: #1A2744;
    }

    .conducteur-contact {
        font-size: 12px;
        color: #64748B;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-permis {
        font-weight: 600;
    }

    .action-btns {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .action-btn svg {
        width: 16px;
        height: 16px;
    }

    .action-btn-edit {
        background: #EFF6FF;
        color: #3B82F6;
    }

    .action-btn-edit:hover {
        background: #DBEAFE;
    }

    .action-btn-delete {
        background: #FEF2F2;
        color: #DC2626;
    }

    .action-btn-delete:hover {
        background: #FEE2E2;
    }

    .action-btn-view {
        background: #F0FDF4;
        color: #16A34A;
    }

    .action-btn-view:hover {
        background: #DCFCE7;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        color: #CBD5E1;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        margin: 0 0 8px 0;
        color: #334155;
    }

    .empty-state p {
        margin: 0;
    }

    /* Modal */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1000;
        background: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.2s ease;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
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

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background: white;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #3B82F6;
    }

    .form-message {
        margin-top: 16px;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        display: none;
    }

    .form-message.success {
        display: block;
        background: #ECFDF5;
        color: #059669;
    }

    .form-message.error {
        display: block;
        background: #FEF2F2;
        color: #DC2626;
    }

    .form-actions {
        margin-top: 24px;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-secondary {
        padding: 10px 20px;
        border: 1px solid #E2E8F0;
        background: white;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: #F8FAFC;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .filters-bar {
            flex-direction: column;
        }

        .search-box {
            max-width: none;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: span 1;
        }
    }
</style>

<div class="conducteurs-page">
    <div class="page-header">
        <h1 class="page-title">Gestion des Conducteurs</h1>
        <a href="<?= BASE_PATH ?>/admin/conducteurs/ajouter" class="btn btn-primary">
            <i data-lucide="plus"></i>
            Nouveau conducteur
        </a>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <div class="search-box">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Rechercher par nom, téléphone, numéro de permis..." id="searchInput">
        </div>
        <select class="filter-select" id="statusFilter">
            <option value="">Tous les statuts</option>
            <option value="actif">Actif</option>
            <option value="suspendu">Suspendu</option>
            <option value="expire">Expiré</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        <?php if (empty($conducteurs)): ?>
            <div class="empty-state">
                <i data-lucide="users"></i>
                <h3>Aucun conducteur trouvé</h3>
                <p>Commencez par ajouter un nouveau conducteur</p>
            </div>
        <?php else: ?>
            <table class="data-table" id="conducteursTable">
                <thead>
                    <tr>
                        <th>Conducteur</th>
                        <th>Téléphone</th>
                        <th>Numéro Permis</th>
                        <th>Catégorie</th>
                        <th>Association</th>
                        <th>Date Enregistrement</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conducteurs as $conducteur): 
                        $meta = getStatutMeta($conducteur['statut'] ?? 'actif');
                        $initials = strtoupper(substr($conducteur['prenom'] ?? '', 0, 1) . substr($conducteur['nom'] ?? '', 0, 1));
                    ?>
                        <tr>
                            <td>
                                <div class="conducteur-info">
                                    <div class="conducteur-avatar"><?= htmlspecialchars($initials) ?></div>
                                    <div>
                                        <div class="conducteur-name"><?= htmlspecialchars(($conducteur['prenom'] ?? '') . ' ' . ($conducteur['nom'] ?? '')) ?></div>
                                        <div class="conducteur-contact"><?= htmlspecialchars($conducteur['adresse'] ?? '-') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($conducteur['telephone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($conducteur['numero_permis'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-permis" style="background: <?= getCategoryColor($conducteur['categorie_permis'] ?? 'B') ?>20; color: <?= getCategoryColor($conducteur['categorie_permis'] ?? 'B') ?>;">
                                    <?= htmlspecialchars($conducteur['categorie_permis'] ?? 'B') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($conducteur['association'] ?? '-') ?></td>
                            <td><?= formatDate($conducteur['date_enregistrement'] ?? null) ?></td>
                            <td>
                                <span class="badge" style="background: <?= $meta['bg'] ?>; color: <?= $meta['color'] ?>;">
                                    <?= htmlspecialchars($meta['label']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn action-btn-view" title="Voir les détails" onclick="showDriver(<?= $conducteur['id'] ?>)">
                                        <i data-lucide="eye"></i>
                                        </button>
                                        <a href="<?= BASE_PATH ?>/admin/conducteurs/modifier/<?= $conducteur['id'] ?>" class="action-btn action-btn-edit" title="Modifier" style="text-decoration:none;">
                                        <i data-lucide="pencil"></i>
                                    </a>
                                    <button class="action-btn action-btn-delete" title="Supprimer" onclick="deleteConducteur(<?= $conducteur['id'] ?>)">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Driver Details Modal -->
<div id="driverDetailModal" class="modal-backdrop" style="display:none;">
    <div class="modal-content" style="max-width:600px;">
        <div style="padding:20px 24px;border-bottom:1px solid #E2E8F0;display:flex;align-items:center;justify-content:space-between;">
            <h2 style="margin:0;font-size:18px;font-weight:600;color:#1A2744;">Détails du conducteur</h2>
            <button onclick="closeDriverModal()" style="background:none;border:none;cursor:pointer;padding:4px;color:#64748B;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div id="driverDetailBody" style="padding:24px;max-height:70vh;overflow-y:auto;">
        </div>
    </div>
</div>


<div id="conducteurModal" class="modal-backdrop">
    <div class="modal-content">
        <div style="padding:20px 24px;border-bottom:1px solid #E2E8F0;display:flex;align-items:center;justify-content:space-between;">
            <h2 id="modalTitle" style="margin:0;font-size:18px;font-weight:600;color:#1A2744;">Nouveau conducteur</h2>
            <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;padding:4px;color:#64748B;">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <form id="conducteurForm" style="padding:24px;">
            <input type="hidden" name="id" id="conducteurId">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" id="nom" required placeholder="Nom de famille">
                </div>
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" id="prenom" required placeholder="Prénom">
                </div>
                <div class="form-group">
                    <label>Date de naissance *</label>
                    <input type="date" name="date_naissance" id="date_naissance" required>
                </div>
                <div class="form-group">
                    <label>Lieu de naissance</label>
                    <input type="text" name="lieu_naissance" id="lieu_naissance" placeholder="Ville/Lieu">
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" id="telephone" placeholder="+243...">
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse" id="adresse" placeholder="Adresse complète">
                </div>
                <div class="form-group">
                    <label>Numéro de permis *</label>
                    <input type="text" name="numero_permis" id="numero_permis" required placeholder="N° permis de conduire">
                </div>
                <div class="form-group">
                    <label>Catégorie permis *</label>
                    <select name="categorie_permis" id="categorie_permis" required>
                        <option value="A">Catégorie A (Moto)</option>
                        <option value="B" selected>Catégorie B (Voiture)</option>
                        <option value="C">Catégorie C (Camion)</option>
                        <option value="D">Catégorie D (Bus)</option>
                        <option value="E">Catégorie E (Remorque)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date expiration permis</label>
                    <input type="date" name="date_expiration_permis" id="date_expiration_permis">
                </div>
                <div class="form-group">
                    <label>Association</label>
                    <input type="text" name="association" id="association" placeholder="Nom de l'association">
                </div>
                <div class="form-group full-width">
                    <label>Syndicat</label>
                    <input type="text" name="syndicat" id="syndicat" placeholder="Nom du syndicat">
                </div>
                <div class="form-group full-width" id="statutGroup" style="display:none;">
                    <label>Statut</label>
                    <select name="statut" id="statut">
                        <option value="actif">Actif</option>
                        <option value="suspendu">Suspendu</option>
                        <option value="expire">Expiré</option>
                    </select>
                </div>
            </div>
            <div id="formMessage" class="form-message"></div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_PATH = '<?= BASE_PATH ?>';

function getStatutMeta(statut) {
    const metas = {
        'actif': {label: 'Actif', color: '#059669', bg: '#ECFDF5'},
        'suspendu': {label: 'Suspendu', color: '#DC2626', bg: '#FEF2F2'},
        'expire': {label: 'Expiré', color: '#D97706', bg: '#FFFBEB'}
    };
    return metas[statut] || {label: statut, color: '#64748B', bg: '#F1F5F9'};
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Nouveau conducteur';
    document.getElementById('conducteurForm').reset();
    document.getElementById('conducteurId').value = '';
    document.getElementById('formMessage').className = 'form-message';
    document.getElementById('formMessage').style.display = 'none';
    document.getElementById('statutGroup').style.display = 'none';
    document.getElementById('conducteurModal').classList.add('active');
}

function closeModal() {
    document.getElementById('conducteurModal').classList.remove('active');
}

// Fermer modal en cliquant en dehors
document.getElementById('conducteurModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Soumettre le formulaire
document.getElementById('conducteurForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    const messageEl = document.getElementById('formMessage');
    const submitBtn = document.getElementById('submitBtn');
    const id = data.id;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enregistrement...';
    messageEl.style.display = 'none';
    
    try {
        const url = id ? `${BASE_PATH}/admin/api/conducteurs?id=${id}` : `${BASE_PATH}/admin/api/conducteurs`;
        const method = id ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            messageEl.className = 'form-message success';
            messageEl.textContent = id ? 'Conducteur modifié avec succès!' : 'Conducteur créé avec succès!';
            messageEl.style.display = 'block';
            
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            messageEl.className = 'form-message error';
            messageEl.textContent = result.error || 'Erreur lors de l\'enregistrement';
            messageEl.style.display = 'block';
        }
    } catch (error) {
        messageEl.className = 'form-message error';
        messageEl.textContent = 'Erreur de connexion';
        messageEl.style.display = 'block';
    }
    
    submitBtn.disabled = false;
    submitBtn.textContent = 'Enregistrer';
});

// Modifier un conducteur
async function editConducteur(id) {
    try {
        const response = await fetch(`${BASE_PATH}/admin/api/conducteurs?id=${id}`);
        const conducteur = await response.json();
        
        if (conducteur && !conducteur.error) {
            document.getElementById('modalTitle').textContent = 'Modifier le conducteur';
            document.getElementById('conducteurId').value = conducteur.id;
            document.getElementById('nom').value = conducteur.nom || '';
            document.getElementById('prenom').value = conducteur.prenom || '';
            document.getElementById('date_naissance').value = conducteur.date_naissance || '';
            document.getElementById('lieu_naissance').value = conducteur.lieu_naissance || '';
            document.getElementById('telephone').value = conducteur.telephone || '';
            document.getElementById('adresse').value = conducteur.adresse || '';
            document.getElementById('numero_permis').value = conducteur.numero_permis || '';
            document.getElementById('categorie_permis').value = conducteur.categorie_permis || 'B';
            document.getElementById('date_expiration_permis').value = conducteur.date_expiration_permis || '';
            document.getElementById('association').value = conducteur.association || '';
            document.getElementById('syndicat').value = conducteur.syndicat || '';
            document.getElementById('statut').value = conducteur.statut || 'actif';
            document.getElementById('statutGroup').style.display = 'block';
            
            document.getElementById('formMessage').style.display = 'none';
            document.getElementById('conducteurModal').classList.add('active');
        }
    } catch (error) {
        alert('Erreur lors du chargement des données');
    }
}

// Supprimer un conducteur avec SweetAlert
async function deleteConducteur(id) {
    const result = await Swal.fire({
        title: 'Êtes-vous sûr?',
        text: "Cette action supprimera définitivement ce conducteur. Cette action est irréversible.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    });

    if (result.isConfirmed) {
        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            
            const response = await fetch(`${BASE_PATH}/admin/api/conducteurs?id=${id}`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                await Swal.fire({
                    title: 'Supprimé!',
                    text: 'Le conducteur a été supprimé avec succès.',
                    icon: 'success',
                    confirmButtonColor: '#059669'
                });
                window.location.reload();
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: data.error || 'Erreur lors de la suppression',
                    icon: 'error',
                    confirmButtonColor: '#DC2626'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Erreur',
                text: 'Erreur de connexion',
                icon: 'error',
                confirmButtonColor: '#DC2626'
            });
        }
    }
}

// Recherche
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#conducteursTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Filtre par statut
document.getElementById('statusFilter').addEventListener('change', function(e) {
    const status = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#conducteursTable tbody tr');
    
    rows.forEach(row => {
        if (!status) {
            row.style.display = '';
        } else {
            const rowStatus = row.textContent.toLowerCase();
            row.style.display = rowStatus.includes(status) ? '' : 'none';
        }
    });
});
</script>

<script>
// Toast notifications - Professional Animation
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icons = {
        success: '<i data-lucide="circle-check-big"></i>',
        error: '<i data-lucide="circle-alert"></i>'
    };
    
    toast.innerHTML = icons[type] + '<span>' + message + '</span>';
    
    // Add progress bar
    const progress = document.createElement('div');
    progress.className = 'toast-progress';
    toast.appendChild(progress);
    
    container.appendChild(toast);
    if (window.lucide) lucide.createIcons();
    
    setTimeout(() => {
        toast.style.animation = 'toastSlideOut 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards';
        setTimeout(() => toast.remove(), 350);
    }, 4000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

// Check for success message in URL
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
        showToast(urlParams.get('success'), 'success');
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    if (urlParams.get('error')) {
        showToast(urlParams.get('error'), 'error');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// Driver Details Modal
function showDriver(id) {
    const modal = document.getElementById('driverDetailModal');
    const modalBody = document.getElementById('driverDetailBody');
    
    modal.style.display = 'flex';
    modalBody.innerHTML = '<div style="text-align:center;padding:40px;"><div class="loader"></div><p>Chargement...</p></div>';
    
    fetch(BASE_PATH + '/admin/api/conducteurs?id=' + id)
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response statusText:', response.statusText);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            // Handle both array and single object response
            let driver = null;
            if (Array.isArray(data)) {
                if (data.length > 0) {
                    driver = data[0];
                }
            } else if (data && !data.error) {
                driver = data;
            }
            
            if (driver) {
                const statutMeta = getStatutMeta(driver.statut);
                const photoUrl = driver.photo_url ? BASE_PATH + '/public/' + driver.photo_url : null;
                
                modalBody.innerHTML = `
                    <div style="display:flex;flex-direction:column;gap:20px;">
                        <div style="display:flex;align-items:center;gap:20px;border-bottom:1px solid #eee;padding-bottom:20px;">
                            ${photoUrl 
                                ? `<img src="${photoUrl}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;background:#f0f0f0;" alt="Photo">`
                                : `<div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#007FFF,#005FCC);display:flex;align-items:center;justify-content:center;color:white;font-size:28px;font-weight:bold;">
                                    ${driver.prenom ? driver.prenom.charAt(0).toUpperCase() : ''}${driver.nom ? driver.nom.charAt(0).toUpperCase() : ''}
                                </div>`
                            }
                            <div>
                                <h2 style="margin:0 0 8px 0;font-size:22px;color:#1a1a2e;">${driver.nom || ''} ${driver.prenom || ''}</h2>
                                <span style="display:inline-block;padding:4px 12px;background:${statutMeta.bg};color:${statutMeta.color};border-radius:20px;font-size:13px;font-weight:600;">
                                    ${statutMeta.label}
                                </span>
                            </div>
                        </div>
                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div>
                                <label style="font-size:12px;color:#64748b;font-weight:600;">DATE DE NAISSANCE</label>
                                <p style="margin:4px 0 0 0;color:#1a1a2e;">${driver.date_naissance ? new Date(driver.date_naissance).toLocaleDateString('fr-FR') : '-'}</p>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#64748b;font-weight:600;">LIEU DE NAISSANCE</label>
                                <p style="margin:4px 0 0 0;color:#1a1a2e;">${driver.lieu_naissance || '-'}</p>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#64748b;font-weight:600;">TÉLÉPHONE</label>
                                <p style="margin:4px 0 0 0;color:#1a1a2e;">${driver.telephone || '-'}</p>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#64748b;font-weight:600;">ADRESSE</label>
                                <p style="margin:4px 0 0 0;color:#1a1a2e;">${driver.adresse || '-'}</p>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#64748b;font-weight:600;">NUMÉRO PERMIS</label>
                                <p style="margin:4px 0 0 0;color:#1a1a2e;font-weight:600;">${driver.numero_permis || '-'}</p>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#64748b;font-weight:600;">CATÉGORIE PERMIS</label>
                                <p style="margin:4px 0 0 0;"><span style="display:inline-block;padding:2px 10px;background:#007FFF20;color:#007FFF;border-radius:4px;font-weight:600;">${driver.categorie_permis || '-'}</span></p>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#64748b;font-weight:600;">EXPIRATION PERMIS</label>
                                <p style="margin:4px 0 0 0;color:#1a1a2e;">${driver.date_expiration_permis ? new Date(driver.date_expiration_permis).toLocaleDateString('fr-FR') : '-'}</p>
                            </div>
                            <div>
                                <label style="font-size:12px;color:#64748b;font-weight:600;">ASSOCIATION</label>
                                <p style="margin:4px 0 0 0;color:#1a1a2e;">${driver.association || '-'}</p>
                            </div>
                        </div>
                        
                        <div style="margin-top:10px;">
                            <label style="font-size:12px;color:#64748b;font-weight:600;">DATE D'ENREGISTREMENT</label>
                            <p style="margin:4px 0 0 0;color:#1a1a2e;">${driver.date_enregistrement ? new Date(driver.date_enregistrement).toLocaleDateString('fr-FR') : '-'}</p>
                        </div>
                    </div>
                `;
            } else if (data && data.error) {
                modalBody.innerHTML = '<p style="text-align:center;color:#EF4444;">' + data.error + '</p>';
            } else {
                modalBody.innerHTML = '<p style="text-align:center;color:#EF4444;">Conducteur non trouvé</p>';
            }
        })
        .catch(error => {
            console.log('Fetch error:', error);
            modalBody.innerHTML = '<p style="text-align:center;color:#EF4444;">Erreur lors du chargement: ' + error.message + '</p>';
        });
}

function closeDriverModal() {
    document.getElementById('driverDetailModal').style.display = 'none';
}
</script>
