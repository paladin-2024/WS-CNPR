<?php
$conducteurs = $conducteurs ?? [];
$paiementsValides = $paiementsValides ?? [];
$search = $search ?? '';
$date_debut = $date_debut ?? '';
$date_fin = $date_fin ?? '';

function formatDate($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}

function formatCurrency($amount) {
    return number_format($amount, 2, ',', ' ') . ' USD';
}
?>

<style>
    .imp-page { padding: 16px; }

    .imp-toolbar {
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
        margin-bottom: 12px; background: white; border-radius: 10px;
        padding: 12px 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .imp-toolbar-title {
        font-size: 18px; font-weight: 700; color: #1A2744; margin: 0;
        white-space: nowrap;
    }
    .imp-toolbar-sep {
        width: 1px; height: 28px; background: #E2E8F0; flex-shrink: 0;
    }

    .search-form {
        display: flex; gap: 8px; flex: 1; min-width: 300px;
    }
    .search-form input {
        flex: 1; padding: 8px 12px; border: 1px solid #E2E8F0;
        border-radius: 6px; font-size: 13px; outline: none;
    }
    .search-form input:focus { border-color: #3B82F6; }
    .search-form button {
        padding: 8px 16px; background: #3B82F6; color: white; border: none;
        border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer;
    }
    .search-form button:hover { background: #2563EB; }

    .imp-tabs {
        display: flex; gap: 0; margin-bottom: 12px;
        background: white; border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden;
    }
    .imp-tab {
        flex: 1; padding: 12px 20px; text-align: center; cursor: pointer;
        font-size: 14px; font-weight: 500; color: #64748B;
        border-bottom: 3px solid transparent; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        background: none; border: none;
    }
    .imp-tab:hover { color: #334155; background: #F8FAFC; }
    .imp-tab.active { color: #3B82F6; border-bottom-color: #3B82F6; font-weight: 600; }
    .imp-tab .tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 22px; padding: 0 6px; border-radius: 12px;
        font-size: 12px; font-weight: 600;
    }
    .imp-tab.active .tab-count { background: #3B82F6; color: white; }
    .imp-tab:not(.active) .tab-count { background: #E2E8F0; color: #64748B; }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; border-radius: 6px; font-size: 13px;
        font-weight: 500; cursor: pointer; border: none; transition: all 0.15s;
        text-decoration: none;
    }
    .btn svg { width: 16px; height: 16px; }
    .btn-primary { background: #3B82F6; color: white; }
    .btn-primary:hover { background: #2563EB; }
    .btn-success { background: #059669; color: white; }
    .btn-success:hover { background: #047857; }
    .btn-outline { background: white; color: #374151; border: 1px solid #E2E8F0; }
    .btn-outline:hover { background: #F8FAFC; }
    .btn-sm { padding: 5px 10px; font-size: 12px; }

    .table-container {
        background: white; border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow-x: auto;
    }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
        text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 600;
        color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;
        background: #F8FAFC; border-bottom: 1px solid #E2E8F0;
        position: sticky; top: 0; z-index: 1;
    }
    .data-table td {
        padding: 8px 12px; font-size: 13px; color: #334155;
        border-bottom: 1px solid #F1F5F9;
    }
    .data-table tr:hover td { background: #F8FAFC; }

    .conducteur-info { display: flex; align-items: center; gap: 8px; }
    .conducteur-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: linear-gradient(135deg, #3B82F6, #8B5CF6);
        color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 12px; flex-shrink: 0;
    }
    .conducteur-avatar.green { background: linear-gradient(135deg, #059669, #10B981); }
    .conducteur-avatar.orange { background: linear-gradient(135deg, #D97706, #F59E0B); }
    .conducteur-name { font-weight: 500; color: #1A2744; font-size: 13px; }

    .badge {
        display: inline-flex; align-items: center; padding: 2px 8px;
        border-radius: 20px; font-size: 11px; font-weight: 500;
    }
    .badge-paye { background: #ECFDF5; color: #059669; }
    .badge-nonpaye { background: #FEE2E2; color: #DC2626; }

    .empty-state {
        text-align: center; padding: 40px 20px; color: #64748B;
    }
    .empty-state svg { width: 48px; height: 48px; color: #CBD5E1; margin-bottom: 12px; }
    .empty-state h3 { margin: 0 0 4px 0; color: #334155; font-size: 15px; }
    .empty-state p { margin: 0; font-size: 13px; }

    .toast-container {
        position: fixed; top: 24px; right: 24px; z-index: 99999;
        display: flex; flex-direction: column; gap: 12px; pointer-events: none;
    }
    .toast {
        padding: 12px 20px; border-radius: 10px; color: white; font-size: 13px;
        font-weight: 500; box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        animation: toastIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        display: flex; align-items: center; gap: 10px; min-width: 280px;
        pointer-events: auto;
    }
    .toast-success { background: linear-gradient(135deg, #10B981, #059669); }
    .toast-error { background: linear-gradient(135deg, #EF4444, #DC2626); }
    .toast svg { width: 20px; height: 20px; flex-shrink: 0; }
    @keyframes toastIn {
        0% { transform: translateX(120%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    @keyframes toastOut {
        0% { transform: translateX(0); opacity: 1; }
        100% { transform: translateX(120%); opacity: 0; }
    }

    .modal-overlay {
        display: none; position: fixed; inset: 0; z-index: 1000;
        background: rgba(0,0,0,0.5); align-items: center; justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: white; border-radius: 12px; width: 90%; max-width: 420px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .modal-header {
        padding: 16px 20px; border-bottom: 1px solid #E2E8F0;
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-header h3 { margin: 0; font-size: 16px; font-weight: 600; color: #1A2744; }
    .modal-close {
        background: none; border: none; cursor: pointer; padding: 4px; color: #64748B;
    }
    .modal-close:hover { color: #334155; }
    .modal-body { padding: 20px; }
    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;
    }
    .form-group input {
        width: 100%; padding: 10px 12px; border: 1px solid #E2E8F0;
        border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box;
    }
    .form-group input:focus { border-color: #3B82F6; }
    .modal-footer {
        padding: 16px 20px; border-top: 1px solid #E2E8F0;
        display: flex; gap: 10px; justify-content: flex-end;
    }
    .paiement-details {
        background: #F8FAFC; border-radius: 8px; padding: 12px; margin-bottom: 16px;
    }
    .paiement-details-row {
        display: flex; justify-content: space-between; margin-bottom: 6px;
        font-size: 13px;
    }
    .paiement-details-row:last-child { margin-bottom: 0; }
    .paiement-details-label { color: #64748B; }
    .paiement-details-value { font-weight: 500; color: #1A2744; }

    @media (max-width: 768px) {
        .imp-toolbar { flex-direction: column; align-items: stretch; }
        .imp-toolbar-sep { display: none; }
        .search-form { flex-direction: column; }
        .imp-tabs { flex-direction: column; }
    }
</style>

<div class="imp-page">

    <div class="imp-toolbar">
        <h1 class="imp-toolbar-title">Paiement Brevet</h1>
        <div class="imp-toolbar-sep"></div>
        <form class="search-form" method="GET" action="<?= BASE_PATH ?>/admin/paiement">
            <input type="text" name="search" placeholder="Rechercher nom, téléphone, permis..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">
                <i data-lucide="search" style="width:16px;height:16px;"></i>
                Rechercher
            </button>
        </form>
    </div>

    <div class="imp-tabs">
        <button class="imp-tab active" onclick="switchTab('enregistrer')" id="tabEnregistrer">
            <i data-lucide="plus" style="width:16px;height:16px;"></i>
            Enregistrer paiement
            <span class="tab-count"><?= count($conducteurs) ?></span>
        </button>
        <button class="imp-tab" onclick="switchTab('historique')" id="tabHistorique">
            <i data-lucide="clipboard" style="width:16px;height:16px;"></i>
            Historique paiements
            <span class="tab-count" id="countHistorique"><?= count($paiementsValides) ?></span>
        </button>
    </div>

    <!-- Onglet: Enregistrer paiement -->
    <div class="tab-panel active" id="panelEnregistrer">
        <?php if (empty($search)): ?>
            <div class="empty-state">
                <i data-lucide="search"></i>
                <h3>Rechercher un conducteur</h3>
                <p>Entrez un nom, téléphone ou numéro de permis pour enregistrer un paiement</p>
            </div>
        <?php elseif (empty($conducteurs)): ?>
            <div class="empty-state">
                <i data-lucide="printer"></i>
                <h3>Aucun conducteur trouvé</h3>
                <p>Aucun conducteur ne correspond à votre recherche</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Conducteur</th>
                            <th>Téléphone</th>
                            <th>N° Permis</th>
                            <th>Catégorie</th>
                            <th>Statut paiement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conducteurs as $c): 
                            $initials = strtoupper(substr($c['prenom'] ?? '', 0, 1) . substr($c['nom'] ?? '', 0, 1));
                            $aPaiement = !empty($c['paiement_montant']);
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($c['id']) ?></strong></td>
                                <td>
                                    <div class="conducteur-info">
                                        <div class="conducteur-avatar <?= $aPaiement ? 'green' : 'orange' ?>"><?= htmlspecialchars($initials) ?></div>
                                        <span class="conducteur-name"><?= htmlspecialchars(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($c['telephone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['numero_permis'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['categorie_permis'] ?? '-') ?></td>
                                <td>
                                    <?php if ($aPaiement): ?>
                                        <span class="badge badge-paye">
                                            <i data-lucide="check" style="width:12px;height:12px;margin-right:4px;"></i>
                                            Payé: <?= formatCurrency($c['paiement_montant']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-nonpaye">Non payé</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="openPaiementModal(<?= $c['id'] ?>, '<?= htmlspecialchars(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?>', '<?= htmlspecialchars($c['numero_permis'] ?? '') ?>', '<?= $aPaiement ? $c['paiement_montant'] : '' ?>', '<?= $aPaiement ? $c['paiement_reference'] : '' ?>')">
                                        <i data-lucide="plus"></i>
                                        <?= $aPaiement ? 'Modifier' : 'Enregistrer' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Onglet: Historique paiements -->
    <div class="tab-panel" id="panelHistorique">
        <div class="imp-toolbar" style="margin-bottom:12px;padding:8px 16px;">
            <form method="GET" action="<?= BASE_PATH ?>/admin/paiement" style="display:flex;gap:8px;align-items:center;">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="tab" value="historique">
                <span style="font-size:13px;color:#64748B;">Du:</span>
                <input type="date" name="date_debut" value="<?= htmlspecialchars($date_debut) ?>" style="padding:6px 8px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                <span style="font-size:13px;color:#64748B;">Au:</span>
                <input type="date" name="date_fin" value="<?= htmlspecialchars($date_fin) ?>" style="padding:6px 8px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
            </form>
            <a href="<?= BASE_PATH ?>/admin/paiement/export-excel?date_debut=<?= urlencode($date_debut) ?>&date_fin=<?= urlencode($date_fin) ?>" class="btn btn-success btn-sm" id="exportExcelBtn" title="Télécharger le fichier Excel (.xlsx)">
                <i data-lucide="download"></i>
                Exporter XLSX
            </a>
        </div>
        
        <div class="table-container">
            <?php if (empty($paiementsValides)): ?>
                <div class="empty-state">
                    <i data-lucide="clipboard"></i>
                    <h3>Aucun paiement enregistré</h3>
                    <p>Aucun paiement pour cette période</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Conducteur</th>
                            <th>Téléphone</th>
                            <th>N° Permis</th>
                            <th>Montant</th>
                            <th>Référence</th>
                            <th>Date paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paiementsValides as $p): 
                            $initials = strtoupper(substr($p['prenom'] ?? '', 0, 1) . substr($p['nom'] ?? '', 0, 1));
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['id']) ?></strong></td>
                                <td>
                                    <div class="conducteur-info">
                                        <div class="conducteur-avatar green"><?= htmlspecialchars($initials) ?></div>
                                        <span class="conducteur-name"><?= htmlspecialchars(($p['conducteur_prenom'] ?? '') . ' ' . ($p['conducteur_nom'] ?? '')) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($p['conducteur_telephone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($p['numero_permis'] ?? '-') ?></td>
                                <td><strong><?= formatCurrency($p['montant'] ?? 0) ?></strong></td>
                                <td><?= htmlspecialchars($p['reference_paiement'] ?? '-') ?></td>
                                <td><?= formatDate($p['date_paiement'] ?? null) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Enregistrer paiement -->
<div id="paiementModal" class="modal-overlay" onclick="closeModalOnOverlay(event)">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Enregistrer le paiement</h3>
            <button class="modal-close" onclick="closePaiementModal()">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="paiement-details">
                <div class="paiement-details-row">
                    <span class="paiement-details-label">Conducteur:</span>
                    <span class="paiement-details-value" id="modalConducteur">-</span>
                </div>
                <div class="paiement-details-row">
                    <span class="paiement-details-label">N° Permis:</span>
                    <span class="paiement-details-value" id="modalPermis">-</span>
                </div>
            </div>
            <form id="paiementForm" method="POST" action="<?= BASE_PATH ?>/admin/paiement/valider">
                <input type="hidden" name="conducteur_id" id="modalConducteurId">
                <div class="form-group">
                    <label for="montant">Montant (USD)</label>
                    <input type="number" name="montant" id="modalMontant" required min="0" step="0.01" placeholder="Ex: 25.00">
                </div>
                <div class="form-group">
                    <label for="reference">Référence du paiement</label>
                    <input type="text" name="reference" id="modalReference" required placeholder="Ex: RECU-2024-001234">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closePaiementModal()">Annuler</button>
            <button class="btn btn-success" onclick="submitPaiement()">
                <i data-lucide="check"></i>
                Enregistrer
            </button>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container"></div>

<script>
const BASE_PATH = '<?= BASE_PATH ?>';

function switchTab(tab) {
    document.querySelectorAll('.imp-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab' + capitalize(tab)).classList.add('active');
    document.getElementById('panel' + capitalize(tab)).classList.add('active');
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function openPaiementModal(id, nom, permis, montant, reference) {
    document.getElementById('modalConducteurId').value = id;
    document.getElementById('modalConducteur').textContent = nom;
    document.getElementById('modalPermis').textContent = permis;
    document.getElementById('modalMontant').value = montant || '';
    document.getElementById('modalReference').value = reference || '';
    document.getElementById('paiementModal').classList.add('active');
}

function closePaiementModal() {
    document.getElementById('paiementModal').classList.remove('active');
}

function closeModalOnOverlay(event) {
    if (event.target === event.currentTarget) {
        closePaiementModal();
    }
}

function submitPaiement() {
    const montant = document.getElementById('modalMontant').value;
    const reference = document.getElementById('modalReference').value;
    
    if (!montant || montant <= 0) {
        showToast('Veuillez entrer un montant valide', 'error');
        return;
    }
    if (!reference || reference.trim() === '') {
        showToast('Veuillez entrer une référence de paiement', 'error');
        return;
    }

    document.getElementById('paiementForm').submit();
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const icons = {
        success: '<i data-lucide="circle-check-big"></i>',
        error: '<i data-lucide="circle-alert"></i>'
    };
    toast.innerHTML = icons[type] + '<span>' + message + '</span>';
    container.appendChild(toast);
    if (window.lucide) lucide.createIcons();
    setTimeout(() => {
        toast.style.animation = 'toastOut 0.3s forwards';
        setTimeout(() => toast.remove(), 350);
    }, 4000);
}

window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
        showToast(urlParams.get('success'), 'success');
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url);
    }
    if (urlParams.get('error')) {
        showToast(urlParams.get('error'), 'error');
        const url = new URL(window.location);
        url.searchParams.delete('error');
        window.history.replaceState({}, document.title, url);
    }
    if (urlParams.get('tab') === 'historique') {
        switchTab('historique');
    }

    // Sync export link with date filters
    function updateExportLink() {
        const dateDebut = document.querySelector('input[name="date_debut"]');
        const dateFin   = document.querySelector('input[name="date_fin"]');
        const exportBtn = document.getElementById('exportExcelBtn');
        if (exportBtn && dateDebut && dateFin) {
            const params = new URLSearchParams();
            if (dateDebut.value) params.set('date_debut', dateDebut.value);
            if (dateFin.value)   params.set('date_fin',   dateFin.value);
            exportBtn.href = BASE_PATH + '/admin/paiement/export-excel?' + params.toString();
        }
    }
    document.querySelector('input[name="date_debut"]')?.addEventListener('change', updateExportLink);
    document.querySelector('input[name="date_fin"]')?.addEventListener('change', updateExportLink);
    updateExportLink();
});
</script>
