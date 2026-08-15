<?php
// Variables injectées par le contrôleur
$parkings = $parkings ?? [];

function getParkingTypeMeta($type) {
    $metas = [
        'couvert' => ['label' => 'Couvert', 'color' => '#3B82F6', 'bg' => '#EFF6FF'],
        'exterieur' => ['label' => 'Extérieur', 'color' => '#059669', 'bg' => '#ECFDF5'],
        'semi_couvert' => ['label' => 'Semi-couvert', 'color' => '#D97706', 'bg' => '#FFFBEB'],
    ];
    return $metas[$type] ?? ['label' => $type, 'color' => '#64748B', 'bg' => '#F1F5F9'];
}

function formatDate($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}
?>

<style>
    .parkings-page {
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
        background: #0369A1;
        color: white;
    }

    .btn-primary:hover {
        background: #075985;
    }

    .btn svg {
        width: 18px;
        height: 18px;
    }

    /* Cards Grid */
    .parkings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .parking-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .parking-header {
        padding: 20px;
        border-bottom: 1px solid #F1F5F9;
    }

    .parking-title {
        font-size: 18px;
        font-weight: 600;
        color: #1A2744;
        margin: 0 0 4px 0;
    }

    .parking-location {
        font-size: 13px;
        color: #64748B;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .parking-location svg {
        width: 14px;
        height: 14px;
    }

    .parking-body {
        padding: 20px;
    }

    .parking-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .parking-stat {
        text-align: center;
        padding: 12px;
        background: #F8FAFC;
        border-radius: 8px;
    }

    .parking-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1A2744;
    }

    .parking-stat-label {
        font-size: 12px;
        color: #64748B;
    }

    .parking-type {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .parking-contact {
        font-size: 13px;
        color: #64748B;
        margin-top: 12px;
    }

    .parking-contact span {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }

    .parking-contact svg {
        width: 14px;
        height: 14px;
    }

    .parking-footer {
        padding: 12px 20px;
        background: #F8FAFC;
        border-top: 1px solid #F1F5F9;
        display: flex;
        justify-content: flex-end;
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
        background: white;
        color: #3B82F6;
        border: 1px solid #E2E8F0;
    }

    .action-btn-edit:hover {
        background: #EFF6FF;
        border-color: #3B82F6;
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

        .parkings-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="parkings-page">
    <div class="page-header">
        <h1 class="page-title">Gestion des Parkings</h1>
        <button class="btn btn-primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouveau parking
        </button>
    </div>

    <?php if (empty($parkings)): ?>
        <div class="empty-state">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <h3>Aucun parking trouvé</h3>
            <p>Commencez par ajouter un nouveau parking</p>
        </div>
    <?php else: ?>
        <div class="parkings-grid">
            <?php foreach ($parkings as $parking): 
                $typeMeta = getParkingTypeMeta($parking['type_parking'] ?? 'exterieur');
            ?>
                <div class="parking-card">
                    <div class="parking-header">
                        <h3 class="parking-title"><?= htmlspecialchars($parking['nom'] ?? '') ?></h3>
                        <div class="parking-location">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <?= htmlspecialchars($parking['localisation'] ?? '-') ?>
                        </div>
                    </div>
                    <div class="parking-body">
                        <div class="parking-stats">
                            <div class="parking-stat">
                                <div class="parking-stat-value"><?= htmlspecialchars($parking['capacite'] ?? 0) ?></div>
                                <div class="parking-stat-label">Places</div>
                            </div>
                            <div class="parking-stat">
                                <div class="parking-stat-value"><?= htmlspecialchars($parking['capacite'] ?? 0) ?></div>
                                <div class="parking-stat-label">Disponibles</div>
                            </div>
                        </div>
                        <span class="parking-type" style="background: <?= $typeMeta['bg'] ?>; color: <?= $typeMeta['color'] ?>;">
                            <?= htmlspecialchars($typeMeta['label']) ?>
                        </span>
                        <div class="parking-contact">
                            <?php if (!empty($parking['responsable_nom'])): ?>
                                <span>
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <?= htmlspecialchars(($parking['responsable_prenom'] ?? '') . ' ' . ($parking['responsable_nom'] ?? '')) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($parking['telephone'])): ?>
                                <span>
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <?= htmlspecialchars($parking['telephone']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="parking-footer">
                        <button class="action-btn action-btn-edit" title="Modifier">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
