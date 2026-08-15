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
        min-width: 900px;
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

    .vehicule-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .vehicule-plaque {
        font-weight: 600;
        color: #1A2744;
        font-family: monospace;
        font-size: 15px;
    }

    .vehicule-marque {
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
    }
</style>

<div class="vehicules-page">
    <div class="page-header">
        <h1 class="page-title">Gestion des Véhicules</h1>
        <button class="btn btn-primary" onclick="openModal()">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau véhicule
        </button>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <div class="search-box">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
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
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
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
                                    <button class="action-btn action-btn-edit" title="Modifier">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button class="action-btn action-btn-delete" title="Supprimer">
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

<script>
function openModal() {
    alert('Fonctionnalité de création de véhicule à implémenter');
}
</script>
