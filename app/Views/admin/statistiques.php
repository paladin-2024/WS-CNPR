<?php
// Variables injectées par le contrôleur
$totalConducteurs = $totalConducteurs ?? 0;
$totalVehicules = $totalVehicules ?? 0;
$totalCartes = $totalCartes ?? 0;
$totalPaiements = $totalPaiements ?? 0;
$totalUtilisateurs = $totalUtilisateurs ?? 0;
$totalParkings = $totalParkings ?? 0;
$brevetsNouveau = $brevetsNouveau ?? 0;
$brevetsEnCours = $brevetsEnCours ?? 0;
$brevetsImprimes = $brevetsImprimes ?? 0;
$conducteursByStatus = $conducteursByStatus ?? [];
$brevetsByStatus = $brevetsByStatus ?? [];
$vehiculesByType = $vehiculesByType ?? [];
$paiementsByMonth = $paiementsByMonth ?? [];

$totalBrevets = $brevetsNouveau + $brevetsEnCours + $brevetsImprimes;

function formatNumber($num) {
    return number_format($num, 0, ',', ' ');
}

function formatCurrency($amount) {
    return number_format($amount, 0, ',', ' ') . ' CDF';
}

function getRandomColor($index) {
    $colors = ['#3B82F6', '#8B5CF6', '#059669', '#D97706', '#EF4444', '#EC4899', '#14B8A6', '#F97316'];
    return $colors[$index % count($colors)];
}

$brevetStatusLabels = [
    'nouveau' => 'Nouveaux',
    'en_cours_impression' => 'En cours',
    'imprime' => 'Imprimés',
];
$brevetStatusColors = [
    'nouveau' => '#6366F1',
    'en_cours_impression' => '#F59E0B',
    'imprime' => '#10B981',
];
?>

