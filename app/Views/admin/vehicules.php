<?php
// Variables injectées par le contrôleur
$vehicules = $vehicules ?? [];

function getVehiculeTypeMeta($type) {
    $metas = [
        'taxi' => ['label' => 'Taxi', 'color' => '#3B82F6', 'bg' => '#EFF6FF'],
        'bus' => ['label' => 'Bus', 'color' => '#8B5CF6', 'bg' => '#F5F3FF'],
        'camion' => ['label' => 'Camion', 'color' => '#059669', 'bg' => '#ECFDF5'],
        'moto' => ['label' => 'Moto', 'color' => '#D97706', 'bg' => '#FFFBEB'],
        'voiture' => ['label' => 'Voiture', 'color' => '#EF4444', 'bg' => '#FEF2F2'],
    ];
    return $metas[$type] ?? ['label' => $type, 'color' => '#64748B', 'bg' => '#F1F5F9'];
}

function getVehiculeStatutMeta($statut) {
    $metas = [
        'actif' => ['label' => 'Actif', 'color' => '#059669', 'bg' => '#ECFDF5'],
        'suspendu' => ['label' => 'Suspendu', 'color' => '#DC2626', 'bg' => '#FEF2F2'],
        'radie' => ['label' => 'Radié', 'color' => '#64748B', 'bg' => '#F1F5F9'],
    ];
    return $metas[$statut] ?? ['label' => $statut, 'color' => '#64748B', 'bg' => '#F1F5F9'];
}

function formatDate($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}
?>

