<?php
$conducteurs = $conducteurs ?? [];
$conducteursEnCours = $conducteursEnCours ?? [];
$conducteursImprimes = $conducteursImprimes ?? [];
$date_debut = $date_debut ?? '';
$date_fin = $date_fin ?? '';

function formatDateBrevet($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}
?>

<style>
    .imp-page { padding: 16px; }

    /* ── Toolbar compacte ── */
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
    .imp-toolbar input[type="date"] {
        padding: 7px 10px; border: 1px solid #E2E8F0; border-radius: 6px;
        font-size: 13px; outline: none; width: 140px;
    }
    .imp-toolbar input[type="date"]:focus { border-color: #3B82F6; }
    .imp-search {
        position: relative; flex: 1; min-width: 180px; max-width: 320px;
    }
    .imp-search svg {
        position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
        width: 16px; height: 16px; color: #94A3B8;
    }
    .imp-search input {
        width: 100%; padding: 7px 12px 7px 34px; border: 1px solid #E2E8F0;
        border-radius: 6px; font-size: 13px; outline: none;
    }
    .imp-search input:focus { border-color: #3B82F6; }
    .imp-badge-count {
        display: inline-flex; align-items: center; gap: 6px;
        background: #EFF6FF; color: #3B82F6; padding: 5px 12px;
        border-radius: 20px; font-size: 13px; font-weight: 600; white-space: nowrap;
    }

    /* ── Onglets ── */
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
        background: none; border: none; border-bottom: 3px solid transparent;
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

    /* ── Actions compactes ── */
    .imp-actions {
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
    .btn-warning { background: #D97706; color: white; }
    .btn-warning:hover { background: #B45309; }
    .btn-purple { background: #7C3AED; color: white; }
    .btn-purple:hover { background: #6D28D9; }
    .btn-outline { background: white; color: #374151; border: 1px solid #E2E8F0; }
    .btn-outline:hover { background: #F8FAFC; }
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
        background: linear-gradient(135deg, #3B82F6, #8B5CF6);
        color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 11px; flex-shrink: 0;
    }
    .conducteur-avatar.orange { background: linear-gradient(135deg, #D97706, #F59E0B); }
    .conducteur-name { font-weight: 500; color: #1A2744; font-size: 13px; }

    .badge {
        display: inline-flex; align-items: center; padding: 2px 8px;
        border-radius: 20px; font-size: 11px; font-weight: 500;
    }
    .badge-nouveau { background: #EFF6FF; color: #3B82F6; }
    .badge-encours { background: #FFFBEB; color: #D97706; }
    .badge-imprime { background: #ECFDF5; color: #059669; }
    .conducteur-avatar.green { background: linear-gradient(135deg, #059669, #10B981); }

    .empty-state {
        text-align: center; padding: 40px 20px; color: #64748B;
    }
    .empty-state svg { width: 48px; height: 48px; color: #CBD5E1; margin-bottom: 12px; }
    .empty-state h3 { margin: 0 0 4px 0; color: #334155; font-size: 15px; }
    .empty-state p { margin: 0; font-size: 13px; }

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
        .imp-toolbar { flex-direction: column; align-items: stretch; }
        .imp-toolbar-sep { display: none; }
        .imp-search { max-width: none; }
        .imp-actions { flex-direction: column; }
        .imp-tabs { flex-direction: column; }
    }
</style>

<div class="imp-page">

    <!-- Toolbar compacte -->
    <div class="imp-toolbar">
        <h1 class="imp-toolbar-title">Espace Imprimeur</h1>
        <div class="imp-toolbar-sep"></div>
        <input type="date" id="filterDateDebut" value="<?= htmlspecialchars($date_debut) ?>" title="Date début">
        <input type="date" id="filterDateFin" value="<?= htmlspecialchars($date_fin) ?>" title="Date fin">
        <button class="btn btn-primary" onclick="filtrerParDate()">
            <i data-lucide="funnel"></i>
            Filtrer
        </button>
        <div class="imp-toolbar-sep"></div>
        <div class="imp-search">
            <i data-lucide="search"></i>
            <input type="text" id="searchInput" placeholder="Rechercher nom, permis...">
        </div>
        <span class="imp-badge-count">
            <i data-lucide="printer" style="width:14px;height:14px;"></i>
            <span id="totalCount"><?= count($conducteurs) ?></span> à imprimer
        </span>
    </div>

    <!-- Onglets -->
    <div class="imp-tabs">
        <button class="imp-tab active" onclick="switchTab('nouveau')" id="tabNouveau">
            <i data-lucide="printer" style="width:16px;height:16px;"></i>
            Nouveaux brevets
            <span class="tab-count" id="countNouveau"><?= count($conducteurs) ?></span>
        </button>
        <button class="imp-tab" onclick="switchTab('encours')" id="tabEncours">
            <i data-lucide="clock" style="width:16px;height:16px;"></i>
            Brevets en cours
            <span class="tab-count" id="countEncours"><?= count($conducteursEnCours) ?></span>
        </button>
        <button class="imp-tab" onclick="switchTab('imprimes')" id="tabImprimes">
            <i data-lucide="circle-check-big" style="width:16px;height:16px;"></i>
            Déjà imprimés
            <span class="tab-count" id="countImprimes"><?= count($conducteursImprimes) ?></span>
        </button>
    </div>

    <!-- ═══ ONGLET 1 : Nouveaux brevets ═══ -->
    <div class="tab-panel active" id="panelNouveau">
        <!-- Actions de téléchargement -->
        <div class="imp-actions" id="actionsNouveau" style="<?= empty($conducteurs) ? 'display:none' : '' ?>">
            <button class="btn btn-success" onclick="document.getElementById('excelModal').style.display='flex'">
                <i data-lucide="file-down"></i>
                Excel
            </button>
            <a href="<?= BASE_PATH ?>/admin/imprimeur/download-photos?date_debut=<?= urlencode($date_debut) ?>&date_fin=<?= urlencode($date_fin) ?>" class="btn btn-primary">
                <i data-lucide="image"></i>
                Photos ZIP
            </a>
            <a href="<?= BASE_PATH ?>/admin/imprimeur/download-qrcodes?date_debut=<?= urlencode($date_debut) ?>&date_fin=<?= urlencode($date_fin) ?>" class="btn btn-purple">
                <i data-lucide="qr-code"></i>
                QR Codes ZIP
            </a>
            <form id="formMarquerImpression" method="POST" action="<?= BASE_PATH ?>/admin/imprimeur/marquer-impression" style="display:inline;">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="date_debut" value="<?= htmlspecialchars($date_debut) ?>">
                <input type="hidden" name="date_fin" value="<?= htmlspecialchars($date_fin) ?>">
                <button type="button" class="btn btn-warning" onclick="confirmerMarquerImpression(<?= count($conducteurs) ?>)">
                    <i data-lucide="printer"></i>
                    Marquer "En cours"
                </button>
            </form>
        </div>

        <div class="table-container">
            <div id="emptyNouveau" style="<?= empty($conducteurs) ? '' : 'display:none' ?>">
                <div class="empty-state">
                    <i data-lucide="printer"></i>
                    <h3>Aucun brevet à imprimer</h3>
                    <p><?= ($date_debut && $date_fin) ? 'Aucun conducteur trouvé pour cette période' : 'Sélectionnez une période pour afficher les conducteurs' ?></p>
                </div>
            </div>
            <table class="data-table" id="tableNouveau" style="<?= empty($conducteurs) ? 'display:none' : '' ?>">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Conducteur</th>
                        <th>Téléphone</th>
                        <th>N° Permis</th>
                        <th>Cat.</th>
                        <th>Enregistrement</th>
                        <th>Photo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="bodyNouveau">
                    <?php foreach ($conducteurs as $c):
                        $initials = strtoupper(substr($c['prenom'] ?? '', 0, 1) . substr($c['nom'] ?? '', 0, 1));
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['id']) ?></strong></td>
                            <td>
                                <div class="conducteur-info">
                                    <div class="conducteur-avatar"><?= htmlspecialchars($initials) ?></div>
                                    <span class="conducteur-name"><?= htmlspecialchars(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($c['telephone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['numero_permis'] ?? '-') ?></td>
                            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;"><?= htmlspecialchars($c['categorie_permis'] ?? '-') ?></span></td>
                            <td><?= formatDateBrevet($c['date_enregistrement'] ?? null) ?></td>
                            <td>
                                <?php if (!empty($c['photo_url'])): ?>
                                    <span class="badge" style="background:#ECFDF5;color:#059669;">✓</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#FEF2F2;color:#DC2626;">✗</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:4px;">
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/carte/<?= $c['id'] ?>" target="_blank" class="btn btn-sm btn-warning" title="Voir la carte brevet" style="padding:4px 8px;">
                                        <i data-lucide="id-card" style="width:12px;height:12px;"></i>
                                    </a>
                                    <?php if (!empty($c['photo_url'])): ?>
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/download-photo/<?= $c['id'] ?>" class="btn btn-sm btn-primary" title="Télécharger photo" style="padding:4px 8px;">
                                        <i data-lucide="image" style="width:12px;height:12px;"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/download-qrcode/<?= $c['id'] ?>" class="btn btn-sm btn-purple" title="Télécharger QR code" style="padding:4px 8px;">
                                        <i data-lucide="qr-code" style="width:12px;height:12px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══ ONGLET 2 : Brevets en cours d'impression ═══ -->
    <div class="tab-panel" id="panelEncours">
        <div class="table-container">
            <div id="emptyEncours" style="<?= empty($conducteursEnCours) ? '' : 'display:none' ?>">
                <div class="empty-state">
                    <i data-lucide="clock"></i>
                    <h3>Aucun brevet en cours</h3>
                    <p>Aucun brevet en cours d'impression pour le moment</p>
                </div>
            </div>
            <table class="data-table" id="tableEncours" style="<?= empty($conducteursEnCours) ? 'display:none' : '' ?>">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Conducteur</th>
                        <th>Téléphone</th>
                        <th>N° Permis</th>
                        <th>Cat.</th>
                        <th>Enregistrement</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="bodyEncours">
                    <?php foreach ($conducteursEnCours as $c):
                        $initials = strtoupper(substr($c['prenom'] ?? '', 0, 1) . substr($c['nom'] ?? '', 0, 1));
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['id']) ?></strong></td>
                            <td>
                                <div class="conducteur-info">
                                    <div class="conducteur-avatar orange"><?= htmlspecialchars($initials) ?></div>
                                    <span class="conducteur-name"><?= htmlspecialchars(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? '')) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($c['telephone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['numero_permis'] ?? '-') ?></td>
                            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;"><?= htmlspecialchars($c['categorie_permis'] ?? '-') ?></span></td>
                            <td><?= formatDateBrevet($c['date_enregistrement'] ?? null) ?></td>
                             <td><span class="badge badge-encours">En cours d'impression</span></td>
                            <td>
                                <div style="display:flex;gap:4px;">
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/carte/<?= $c['id'] ?>" target="_blank" class="btn btn-sm btn-warning" title="Voir la carte brevet" style="padding:4px 8px;">
                                        <i data-lucide="id-card" style="width:12px;height:12px;"></i>
                                    </a>
                                    <?php if (!empty($c['photo_url'])): ?>
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/download-photo/<?= $c['id'] ?>" class="btn btn-sm btn-primary" title="Télécharger photo" style="padding:4px 8px;">
                                        <i data-lucide="image" style="width:12px;height:12px;"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/download-qrcode/<?= $c['id'] ?>" class="btn btn-sm btn-purple" title="Télécharger QR code" style="padding:4px 8px;">
                                        <i data-lucide="qr-code" style="width:12px;height:12px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══ ONGLET 3 : Brevets déjà imprimés ═══ -->
    <div class="tab-panel" id="panelImprimes">
        <div class="table-container">
            <div id="emptyImprimes" style="<?= empty($conducteursImprimes) ? '' : 'display:none' ?>">
                <div class="empty-state">
                    <i data-lucide="circle-check-big"></i>
                    <h3>Aucun brevet imprimé</h3>
                    <p>Aucun brevet imprimé pour le moment</p>
                </div>
            </div>
            <table class="data-table" id="tableImprimes" style="<?= empty($conducteursImprimes) ? 'display:none' : '' ?>">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Conducteur</th>
                        <th>Téléphone</th>
                        <th>N° Permis</th>
                        <th>Cat.</th>
                        <th>Enregistrement</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="bodyImprimes">
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
                            <td><?= htmlspecialchars($c['telephone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['numero_permis'] ?? '-') ?></td>
                            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;"><?= htmlspecialchars($c['categorie_permis'] ?? '-') ?></span></td>
                            <td><?= formatDateBrevet($c['date_enregistrement'] ?? null) ?></td>
                            <td><span class="badge badge-imprime">Imprimé ✓</span></td>
                            <td>
                                <div style="display:flex;gap:4px;">
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/carte/<?= $c['id'] ?>" target="_blank" class="btn btn-sm btn-warning" title="Voir la carte brevet" style="padding:4px 8px;">
                                        <i data-lucide="id-card" style="width:12px;height:12px;"></i>
                                    </a>
                                    <?php if (!empty($c['photo_url'])): ?>
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/download-photo/<?= $c['id'] ?>" class="btn btn-sm btn-primary" title="Télécharger photo" style="padding:4px 8px;">
                                        <i data-lucide="image" style="width:12px;height:12px;"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= BASE_PATH ?>/admin/imprimeur/download-qrcode/<?= $c['id'] ?>" class="btn btn-sm btn-purple" title="Télécharger QR code" style="padding:4px 8px;">
                                        <i data-lucide="qr-code" style="width:12px;height:12px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Téléchargement Excel -->
<div id="excelModal" style="display:none; position:fixed; inset:0; z-index:1000; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div style="background:white; border-radius:10px; width:90%; max-width:480px;">
        <div style="padding:16px 20px; border-bottom:1px solid #E2E8F0; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <h2 style="margin:0; font-size:16px; font-weight:600; color:#1A2744;">Télécharger Excel</h2>
                <p style="margin:2px 0 0 0; font-size:12px; color:#64748B;">Chemins des dossiers photos et QR codes</p>
            </div>
            <button onclick="document.getElementById('excelModal').style.display='none'" style="background:none; border:none; cursor:pointer; padding:4px; color:#64748B;">
                <i data-lucide="x" style="width:18px;height:18px;"></i>
            </button>
        </div>
        <div style="padding:16px 20px;">
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">Chemin dossier Photos</label>
                <input type="text" id="cheminPhotos" placeholder="Ex: C:\Impression\Photos" style="width:100%; padding:8px 10px; border:1px solid #E2E8F0; border-radius:6px; font-size:13px; outline:none; box-sizing:border-box;" onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#E2E8F0'">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:4px;">Chemin dossier QR Codes</label>
                <input type="text" id="cheminQrcodes" placeholder="Ex: C:\Impression\QRCodes" style="width:100%; padding:8px 10px; border:1px solid #E2E8F0; border-radius:6px; font-size:13px; outline:none; box-sizing:border-box;" onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#E2E8F0'">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button onclick="document.getElementById('excelModal').style.display='none'" class="btn btn-outline">Annuler</button>
                <button onclick="downloadExcel()" class="btn btn-success">
                    <i data-lucide="file-down"></i>
                    Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toastContainer" class="toast-container"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_PATH = '<?= BASE_PATH ?>';
let currentTab = 'nouveau';

// ── Onglets ──
function switchTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.imp-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab' + capitalize(tab)).classList.add('active');
    document.getElementById('panel' + capitalize(tab)).classList.add('active');
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// ── Recherche instantanée ──
document.getElementById('searchInput').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    const bodyMap = { nouveau: 'bodyNouveau', encours: 'bodyEncours', imprimes: 'bodyImprimes' };
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
        // Charger les trois onglets
        params.set('statut', 'nouveau');
        const resp1 = await fetch(BASE_PATH + '/admin/api/imprimeur?' + params.toString());
        const nouveaux = await resp1.json();

        params.set('statut', 'en_cours_impression');
        const resp2 = await fetch(BASE_PATH + '/admin/api/imprimeur?' + params.toString());
        const encours = await resp2.json();

        params.set('statut', 'imprime');
        const resp3 = await fetch(BASE_PATH + '/admin/api/imprimeur?' + params.toString());
        const imprimes = await resp3.json();

        renderNouveaux(nouveaux, dateDebut, dateFin);
        renderEncours(encours);
        renderImprimes(imprimes);
    } catch (e) {
        showToast('Erreur lors du filtrage', 'error');
    }
}

function renderNouveaux(conducteurs, dateDebut, dateFin) {
    const tbody = document.getElementById('bodyNouveau');
    const table = document.getElementById('tableNouveau');
    const empty = document.getElementById('emptyNouveau');
    const actions = document.getElementById('actionsNouveau');

    document.getElementById('totalCount').textContent = conducteurs.length;
    document.getElementById('countNouveau').textContent = conducteurs.length;

    if (conducteurs.length === 0) {
        table.style.display = 'none'; empty.style.display = ''; actions.style.display = 'none';
        tbody.innerHTML = ''; return;
    }

    table.style.display = ''; empty.style.display = 'none'; actions.style.display = '';

    // Update download links
    const dlParams = `date_debut=${encodeURIComponent(dateDebut||'')}&date_fin=${encodeURIComponent(dateFin||'')}`;
    document.querySelectorAll('#actionsNouveau a').forEach(a => {
        const href = a.getAttribute('href').split('?')[0];
        a.setAttribute('href', href + '?' + dlParams);
    });
    const hiddenDebut = document.querySelector('#formMarquerImpression input[name="date_debut"]');
    const hiddenFin = document.querySelector('#formMarquerImpression input[name="date_fin"]');
    if (hiddenDebut) hiddenDebut.value = dateDebut || '';
    if (hiddenFin) hiddenFin.value = dateFin || '';

    tbody.innerHTML = conducteurs.map(c => {
        const initials = ((c.prenom||'').charAt(0) + (c.nom||'').charAt(0)).toUpperCase();
        const dateEnr = c.date_enregistrement ? new Date(c.date_enregistrement).toLocaleDateString('fr-FR') : '-';
        const hasPhoto = c.photo_url ? '<span class="badge" style="background:#ECFDF5;color:#059669;">✓</span>' : '<span class="badge" style="background:#FEF2F2;color:#DC2626;">✗</span>';
        const carteIcon = `<i data-lucide="id-card" style="width:12px;height:12px;"></i>`;
        return `<tr>
            <td><strong>${c.id}</strong></td>
            <td><div class="conducteur-info"><div class="conducteur-avatar">${initials}</div><span class="conducteur-name">${(c.prenom||'')} ${(c.nom||'')}</span></div></td>
            <td>${c.telephone || '-'}</td>
            <td>${c.numero_permis || '-'}</td>
            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;">${c.categorie_permis || '-'}</span></td>
            <td>${dateEnr}</td>
            <td>${hasPhoto}</td>
            <td><a href="${BASE_PATH}/admin/imprimeur/carte/${c.id}" target="_blank" class="btn btn-sm btn-warning" title="Carte brevet" style="padding:4px 8px;">${carteIcon}</a></td>
        </tr>`;
    }).join('');
    if (window.lucide) lucide.createIcons();
}

function renderEncours(conducteurs) {
    const tbody = document.getElementById('bodyEncours');
    const table = document.getElementById('tableEncours');
    const empty = document.getElementById('emptyEncours');

    document.getElementById('countEncours').textContent = conducteurs.length;

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
            <td><div class="conducteur-info"><div class="conducteur-avatar orange">${initials}</div><span class="conducteur-name">${(c.prenom||'')} ${(c.nom||'')}</span></div></td>
            <td>${c.telephone || '-'}</td>
            <td>${c.numero_permis || '-'}</td>
            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;">${c.categorie_permis || '-'}</span></td>
            <td>${dateEnr}</td>
            <td><span class="badge badge-encours">En cours d'impression</span></td>
        </tr>`;
    }).join('');
    if (window.lucide) lucide.createIcons();
}

function renderImprimes(conducteurs) {
    const tbody = document.getElementById('bodyImprimes');
    const table = document.getElementById('tableImprimes');
    const empty = document.getElementById('emptyImprimes');

    document.getElementById('countImprimes').textContent = conducteurs.length;

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
            <td>${c.telephone || '-'}</td>
            <td>${c.numero_permis || '-'}</td>
            <td><span class="badge" style="background:#3B82F620;color:#3B82F6;font-weight:600;">${c.categorie_permis || '-'}</span></td>
            <td>${dateEnr}</td>
            <td><span class="badge badge-imprime">Imprimé ✓</span></td>
        </tr>`;
    }).join('');
    if (window.lucide) lucide.createIcons();
}

// ── Excel ──
function downloadExcel() {
    const cheminPhotos = document.getElementById('cheminPhotos').value;
    const cheminQrcodes = document.getElementById('cheminQrcodes').value;
    const params = new URLSearchParams({
        date_debut: document.getElementById('filterDateDebut').value || '<?= htmlspecialchars($date_debut) ?>',
        date_fin: document.getElementById('filterDateFin').value || '<?= htmlspecialchars($date_fin) ?>',
        chemin_photos: cheminPhotos,
        chemin_qrcodes: cheminQrcodes
    });
    window.location.href = BASE_PATH + '/admin/imprimeur/download-excel?' + params.toString();
    document.getElementById('excelModal').style.display = 'none';
}

document.getElementById('excelModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

// ── Marquer en cours ──
function confirmerMarquerImpression(total) {
    Swal.fire({
        title: 'Confirmer le changement',
        html: `<p style="font-size:14px;">Marquer <strong>${total} conducteur(s)</strong> en <span style="color:#D97706;font-weight:600;">cours d'impression</span> ?</p>
               <p style="font-size:12px;color:#64748B;margin-top:6px;">Les brevets ne seront plus disponibles au téléchargement.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D97706',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Oui, marquer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formMarquerImpression').submit();
        }
    });
}

// ── Toast ──
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
});
</script>
