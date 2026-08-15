<?php
$utilisateurs = $utilisateurs ?? [];

function getRoleMeta($role) {
    $metas = [
        'admin' => ['label' => 'Administrateur', 'color' => '#DC2626', 'bg' => '#FEF2F2'],
        'minister_admin' => ['label' => 'Admin Ministère', 'color' => '#7C3AED', 'bg' => '#F5F3FF'],
        'agent' => ['label' => 'Agent', 'color' => '#0369A1', 'bg' => '#F0F9FF'],
        'inspecteur' => ['label' => 'Inspecteur', 'color' => '#D97706', 'bg' => '#FFFBEB'],
        'gestionnaire_parking' => ['label' => 'Gestionnaire Parking', 'color' => '#059669', 'bg' => '#ECFDF5'],
        'transporteur' => ['label' => 'Transporteur', 'color' => '#0891B2', 'bg' => '#ECFEFF'],
        'conducteur' => ['label' => 'Conducteur', 'color' => '#3B82F6', 'bg' => '#EFF6FF'],
        'citoyen' => ['label' => 'Citoyen', 'color' => '#64748B', 'bg' => '#F1F5F9'],
        'imprimeur' => ['label' => 'Imprimeur', 'color' => '#EA580C', 'bg' => '#FFF7ED'],
        'receptionnaire' => ['label' => 'Réceptionnaire', 'color' => '#0D9488', 'bg' => '#F0FDFA'],
        'operateur_saisie' => ['label' => 'Opérateur Saisie', 'color' => '#2563EB', 'bg' => '#DBEAFE'],
        'validateur' => ['label' => 'Validateur', 'color' => '#7C3AED', 'bg' => '#EDE9FE'],
        'receveur' => ['label' => 'Receveur', 'color' => '#059669', 'bg' => '#D1FAE5'],
        'instructeur' => ['label' => 'Instructeur', 'color' => '#D97706', 'bg' => '#FEF3C7'],
    ];
    return $metas[$role] ?? ['label' => $role, 'color' => '#64748B', 'bg' => '#F1F5F9'];
}

function getStatutMeta($statut) {
    $metas = [
        'actif' => ['label' => 'Actif', 'color' => '#059669', 'bg' => '#ECFDF5'],
        'inactif' => ['label' => 'Inactif', 'color' => '#64748B', 'bg' => '#F1F5F9'],
        'suspendu' => ['label' => 'Suspendu', 'color' => '#DC2626', 'bg' => '#FEF2F2'],
    ];
    return $metas[$statut] ?? ['label' => $statut, 'color' => '#64748B', 'bg' => '#F1F5F9'];
}