<style>
    .vehicules-page {
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
        color: var(--admin-navy);
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
        background: #8B5CF6;
        color: white;
    }

    .btn-primary:hover {
        background: #7C3AED;
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
        border-color: #8B5CF6;
    }

    .search-box svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: var(--admin-slate-light);
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
        min-width: 900px;
    }

    .data-table th {
        text-align: left;
        padding: 14px 16px;
        font-size: 12px;
        font-weight: 600;
        color: var(--admin-slate);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
    }

    .data-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: var(--admin-slate-dark);
        border-bottom: 1px solid #F1F5F9;
    }

    .data-table tr:hover td {
        background: #F8FAFC;
    }

    .vehicule-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .vehicule-plaque {
        font-weight: 600;
        color: var(--admin-navy);
        font-family: monospace;
        font-size: 15px;
    }

    .vehicule-marque {
        font-size: 12px;
        color: var(--admin-slate);
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-type {
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
        color: var(--admin-accent-blue);
    }

    .action-btn-edit:hover {
        background: #DBEAFE;
    }

    .action-btn-delete {
        background: var(--status-danger-bg);
        color: var(--status-danger);
    }

    .action-btn-delete:hover {
        background: #FEE2E2;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--admin-slate);
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        color: #CBD5E1;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        margin: 0 0 8px 0;
        color: var(--admin-slate-dark);
    }

    .empty-state p {
        margin: 0;
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
    }

    /* Toast */
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

    .toast-success { background: linear-gradient(135deg, #10B981, var(--status-success), #047857); }
    .toast-error { background: linear-gradient(135deg, #EF4444, var(--status-danger), #B91C1C); }
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

    .modal-header h2 { margin: 0; font-size: 18px; color: var(--admin-navy); }

    .modal-close {
        width: 32px; height: 32px;
        border: none; background: #F1F5F9;
        border-radius: 8px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--admin-slate);
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
    .form-group select:focus { border-color: #8B5CF6; }

    .form-message {
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        margin-bottom: 16px;
        display: none;
    }

    .form-message.error { background: var(--status-danger-bg); color: var(--status-danger); }
    .form-message.success { background: var(--status-success-bg); color: var(--status-success); }

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
</style>

<div class="vehicules-page">
    <div class="page-header">
        <h1 class="page-title">Gestion des Véhicules</h1>
        <button class="btn btn-primary" onclick="openModal()">
            <i data-lucide="plus"></i>
            Nouveau véhicule
        </button>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <div class="search-box">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Rechercher par plaque, marque, modèle...">
        </div>
        <select class="filter-select">
            <option value="">Tous les types</option>
            <option value="taxi">Taxi</option>
            <option value="bus">Bus</option>
            <option value="camion">Camion</option>
            <option value="moto">Moto</option>
            <option value="voiture">Voiture</option>
        </select>
        <select class="filter-select">
            <option value="">Tous les statuts</option>
            <option value="actif">Actif</option>
            <option value="suspendu">Suspendu</option>
            <option value="radie">Radié</option>
        </select>
    </div>

    <!-- Table -->
    <div class="table-container">
        <?php if (empty($vehicules)): ?>
            <div class="empty-state">
                <i data-lucide="car"></i>
                <h3>Aucun véhicule trouvé</h3>
                <p>Commencez par ajouter un nouveau véhicule</p>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Véhicule</th>
                        <th>Type</th>
                        <th>Marque / Modèle</th>
                        <th>Année</th>
                        <th>Capacité</th>
                        <th>Propriétaire</th>
                        <th>Date Immatriculation</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicules as $vehicule): 
                        $typeMeta = getVehiculeTypeMeta($vehicule['type_vehicule'] ?? 'taxi');
                        $statutMeta = getVehiculeStatutMeta($vehicule['statut'] ?? 'actif');
                    ?>
                        <tr>
                            <td>
                                <div class="vehicule-info">
                                    <span class="vehicule-plaque"><?= htmlspecialchars($vehicule['numero_plaque'] ?? '-') ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-type" style="background: <?= $typeMeta['bg'] ?>; color: <?= $typeMeta['color'] ?>;">
                                    <?= htmlspecialchars($typeMeta['label']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="vehicule-info">
                                    <span><?= htmlspecialchars(($vehicule['marque'] ?? '') . ' ' . ($vehicule['modele'] ?? '')) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($vehicule['annee'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($vehicule['capacite'] ?? '-') ?> places</td>
                            <td><?= htmlspecialchars($vehicule['proprietaire_nom'] ?? $vehicule['societe_transport'] ?? '-') ?></td>
                            <td><?= formatDate($vehicule['date_immatriculation'] ?? null) ?></td>
                            <td>
                                <span class="badge" style="background: <?= $statutMeta['bg'] ?>; color: <?= $statutMeta['color'] ?>;">
                                    <?= htmlspecialchars($statutMeta['label']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn action-btn-edit" title="Modifier" onclick="editVehicule(<?= (int)$vehicule['id'] ?>)">
                                        <i data-lucide="pencil"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" title="Supprimer" onclick="deleteVehicule(<?= (int)$vehicule['id'] ?>)">
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

<!-- Modal Ajouter/Modifier -->
<div class="modal-overlay" id="vehiculeModal">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Nouveau véhicule</h2>
            <button class="modal-close" onclick="closeVehiculeModal()">
                <i data-lucide="x"></i>
            </button>
        </div>
        <form id="vehiculeForm">
            <?= \App\Core\Csrf::field() ?>
            <div class="modal-body">
                <div id="formMessage" class="form-message"></div>
                <input type="hidden" id="vehiculeId" name="id">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="numero_plaque">Numéro de plaque *</label>
                        <input type="text" id="numero_plaque" name="numero_plaque" required>
                    </div>
                    <div class="form-group">
                        <label for="type_vehicule">Type *</label>
                        <select id="type_vehicule" name="type_vehicule" required>
                            <option value="taxi">Taxi</option>
                            <option value="bus">Bus</option>
                            <option value="camion">Camion</option>
                            <option value="moto">Moto</option>
                            <option value="voiture">Voiture</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="marque">Marque *</label>
                        <input type="text" id="marque" name="marque" required>
                    </div>
                    <div class="form-group">
                        <label for="modele">Modèle</label>
                        <input type="text" id="modele" name="modele">
                    </div>
                    <div class="form-group">
                        <label for="annee">Année</label>
                        <input type="number" id="annee" name="annee" min="1950" max="2100">
                    </div>
                    <div class="form-group">
                        <label for="capacite">Capacité (places)</label>
                        <input type="number" id="capacite" name="capacite" min="1">
                    </div>
                    <div class="form-group">
                        <label for="proprietaire_nom">Nom du propriétaire</label>
                        <input type="text" id="proprietaire_nom" name="proprietaire_nom">
                    </div>
                    <div class="form-group">
                        <label for="societe_transport">Société de transport</label>
                        <input type="text" id="societe_transport" name="societe_transport">
                    </div>
                    <div class="form-group" id="statutGroup" style="display:none;">
                        <label for="statut">Statut</label>
                        <select id="statut" name="statut">
                            <option value="actif">Actif</option>
                            <option value="suspendu">Suspendu</option>
                            <option value="radie">Radié</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeVehiculeModal()">Annuler</button>
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
        success: '<i data-lucide="circle-check-big"></i>',
        error: '<i data-lucide="circle-alert"></i>'
    };
    toast.innerHTML = icons[type] + '<span>' + message + '</span>';
    const progress = document.createElement('div');
    progress.className = 'toast-progress';
    toast.appendChild(progress);
    container.appendChild(toast);
    if (window.lucide) lucide.createIcons();
    setTimeout(() => {
        toast.style.animation = 'toastSlideOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 350);
    }, 4000);
}

function openModal(isEdit = false) {
    document.getElementById('vehiculeForm').reset();
    document.getElementById('vehiculeId').value = '';
    document.getElementById('formMessage').style.display = 'none';
    document.getElementById('modalTitle').textContent = isEdit ? 'Modifier le véhicule' : 'Nouveau véhicule';
    document.getElementById('statutGroup').style.display = isEdit ? '' : 'none';
    document.getElementById('vehiculeModal').classList.add('active');
}

function closeVehiculeModal() {
    document.getElementById('vehiculeModal').classList.remove('active');
}

document.getElementById('vehiculeModal').addEventListener('click', function(e) {
    if (e.target === this) closeVehiculeModal();
});

async function editVehicule(id) {
    try {
        const response = await fetch(`${BASE_PATH}/admin/api/vehicules?id=${id}`);
        const vehicule = await response.json();
        if (vehicule && !vehicule.error) {
            openModal(true);
            document.getElementById('vehiculeId').value = vehicule.id;
            document.getElementById('numero_plaque').value = vehicule.numero_plaque || '';
            document.getElementById('type_vehicule').value = vehicule.type_vehicule || 'taxi';
            document.getElementById('marque').value = vehicule.marque || '';
            document.getElementById('modele').value = vehicule.modele || '';
            document.getElementById('annee').value = vehicule.annee || '';
            document.getElementById('capacite').value = vehicule.capacite || '';
            document.getElementById('proprietaire_nom').value = vehicule.proprietaire_nom || '';
            document.getElementById('societe_transport').value = vehicule.societe_transport || '';
            document.getElementById('statut').value = vehicule.statut || 'actif';
        } else {
            showToast(vehicule.error || 'Véhicule non trouvé', 'error');
        }
    } catch (error) {
        showToast('Erreur lors du chargement', 'error');
    }
}

async function deleteVehicule(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')) return;
    try {
        const response = await fetch(`${BASE_PATH}/admin/api/vehicules?id=${id}`, { method: 'DELETE', headers: { 'X-CSRF-Token': CSRF_TOKEN } });
        const result = await response.json();
        if (result.success) {
            showToast('Véhicule supprimé avec succès');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(result.error || 'Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        showToast('Erreur de connexion', 'error');
    }
}

document.getElementById('vehiculeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const messageEl = document.getElementById('formMessage');
    const submitBtn = document.getElementById('submitBtn');
    const vehiculeId = document.getElementById('vehiculeId').value;
    const isEdit = !!vehiculeId;

    const data = {
        numero_plaque: document.getElementById('numero_plaque').value,
        type_vehicule: document.getElementById('type_vehicule').value,
        marque: document.getElementById('marque').value,
        modele: document.getElementById('modele').value,
        annee: document.getElementById('annee').value || null,
        capacite: document.getElementById('capacite').value || null,
        proprietaire_nom: document.getElementById('proprietaire_nom').value,
        societe_transport: document.getElementById('societe_transport').value,
    };

    if (isEdit) {
        data.id = vehiculeId;
        data.statut = document.getElementById('statut').value;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Enregistrement...';

    try {
        const response = await fetch(`${BASE_PATH}/admin/api/vehicules`, {
            method: isEdit ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF_TOKEN },
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (result.success) {
            closeVehiculeModal();
            showToast(isEdit ? 'Véhicule modifié avec succès' : 'Véhicule créé avec succès');
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
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Enregistrer';
    }
});
</script>