<style>
    .statistiques-page { padding: 24px; }
    .page-header { margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 700; color: #1A2744; margin: 0 0 4px 0; }
    .page-subtitle { font-size: 14px; color: #64748B; margin: 0; }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        text-align: center;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }

    .stat-icon svg { width: 24px; height: 24px; }
    .stat-value { font-size: 28px; font-weight: 700; color: #1A2744; margin-bottom: 4px; }
    .stat-label { font-size: 13px; color: #64748B; }

    /* Section title */
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1A2744;
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title svg { width: 20px; height: 20px; }

    /* Brevet Stats Row */
    .brevet-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .brevet-stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 16px;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid transparent;
    }

    .brevet-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .brevet-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .brevet-stat-icon svg { width: 26px; height: 26px; }

    .brevet-stat-info { flex: 1; }
    .brevet-stat-label { font-size: 13px; color: #64748B; margin-bottom: 4px; }
    .brevet-stat-value { font-size: 28px; font-weight: 700; color: #1A2744; }

    /* Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .chart-title { font-size: 16px; font-weight: 600; color: #1A2744; margin: 0 0 20px 0; }

    /* Bar Chart */
    .bar-chart { display: flex; flex-direction: column; gap: 12px; }

    .bar-item { display: flex; align-items: center; gap: 12px; }

    .bar-label {
        width: 100px;
        font-size: 13px;
        color: #334155;
        flex-shrink: 0;
    }

    .bar-track {
        flex: 1;
        height: 32px;
        background: #F1F5F9;
        border-radius: 6px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 12px;
        min-width: 40px;
        transition: width 0.6s ease;
    }

    .bar-value { font-size: 12px; font-weight: 600; color: white; }

    /* Donut Chart */
    .donut-chart-container { display: flex; align-items: center; gap: 24px; }

    .donut-chart {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        position: relative;
    }

    .donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        background: white;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .donut-center-value { font-size: 24px; font-weight: 700; color: #1A2744; }
    .donut-center-label { font-size: 12px; color: #64748B; }
    .donut-legend { display: flex; flex-direction: column; gap: 10px; }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #334155;
    }

    .legend-dot { width: 12px; height: 12px; border-radius: 3px; }
    .legend-value { margin-left: auto; font-weight: 600; }

    /* Brevet Progress */
    .brevet-progress-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .progress-bar-full {
        width: 100%;
        height: 28px;
        background: #F1F5F9;
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        margin-bottom: 16px;
    }

    .progress-segment {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
        color: white;
        transition: width 0.6s ease;
    }

    .progress-legend {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }

    .progress-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #334155;
    }

    .progress-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
    }

    .progress-legend-count { font-weight: 700; }

    /* Table */
    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .table-title {
        font-size: 16px;
        font-weight: 600;
        color: #1A2744;
        margin: 0 0 16px 0;
        padding: 20px 20px 0 20px;
    }

    .data-table { width: 100%; border-collapse: collapse; }

    .data-table th {
        text-align: left;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
    }

    .data-table td {
        padding: 12px 16px;
        font-size: 14px;
        color: #334155;
        border-bottom: 1px solid #F1F5F9;
    }

    .data-table tr:last-child td { border-bottom: none; }

    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 900px) {
        .charts-grid { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .brevet-stats-row { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .stats-grid { grid-template-columns: 1fr; }
        .donut-chart-container { flex-direction: column; }
        .progress-legend { flex-direction: column; gap: 8px; }
    }
</style>

<div class="statistiques-page">
    <div class="page-header">
        <h1 class="page-title">Statistiques</h1>
        <p class="page-subtitle">Vue d'ensemble des données du ministère des transports</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #EFF6FF; color: #3B82F6;">
                <i data-lucide="circle-check-big"></i>
            </div>
            <div class="stat-value"><?= formatNumber($totalConducteurs) ?></div>
            <div class="stat-label">Conducteurs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #F5F3FF; color: #8B5CF6;">
                <i data-lucide="car"></i>
            </div>
            <div class="stat-value"><?= formatNumber($totalVehicules) ?></div>
            <div class="stat-label">Véhicules</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #ECFDF5; color: #059669;">
                <i data-lucide="credit-card"></i>
            </div>
            <div class="stat-value"><?= formatNumber($totalCartes) ?></div>
            <div class="stat-label">Cartes Pro.</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #FFFBEB; color: #D97706;">
                <i data-lucide="dollar-sign"></i>
            </div>
            <div class="stat-value"><?= formatNumber($totalPaiements) ?></div>
            <div class="stat-label">Taxes (CDF)</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #F0F9FF; color: #0369A1;">
                <i data-lucide="users"></i>
            </div>
            <div class="stat-value"><?= formatNumber($totalUtilisateurs) ?></div>
            <div class="stat-label">Utilisateurs</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEF2F2; color: #EF4444;">
                <i data-lucide="building-2"></i>
            </div>
            <div class="stat-value"><?= formatNumber($totalParkings) ?></div>
            <div class="stat-label">Parkings</div>
        </div>
    </div>

    <!-- Brevets Section -->
    <div class="section-title" style="color: #6366F1;">
        <i data-lucide="file-text"></i>
        Suivi des Brevets
    </div>

    <div class="brevet-stats-row">
        <a href="<?= BASE_PATH ?>/admin/imprimeur" class="brevet-stat-card" style="border-left-color: #6366F1;">
            <div class="brevet-stat-icon" style="background: #EEF2FF; color: #6366F1;">
                <i data-lucide="file-plus"></i>
            </div>
            <div class="brevet-stat-info">
                <div class="brevet-stat-label">Nouveaux brevets</div>
                <div class="brevet-stat-value"><?= formatNumber($brevetsNouveau) ?></div>
            </div>
        </a>
        <a href="<?= BASE_PATH ?>/admin/imprimeur" class="brevet-stat-card" style="border-left-color: #F59E0B;">
            <div class="brevet-stat-icon" style="background: #FFFBEB; color: #F59E0B;">
                <i data-lucide="printer"></i>
            </div>
            <div class="brevet-stat-info">
                <div class="brevet-stat-label">En cours d'impression</div>
                <div class="brevet-stat-value"><?= formatNumber($brevetsEnCours) ?></div>
            </div>
        </a>
        <a href="<?= BASE_PATH ?>/admin/receptionnaire" class="brevet-stat-card" style="border-left-color: #10B981;">
            <div class="brevet-stat-icon" style="background: #ECFDF5; color: #10B981;">
                <i data-lucide="circle-check-big"></i>
            </div>
            <div class="brevet-stat-info">
                <div class="brevet-stat-label">Brevets imprimés</div>
                <div class="brevet-stat-value"><?= formatNumber($brevetsImprimes) ?></div>
            </div>
        </a>
    </div>

    <!-- Brevet Progress Bar -->
    <?php if ($totalBrevets > 0): ?>
        <div class="brevet-progress-card" style="margin-bottom: 24px;">
            <h3 class="chart-title">Progression globale des brevets</h3>
            <div class="progress-bar-full">
                <?php
                $pctNouveau = round($brevetsNouveau / $totalBrevets * 100);
                $pctEnCours = round($brevetsEnCours / $totalBrevets * 100);
                $pctImprimes = 100 - $pctNouveau - $pctEnCours;
                ?>
                <?php if ($pctNouveau > 0): ?>
                    <div class="progress-segment" style="width: <?= $pctNouveau ?>%; background: #6366F1;"><?= $pctNouveau ?>%</div>
                <?php endif; ?>
                <?php if ($pctEnCours > 0): ?>
                    <div class="progress-segment" style="width: <?= $pctEnCours ?>%; background: #F59E0B;"><?= $pctEnCours ?>%</div>
                <?php endif; ?>
                <?php if ($pctImprimes > 0): ?>
                    <div class="progress-segment" style="width: <?= $pctImprimes ?>%; background: #10B981;"><?= $pctImprimes ?>%</div>
                <?php endif; ?>
            </div>
            <div class="progress-legend">
                <div class="progress-legend-item">
                    <div class="progress-legend-dot" style="background: #6366F1;"></div>
                    Nouveaux <span class="progress-legend-count"><?= formatNumber($brevetsNouveau) ?></span>
                </div>
                <div class="progress-legend-item">
                    <div class="progress-legend-dot" style="background: #F59E0B;"></div>
                    En cours <span class="progress-legend-count"><?= formatNumber($brevetsEnCours) ?></span>
                </div>
                <div class="progress-legend-item">
                    <div class="progress-legend-dot" style="background: #10B981;"></div>
                    Imprimés <span class="progress-legend-count"><?= formatNumber($brevetsImprimes) ?></span>
                </div>
                <div class="progress-legend-item" style="margin-left: auto; font-weight: 600; color: #1A2744;">
                    Total: <?= formatNumber($totalBrevets) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Charts Grid -->
    <div class="charts-grid">
        <!-- Véhicules par type -->
        <div class="chart-card">
            <h3 class="chart-title">Véhicules par type</h3>
            <div class="bar-chart">
                <?php 
                $maxCount = 0;
                foreach ($vehiculesByType as $v) {
                    if (($v['total'] ?? 0) > $maxCount) $maxCount = $v['total'];
                }
                $i = 0;
                foreach ($vehiculesByType as $vehicule): 
                    $percent = $maxCount > 0 ? round(($vehicule['total'] ?? 0) / $maxCount * 100) : 0;
                    $color = getRandomColor($i);
                ?>
                    <div class="bar-item">
                        <div class="bar-label"><?= htmlspecialchars(ucfirst($vehicule['type_vehicule'] ?? '-')) ?></div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: <?= $percent ?>%; background: <?= $color ?>;">
                                <span class="bar-value"><?= formatNumber($vehicule['total'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                <?php 
                $i++;
                endforeach; 
                ?>
                <?php if (empty($vehiculesByType)): ?>
                    <p style="color: #64748B; text-align: center; padding: 20px;">Aucune donnée disponible</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Conducteurs par statut -->
        <div class="chart-card">
            <h3 class="chart-title">Conducteurs par statut</h3>
            <?php if (!empty($conducteursByStatus)): ?>
                <?php
                $total = 0;
                foreach ($conducteursByStatus as $c) { $total += $c['total'] ?? 0; }
                $cumulDeg = 0;
                $gradientParts = [];
                $i = 0;
                foreach ($conducteursByStatus as $conducteur) {
                    $color = getRandomColor($i);
                    $pct = $total > 0 ? ($conducteur['total'] ?? 0) / $total * 360 : 0;
                    $start = $cumulDeg;
                    $cumulDeg += $pct;
                    $gradientParts[] = "{$color} {$start}deg {$cumulDeg}deg";
                    $i++;
                }
                $gradient = implode(', ', $gradientParts);
                ?>
                <div class="donut-chart-container">
                    <div class="donut-chart" style="background: conic-gradient(<?= $gradient ?>);">
                        <div class="donut-center">
                            <div class="donut-center-value"><?= formatNumber($total) ?></div>
                            <div class="donut-center-label">Total</div>
                        </div>
                    </div>
                    <div class="donut-legend">
                        <?php 
                        $i = 0;
                        foreach ($conducteursByStatus as $conducteur): 
                            $color = getRandomColor($i);
                        ?>
                            <div class="legend-item">
                                <div class="legend-dot" style="background: <?= $color ?>;"></div>
                                <span><?= htmlspecialchars(ucfirst($conducteur['statut'] ?? '-')) ?></span>
                                <span class="legend-value"><?= formatNumber($conducteur['total'] ?? 0) ?></span>
                            </div>
                        <?php 
                        $i++;
                        endforeach; 
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <p style="color: #64748B; text-align: center; padding: 20px;">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <!-- Brevets par statut -->
        <div class="chart-card">
            <h3 class="chart-title">Brevets par statut</h3>
            <?php if (!empty($brevetsByStatus)): ?>
                <?php
                $cumulDeg = 0;
                $gradientParts = [];
                foreach ($brevetsByStatus as $b) {
                    $statut = $b['statut_brevet'] ?? 'nouveau';
                    $color = $brevetStatusColors[$statut] ?? '#6366F1';
                    $pct = $totalBrevets > 0 ? ($b['total'] ?? 0) / $totalBrevets * 360 : 0;
                    $start = $cumulDeg;
                    $cumulDeg += $pct;
                    $gradientParts[] = "{$color} {$start}deg {$cumulDeg}deg";
                }
                $gradient = implode(', ', $gradientParts);
                ?>
                <div class="donut-chart-container">
                    <div class="donut-chart" style="background: conic-gradient(<?= $gradient ?>);">
                        <div class="donut-center">
                            <div class="donut-center-value"><?= formatNumber($totalBrevets) ?></div>
                            <div class="donut-center-label">Total</div>
                        </div>
                    </div>
                    <div class="donut-legend">
                        <?php foreach ($brevetsByStatus as $b):
                            $statut = $b['statut_brevet'] ?? 'nouveau';
                            $color = $brevetStatusColors[$statut] ?? '#6366F1';
                            $label = $brevetStatusLabels[$statut] ?? $statut;
                        ?>
                            <div class="legend-item">
                                <div class="legend-dot" style="background: <?= $color ?>;"></div>
                                <span><?= htmlspecialchars($label) ?></span>
                                <span class="legend-value"><?= formatNumber($b['total'] ?? 0) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p style="color: #64748B; text-align: center; padding: 20px;">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <!-- Paiements par mois (chart) -->
        <?php if (!empty($paiementsByMonth)): ?>
            <div class="chart-card">
                <h3 class="chart-title">Paiements par mois</h3>
                <div class="bar-chart">
                    <?php
                    $maxMontant = 0;
                    foreach ($paiementsByMonth as $p) {
                        if (($p['total'] ?? 0) > $maxMontant) $maxMontant = $p['total'];
                    }
                    $i = 0;
                    foreach ($paiementsByMonth as $paiement):
                        $percent = $maxMontant > 0 ? round(($paiement['total'] ?? 0) / $maxMontant * 100) : 0;
                    ?>
                        <div class="bar-item">
                            <div class="bar-label"><?= htmlspecialchars($paiement['mois'] ?? '-') ?></div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: <?= $percent ?>%; background: #D97706;">
                                    <span class="bar-value"><?= formatNumber($paiement['total'] ?? 0) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php
                    $i++;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