function formatDate($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($date) {
    if (!$date) return '-';
    return date('d/m/Y H:i', strtotime($date));
}
?>

<style>
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
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        animation: toastSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 420px;
        pointer-events: auto;
        position: relative;
    }

    .toast-success { background: linear-gradient(135deg, #10B981, #059669, #047857); }
    .toast-error { background: linear-gradient(135deg, #EF4444, #DC2626, #B91C1C); }
    .toast svg { width: 22px; height: 22px; flex-shrink: 0; }
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
        0% { transform: translateX(120%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    @keyframes toastSlideOut {
        0% { transform: translateX(0); opacity: 1; }
        100% { transform: translateX(120%); opacity: 0; }
    }
    @keyframes progressShrink {
        from { width: 100%; }
        to { width: 0%; }
    }

    .utilisateurs-page { padding: 24px; }

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

    .btn-primary { background: #059669; color: white; }
    .btn-primary:hover { background: #047857; }
    .btn svg { width: 18px; height: 18px; }

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

    .search-box input:focus { border-color: #059669; }

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

    .data-table tr:hover td { background: #F8FAFC; }

    .user-info { display: flex; align-items: center; gap: 12px; }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #059669, #10B981);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .user-name { font-weight: 500; color: #1A2744; }
    .user-email { font-size: 12px; color: #64748B; }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .action-btns { display: flex; gap: 8px; }

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

    .action-btn svg { width: 16px; height: 16px; }
    .action-btn-edit { background: #EFF6FF; color: #3B82F6; }
    .action-btn-edit:hover { background: #DBEAFE; }
    .action-btn-delete { background: #FEF2F2; color: #DC2626; }
    .action-btn-delete:hover { background: #FEE2E2; }

    .empty-state { text-align: center; padding: 60px 20px; color: #64748B; }
    .empty-state svg { width: 64px; height: 64px; color: #CBD5E1; margin-bottom: 16px; }
    .empty-state h3 { margin: 0 0 8px 0; color: #334155; }
    .empty-state p { margin: 0; }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.active { display: flex; }

    .modal {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #E2E8F0;
    }

    .modal-header h2 { margin: 0; font-size: 18px; color: #1A2744; }

    .modal-close {
        width: 32px; height: 32px;
        border: none; background: #F1F5F9;
        border-radius: 8px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: #64748B;
    }

    .modal-close:hover { background: #E2E8F0; }
    .modal-close svg { width: 18px; height: 18px; }

    .modal-body { padding: 24px; }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .form-group input,
    .form-group select {
        padding: 10px 12px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus { border-color: #059669; }

    .form-message {
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 16px;
        display: none;
    }

    .form-message.error { background: #FEF2F2; color: #DC2626; }
    .form-message.success { background: #ECFDF5; color: #059669; }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid #E2E8F0;
    }

    .btn-secondary {
        background: #F1F5F9;
        color: #374151;
    }

    .btn-secondary:hover { background: #E2E8F0; }

    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: flex-start; }
        .filters-bar { flex-direction: column; }
        .search-box { max-width: none; }
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="utilisateurs-page">
    <div class="page-header">
        <h1 class="page-title">Gestion des Utilisateurs</h1>
        <button class="btn btn-primary" onclick="openUserModal()">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Nouvel utilisateur
        </button>
    </div>

    <div class="filters-bar">
        <div class="search-box">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="searchInput" placeholder="Rechercher par nom, email, téléphone...">
        </div>
        <select class="filter-select" id="roleFilter">
            <option value="">Tous les rôles</option>
            <option value="admin">Administrateur</option>
            <option value="minister_admin">Admin Ministère</option>
            <option value="agent">Agent</option>
            <option value="inspecteur">Inspecteur</option>
            <option value="gestionnaire_parking">Gestionnaire Parking</option>
            <option value="transporteur">Transporteur</option>
            <option value="conducteur">Conducteur</option>
            <option value="citoyen">Citoyen</option>
            <option value="imprimeur">Imprimeur</option>
            <option value="receptionnaire">Réceptionnaire</option>
            <option value="operateur_saisie">Opérateur Saisie</option>
            <option value="validateur">Validateur</option>
            <option value="receveur">Receveur</option>
            <option value="instructeur">Instructeur</option>
        </select>
        <select class="filter-select" id="statutFilter">
            <option value="">Tous les statuts</option>
            <option value="actif">Actif</option>
            <option value="inactif">Inactif</option>
            <option value="suspendu">Suspendu</option>
        </select>
    </div>

    <div class="table-container">
        <?php if (empty($utilisateurs)): ?>
            <div class="empty-state">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <h3>Aucun utilisateur trouvé</h3>
                <p>Commencez par ajouter un nouvel utilisateur</p>
            </div>
        <?php else: ?>
            <table class="data-table" id="utilisateursTable">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Dernière connexion</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $user): 
                        $roleMeta = getRoleMeta($user['role'] ?? 'citoyen');
                        $statutMeta = getStatutMeta($user['statut'] ?? 'actif');
                        $initials = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
                    ?>
                        <tr data-role="<?= htmlspecialchars($user['role'] ?? '') ?>" data-statut="<?= htmlspecialchars($user['statut'] ?? '') ?>">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
                                    <div>
                                        <div class="user-name"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></div>
                                        <div class="user-email"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>
                            <td>
                                <span class="badge" style="background: <?= $roleMeta['bg'] ?>; color: <?= $roleMeta['color'] ?>;">
                                    <?= htmlspecialchars($roleMeta['label']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background: <?= $statutMeta['bg'] ?>; color: <?= $statutMeta['color'] ?>;">
                                    <?= htmlspecialchars($statutMeta['label']) ?>
                                </span>
                            </td>
                            <td><?= formatDateTime($user['derniere_connexion'] ?? null) ?></td>
                            <td><?= formatDate($user['date_creation'] ?? null) ?></td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn action-btn-edit" title="Modifier" onclick="editUser(<?= (int)$user['id'] ?>)">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button class="action-btn action-btn-delete" title="Supprimer" onclick="deleteUser(<?= (int)$user['id'] ?>)">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

<!-- Modal Ajouter/Modifier -->
<div class="modal-overlay" id="userModal">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Nouvel utilisateur</h2>
            <button class="modal-close" onclick="closeUserModal()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="userForm">
            <div class="modal-body">
                <div id="formMessage" class="form-message"></div>
                <input type="hidden" id="userId" name="id">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="text" id="telephone" name="telephone">
                    </div>
                    <div class="form-group">
                        <label for="role">Rôle</label>
                        <select id="role" name="role">
                            <option value="minister_admin">Admin Ministère</option>
                            <option value="admin">Administrateur</option>
                            <option value="imprimeur">Imprimeur</option>
                            <option value="receptionnaire">Réceptionnaire</option>
                            <option value="operateur_saisie">Opérateur Saisie</option>
                            <option value="validateur">Validateur</option>
                            <option value="receveur">Receveur</option>
                            <option value="instructeur">Instructeur</option>
                        </select>
                    </div>
                    <div class="form-group" id="statutGroup" style="display:none;">
                        <label for="statut">Statut</label>
                        <select id="statut" name="statut">
                            <option value="actif">Actif</option>
                            <option value="inactif">Inactif</option>
                            <option value="suspendu">Suspendu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mot_de_passe" id="passwordLabel">Mot de passe *</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe">
                    </div>
                    <div class="form-group">
                        <label for="mot_de_passe_confirm" id="passwordConfirmLabel">Confirmer *</label>
                        <input type="password" id="mot_de_passe_confirm">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeUserModal()">Annuler</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div id="toastContainer" class="toast-container"></div>

<script>
const BASE_PATH = '<?= BASE_PATH ?>';

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const icons = {
        success: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        error: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    };
    toast.innerHTML = icons[type] + '<span>' + message + '</span>';
    const progress = document.createElement('div');
    progress.className = 'toast-progress';
    toast.appendChild(progress);
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'toastSlideOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 350);
    }, 4000);
}

function openUserModal(isEdit = false) {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('formMessage').style.display = 'none';

    if (isEdit) {
        document.getElementById('modalTitle').textContent = 'Modifier l\'utilisateur';
        document.getElementById('statutGroup').style.display = '';
        document.getElementById('passwordLabel').textContent = 'Nouveau mot de passe';
        document.getElementById('passwordConfirmLabel').textContent = 'Confirmer';
        document.getElementById('mot_de_passe').removeAttribute('required');
        document.getElementById('mot_de_passe_confirm').removeAttribute('required');
    } else {
        document.getElementById('modalTitle').textContent = 'Nouvel utilisateur';
        document.getElementById('statutGroup').style.display = 'none';
        document.getElementById('passwordLabel').textContent = 'Mot de passe *';
        document.getElementById('passwordConfirmLabel').textContent = 'Confirmer *';
    }

    document.getElementById('userModal').classList.add('active');
}

function closeUserModal() {
    document.getElementById('userModal').classList.remove('active');
}

document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeUserModal();
});

async function editUser(id) {
    try {
        const response = await fetch(`${BASE_PATH}/admin/api/utilisateurs?id=${id}`);
        const user = await response.json();
        if (user && !user.error) {
            openUserModal(true);
            document.getElementById('userId').value = user.id;
            document.getElementById('nom').value = user.nom || '';
            document.getElementById('prenom').value = user.prenom || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('telephone').value = user.telephone || '';
            document.getElementById('role').value = user.role || 'citoyen';
            document.getElementById('statut').value = user.statut || 'actif';
        } else {
            showToast(user.error || 'Utilisateur non trouvé', 'error');
        }
    } catch (error) {
        showToast('Erreur lors du chargement', 'error');
    }
}

async function deleteUser(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) return;
    try {
        const response = await fetch(`${BASE_PATH}/admin/api/utilisateurs?id=${id}`, { method: 'DELETE' });
        const result = await response.json();
        if (result.success) {
            showToast('Utilisateur supprimé avec succès');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(result.error || 'Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        showToast('Erreur de connexion', 'error');
    }
}

document.getElementById('userForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const messageEl = document.getElementById('formMessage');
    const submitBtn = document.getElementById('submitBtn');
    const userId = document.getElementById('userId').value;
    const isEdit = !!userId;

    const password = document.getElementById('mot_de_passe').value;
    const passwordConfirm = document.getElementById('mot_de_passe_confirm').value;

    if (!isEdit && !password) {
        messageEl.className = 'form-message error';
        messageEl.textContent = 'Le mot de passe est requis';
        messageEl.style.display = 'block';
        return;
    }

    if (password && password !== passwordConfirm) {
        messageEl.className = 'form-message error';
        messageEl.textContent = 'Les mots de passe ne correspondent pas';
        messageEl.style.display = 'block';
        return;
    }

    const data = {
        nom: document.getElementById('nom').value,
        prenom: document.getElementById('prenom').value,
        email: document.getElementById('email').value,
        telephone: document.getElementById('telephone').value,
        role: document.getElementById('role').value,
    };

    if (password) data.mot_de_passe = password;

    if (isEdit) {
        data.id = userId;
        data.statut = document.getElementById('statut').value;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Enregistrement...';

    try {
        const response = await fetch(`${BASE_PATH}/admin/api/utilisateurs`, {
            method: isEdit ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (result.success) {
            closeUserModal();
            showToast(isEdit ? 'Utilisateur modifié avec succès' : 'Utilisateur créé avec succès');
            setTimeout(() => window.location.reload(), 1000);
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

// Search filter
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('roleFilter').addEventListener('change', applyFilters);
document.getElementById('statutFilter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const role = document.getElementById('roleFilter').value;
    const statut = document.getElementById('statutFilter').value;
    const rows = document.querySelectorAll('#utilisateursTable tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowRole = row.dataset.role;
        const rowStatut = row.dataset.statut;

        const matchSearch = !search || text.includes(search);
        const matchRole = !role || rowRole === role;
        const matchStatut = !statut || rowStatut === statut;

        row.style.display = (matchSearch && matchRole && matchStatut) ? '' : 'none';
    });
}
</script>
