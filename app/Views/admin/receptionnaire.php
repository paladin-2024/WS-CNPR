<?php
$conducteurs = $conducteurs ?? [];
$conducteursImprimes = $conducteursImprimes ?? [];
$date_debut = $date_debut ?? '';
$date_fin = $date_fin ?? '';

function formatDateRecep($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}
?>

<style>
    .recep-page { padding: 16px; }

    /* ── Toolbar compacte ── */
    .recep-toolbar {
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
        margin-bottom: 12px; background: white; border-radius: 10px;
        padding: 12px 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .recep-toolbar-title {
        font-size: 18px; font-weight: 700; color: #1A2744; margin: 0;
        white-space: nowrap;
    }
    .recep-toolbar-sep {
        width: 1px; height: 28px; background: #E2E8F0; flex-shrink: 0;
    }
    .recep-toolbar input[type="date"] {
        padding: 7px 10px; border: 1px solid #E2E8F0; border-radius: 6px;
        font-size: 13px; outline: none; width: 140px;
    }
    .recep-toolbar input[type="date"]:focus { border-color: #3B82F6; }
    .recep-search {
        position: relative; flex: 1; min-width: 180px; max-width: 320px;
    }
    .recep-search svg {
        position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
        width: 16px; height: 16px; color: #94A3B8;
    }
    .recep-search input {
        width: 100%; padding: 7px 12px 7px 34px; border: 1px solid #E2E8F0;
        border-radius: 6px; font-size: 13px; outline: none;
    }
    .recep-search input:focus { border-color: #3B82F6; }
    .recep-badge-count {
        display: inline-flex; align-items: center; gap: 6px;
        background: #FFFBEB; color: #D97706; padding: 5px 12px;
        border-radius: 20px; font-size: 13px; font-weight: 600; white-space: nowrap;
    }

    /* ── Onglets ── */
    .recep-tabs {
        display: flex; gap: 0; margin-bottom: 12px;
        background: white; border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden;
    }
    .recep-tab {
        flex: 1; padding: 12px 20px; text-align: center; cursor: pointer;
        font-size: 14px; font-weight: 500; color: #64748B;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        background: none; border: none; border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    .recep-tab:hover { color: #334155; background: #F8FAFC; }
    .recep-tab.active { color: #D97706; border-bottom-color: #D97706; font-weight: 600; }
    .recep-tab.active-green { color: #059669; border-bottom-color: #059669; font-weight: 600; }
    .recep-tab .tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 22px; padding: 0 6px; border-radius: 12px;
        font-size: 12px; font-weight: 600;
    }
    .recep-tab.active .tab-count { background: #D97706; color: white; }
    .recep-tab.active-green .tab-count { background: #059669; color: white; }
    .recep-tab:not(.active):not(.active-green) .tab-count { background: #E2E8F0; color: #64748B; }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ── Actions compactes ── */
    .recep-actions {
        display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap;
        align-items: center;
    }
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
    .btn-sm { padding: 5px 10px; font-size: 12px; }
    .btn-sm svg { width: 14px; height: 14px; }

    /* ── Tableau compact ── */
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
        width: 30px; height: 30px; border-radius: 50%;
        background: linear-gradient(135deg, #D97706, #F59E0B);
        color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 11px; flex-shrink: 0;
    }
    .conducteur-avatar.green { background: linear-gradient(135deg, #059669, #10B981); }
    .conducteur-name { font-weight: 500; color: #1A2744; font-size: 13px; }

    .badge {
        display: inline-flex; align-items: center; padding: 2px 8px;
        border-radius: 20px; font-size: 11px; font-weight: 500;
    }
    .badge-pending { background: #FFFBEB; color: #D97706; }
    .badge-confirmed { background: #ECFDF5; color: #059669; }

    .empty-state {
        text-align: center; padding: 40px 20px; color: #64748B;
    }
    .empty-state svg { width: 48px; height: 48px; color: #CBD5E1; margin-bottom: 12px; }
    .empty-state h3 { margin: 0 0 4px 0; color: #334155; font-size: 15px; }
    .empty-state p { margin: 0; font-size: 13px; }

    .sel-count { font-size: 13px; color: #64748B; white-space: nowrap; }

    /* ── Toast ── */
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

    @media (max-width: 768px) {
        .recep-toolbar { flex-direction: column; align-items: stretch; }
        .recep-toolbar-sep { display: none; }
        .recep-search { max-width: none; }
        .recep-actions { flex-direction: column; }
        .recep-tabs { flex-direction: column; }
    }
</style>

<div class="recep-page">

    <!-- Toolbar compacte -->
    <div class="recep-toolbar">
        <h1 class="recep-toolbar-title">Réception Brevets</h1>
        <div class="recep-toolbar-sep"></div>
        <input type="date" id="filterDateDebut" value="<?= htmlspecialchars($date_debut) ?>" title="Date début">
        <input type="date" id="filterDateFin" value="<?= htmlspecialchars($date_fin) ?>" title="Date fin">
        <button class="btn btn-primary" onclick="filtrerParDate()">
            <i data-lucide="funnel"></i>
            Filtrer
        </button>
        <div class="recep-toolbar-sep"></div>
        <div class="recep-search">
            <i data-lucide="search"></i>
            <input type="text" id="searchInput" placeholder="Rechercher nom, permis...">
        </div>
        <span class="recep-badge-count" id="badgeCount">
            <i data-lucide="clock" style="width:14px;height:14px;"></i>
            <span id="totalCount"><?= count($conducteurs) ?></span> en attente
        </span>
    </div>

    <!-- Onglets -->
    <div class="recep-tabs">
        <button class="recep-tab active" onclick="switchTab('attente')" id="tabAttente">
            <i data-lucide="clock" style="width:16px;height:16px;"></i>
            En attente de réception
            <span class="tab-count" id="countAttente"><?= count($conducteurs) ?></span>
        </button>
        <button class="recep-tab" onclick="switchTab('recus')" id="tabRecus">
            <i data-lucide="circle-check-big" style="width:16px;height:16px;"></i>
            Déjà réceptionnés
            <span class="tab-count" id="countRecus"><?= count($conducteursImprimes) ?></span>
        </button>
    </div>

    <!-- ═══ ONGLET 1 : En attente ═══ -->
    <div class="tab-panel active" id="panelAttente">
        <!-- Actions + sélection -->
        <div class="recep-actions" id="actionsBar" style="<?= empty($conducteurs) ? 'display:none' : '' ?>">
            <button class="btn btn-success" onclick="confirmerSelectionnes()">
                <i data-lucide="circle-check-big"></i>
                Confirmer sélection
            </button>
            <button class="btn btn-primary" onclick="confirmerTous()">
                <i data-lucide="check"></i>
                Confirmer tous
            </button>
            <span class="sel-count" id="selectionCount">0 sélectionné(s)</span>
        </div>

        <div class="table-container">
            <div id="emptyAttente" style="<?= empty($conducteurs) ? '' : 'display:none' ?>">
                <div class="empty-state">
                    <i data-lucide="circle-check-big"></i>
                    <h3>Aucun brevet en attente</h3>
                    <p>Aucun brevet en cours d'impression à réceptionner</p>
                </div>
            </div>
            <table class="data-table" id="tableAttente" style="<?= empty($conducteurs) ? 'display:none' : '' ?>">
                <thead>
                    <tr>
                        <th style="width:30px"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                        <th>ID</th>
                        <th>Conducteur</th>
                        <th>N° Permis</th>
                        <th>Cat.</th>
                        <th>Enregistrement</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="bodyAttente">
                    <?php foreach ($conducteurs as $c):
                        $initials = strtoupper(substr($c['prenom'] ?? '', 0, 1) . substr($c['nom'] ?? '', 0, 1));
                    ?>
                        <tr id="row-<?= $c['id'] ?>">
                            <td><input type="checkbox" class="conducteur-check" value="<?= $c['id'] ?>" onchange="updateSelectionCount()"></td>
                            <td><strong><?= $c['id'] ?></strong></td>
                            <td>
                                <div class="conducteur-info">
                                    <div class="conducteur-avatar"><?= htmlspecialchars($initials) ?></div>
                                    <span class="conducteur-name"><?= htmlspecialchars(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($c['numero_permis'] ?? '-') ?></td>
                            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;"><?= htmlspecialchars($c['categorie_permis'] ?? '-') ?></span></td>
                            <td><?= formatDateRecep($c['date_enregistrement'] ?? null) ?></td>
                            <td><span class="badge badge-pending" id="badge-<?= $c['id'] ?>">En cours</span></td>
                            <td>
                                <button class="btn btn-success btn-sm" id="btn-<?= $c['id'] ?>" onclick="confirmerUn(<?= $c['id'] ?>)">
                                    <i data-lucide="check"></i>
                                    OK
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══ ONGLET 2 : Déjà réceptionnés ═══ -->
    <div class="tab-panel" id="panelRecus">
        <div class="table-container">
            <div id="emptyRecus" style="<?= empty($conducteursImprimes) ? '' : 'display:none' ?>">
                <div class="empty-state">
                    <i data-lucide="circle-check-big"></i>
                    <h3>Aucun brevet réceptionné</h3>
                    <p>Aucun brevet réceptionné pour le moment</p>
                </div>
            </div>
            <table class="data-table" id="tableRecus" style="<?= empty($conducteursImprimes) ? 'display:none' : '' ?>">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Conducteur</th>
                        <th>N° Permis</th>
                        <th>Cat.</th>
                        <th>Enregistrement</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody id="bodyRecus">
                    <?php foreach ($conducteursImprimes as $c):
                        $initials = strtoupper(substr($c['prenom'] ?? '', 0, 1) . substr($c['nom'] ?? '', 0, 1));
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['id']) ?></strong></td>
                            <td>
                                <div class="conducteur-info">
                                    <div class="conducteur-avatar green"><?= htmlspecialchars($initials) ?></div>
                                    <span class="conducteur-name"><?= htmlspecialchars(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($c['numero_permis'] ?? '-') ?></td>
                            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;"><?= htmlspecialchars($c['categorie_permis'] ?? '-') ?></span></td>
                            <td><?= formatDateRecep($c['date_enregistrement'] ?? null) ?></td>
                            <td><span class="badge badge-confirmed">Réceptionné ✓</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container"></div>

<script>
const BASE_PATH = '<?= BASE_PATH ?>';
let currentTab = 'attente';

// ── Onglets ──
function switchTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.recep-tab').forEach(t => { t.classList.remove('active'); t.classList.remove('active-green'); });
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

    const tabEl = document.getElementById('tab' + capitalize(tab));
    tabEl.classList.add(tab === 'recus' ? 'active-green' : 'active');
    document.getElementById('panel' + capitalize(tab)).classList.add('active');
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
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

// ── Recherche instantanée ──
document.getElementById('searchInput').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    const bodyMap = { attente: 'bodyAttente', recus: 'bodyRecus' };
    document.querySelectorAll('#' + bodyMap[currentTab] + ' tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});

// ── Filtre par date AJAX ──
async function filtrerParDate() {
    const dateDebut = document.getElementById('filterDateDebut').value;
    const dateFin = document.getElementById('filterDateFin').value;

    const params = new URLSearchParams();
    if (dateDebut) params.set('date_debut', dateDebut);
    if (dateFin) params.set('date_fin', dateFin);

    try {
        params.set('statut', 'en_cours_impression');
        const resp1 = await fetch(BASE_PATH + '/admin/api/receptionnaire?' + params.toString());
        const attente = await resp1.json();

        params.set('statut', 'imprime');
        const resp2 = await fetch(BASE_PATH + '/admin/api/receptionnaire?' + params.toString());
        const recus = await resp2.json();

        renderAttente(attente);
        renderRecus(recus);
    } catch (e) {
        showToast('Erreur lors du filtrage', 'error');
    }
}

function renderAttente(conducteurs) {
    const tbody = document.getElementById('bodyAttente');
    const table = document.getElementById('tableAttente');
    const empty = document.getElementById('emptyAttente');
    const actions = document.getElementById('actionsBar');

    document.getElementById('totalCount').textContent = conducteurs.length;
    document.getElementById('countAttente').textContent = conducteurs.length;

    if (conducteurs.length === 0) {
        table.style.display = 'none'; empty.style.display = ''; actions.style.display = 'none';
        tbody.innerHTML = ''; return;
    }

    table.style.display = ''; empty.style.display = 'none'; actions.style.display = '';

    tbody.innerHTML = conducteurs.map(c => {
        const initials = ((c.prenom || '').charAt(0) + (c.nom || '').charAt(0)).toUpperCase();
        const dateEnr = c.date_enregistrement ? new Date(c.date_enregistrement).toLocaleDateString('fr-FR') : '-';
        return `<tr id="row-${c.id}">
            <td><input type="checkbox" class="conducteur-check" value="${c.id}" onchange="updateSelectionCount()"></td>
            <td><strong>${c.id}</strong></td>
            <td><div class="conducteur-info"><div class="conducteur-avatar">${initials}</div><span class="conducteur-name">${(c.prenom||'')} ${(c.nom||'')}</span></div></td>
            <td>${c.numero_permis || '-'}</td>
            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;">${c.categorie_permis || '-'}</span></td>
            <td>${dateEnr}</td>
            <td><span class="badge badge-pending" id="badge-${c.id}">En cours</span></td>
            <td><button class="btn btn-success btn-sm" id="btn-${c.id}" onclick="confirmerUn(${c.id})"><i data-lucide="check"></i> OK</button></td>
        </tr>`;
    }).join('');
    if (window.lucide) lucide.createIcons();

    document.getElementById('selectAll').checked = false;
    updateSelectionCount();
}

function renderRecus(conducteurs) {
    const tbody = document.getElementById('bodyRecus');
    const table = document.getElementById('tableRecus');
    const empty = document.getElementById('emptyRecus');

    document.getElementById('countRecus').textContent = conducteurs.length;

    if (conducteurs.length === 0) {
        table.style.display = 'none'; empty.style.display = '';
        tbody.innerHTML = ''; return;
    }

    table.style.display = ''; empty.style.display = 'none';

    tbody.innerHTML = conducteurs.map(c => {
        const initials = ((c.prenom||'').charAt(0) + (c.nom||'').charAt(0)).toUpperCase();
        const dateEnr = c.date_enregistrement ? new Date(c.date_enregistrement).toLocaleDateString('fr-FR') : '-';
        return `<tr>
            <td><strong>${c.id}</strong></td>
            <td><div class="conducteur-info"><div class="conducteur-avatar green">${initials}</div><span class="conducteur-name">${(c.prenom||'')} ${(c.nom||'')}</span></div></td>
            <td>${c.numero_permis || '-'}</td>
            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;">${c.categorie_permis || '-'}</span></td>
            <td>${dateEnr}</td>
            <td><span class="badge badge-confirmed">Réceptionné ✓</span></td>
        </tr>`;
    }).join('');
}

// ── Sélection ──
function toggleSelectAll(checkbox) {
    document.querySelectorAll('.conducteur-check').forEach(cb => cb.checked = checkbox.checked);
    updateSelectionCount();
}

function updateSelectionCount() {
    const count = document.querySelectorAll('.conducteur-check:checked').length;
    document.getElementById('selectionCount').textContent = count + ' sélectionné(s)';
}

// ── Confirmations ──
function marquerConfirme(id) {
    const badge = document.getElementById('badge-' + id);
    const btn = document.getElementById('btn-' + id);
    if (badge) { badge.className = 'badge badge-confirmed'; badge.textContent = 'Réceptionné ✓'; }
    if (btn) { btn.disabled = true; btn.style.opacity = '0.4'; btn.innerHTML = '✓'; }
}

async function confirmerUn(id) {
    try {
        const r = await fetch(BASE_PATH + '/admin/receptionnaire/confirmer', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ conducteur_id: id })
        });
        const res = await r.json();
        res.success ? (marquerConfirme(id), showToast('Brevet confirmé')) : showToast(res.error || 'Erreur', 'error');
    } catch (e) { showToast('Erreur de connexion', 'error'); }
}

async function confirmerSelectionnes() {
    const ids = Array.from(document.querySelectorAll('.conducteur-check:checked')).map(cb => parseInt(cb.value));
    if (!ids.length) return showToast('Sélectionnez au moins un conducteur', 'error');
    try {
        const r = await fetch(BASE_PATH + '/admin/receptionnaire/confirmer-lot', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ conducteur_ids: ids })
        });
        const res = await r.json();
        if (res.success) { ids.forEach(id => marquerConfirme(id)); showToast(`${ids.length} brevet(s) confirmé(s)`); }
        else showToast(res.error || 'Erreur', 'error');
    } catch (e) { showToast('Erreur de connexion', 'error'); }
}

async function confirmerTous() {
    const ids = Array.from(document.querySelectorAll('.conducteur-check')).map(cb => parseInt(cb.value));
    if (!ids.length) return;
    try {
        const r = await fetch(BASE_PATH + '/admin/receptionnaire/confirmer-lot', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ conducteur_ids: ids })
        });
        const res = await r.json();
        if (res.success) { ids.forEach(id => marquerConfirme(id)); showToast(`${ids.length} brevet(s) confirmé(s)`); }
        else showToast(res.error || 'Erreur', 'error');
    } catch (e) { showToast('Erreur de connexion', 'error'); }
}
</script>
