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

// Fixed color per vehicle type (not per array position, which depends on the
// unspecified GROUP BY result order) so a given type reads the same color here
// and on the main dashboard's "Répartition des véhicules" chart.
$vehicleTypeColors = [
    'taxi' => '#3B82F6',
    'bus' => '#8B5CF6',
    'camion' => '#D97706',
    'moto' => '#10B981',
    'voiture' => '#6366F1',
];

// Status-shaped data (actif/suspendu/expire) gets a fixed semantic color rather
// than an arbitrary rotation — active reads as "good", suspended as "warning",
// expired as "critical", consistent with how the rest of the admin UI uses color.
$conducteurStatusColors = [
    'actif' => '#059669',
    'suspendu' => '#D97706',
    'expire' => '#EF4444',
];

$moisLabelMap = ['01' => 'Jan', '02' => 'Fév', '03' => 'Mar', '04' => 'Avr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Aoû', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Déc'];
function formatMoisLabel($mois, $map) {
    $parts = explode('-', $mois ?? '');
    return (isset($parts[1], $map[$parts[1]])) ? $map[$parts[1]] . ' ' . $parts[0] : ($mois ?? '');
}

// --- Chart.js data, built once here and consumed by the script block at the
// bottom of this view. Labels are plain strings (never raw HTML), so this is
// safe to json_encode and parse with JSON.parse() client-side.
$chartVehicules = ['labels' => [], 'values' => [], 'colors' => []];
foreach ($vehiculesByType as $v) {
    $type = $v['type_vehicule'] ?? '';
    $chartVehicules['labels'][] = ucfirst($type ?: '-');
    $chartVehicules['values'][] = (int)($v['total'] ?? 0);
    $chartVehicules['colors'][] = $vehicleTypeColors[$type] ?? '#3B82F6';
}

$chartConducteurs = ['labels' => [], 'values' => [], 'colors' => []];
foreach ($conducteursByStatus as $c) {
    $statut = $c['statut'] ?? '';
    $chartConducteurs['labels'][] = ucfirst($statut ?: '-');
    $chartConducteurs['values'][] = (int)($c['total'] ?? 0);
    $chartConducteurs['colors'][] = $conducteurStatusColors[$statut] ?? '#8B5CF6';
}

$chartBrevets = ['labels' => [], 'values' => [], 'colors' => []];
foreach ($brevetsByStatus as $b) {
    $statut = $b['statut_brevet'] ?? 'nouveau';
    $chartBrevets['labels'][] = $brevetStatusLabels[$statut] ?? $statut;
    $chartBrevets['values'][] = (int)($b['total'] ?? 0);
    $chartBrevets['colors'][] = $brevetStatusColors[$statut] ?? '#6366F1';
}

// Query returns most-recent-month-first (DESC LIMIT 12); a trend line reads
// left-to-right chronologically, so reverse just for the chart.
$paiementsChrono = array_reverse($paiementsByMonth);
$chartPaiements = ['labels' => [], 'values' => []];
foreach ($paiementsChrono as $p) {
    $chartPaiements['labels'][] = formatMoisLabel($p['mois'] ?? '', $moisLabelMap);
    $chartPaiements['values'][] = (float)($p['total'] ?? 0);
}

$statistiquesChartData = [
    'vehicules' => $chartVehicules,
    'conducteurs' => $chartConducteurs,
    'brevets' => $chartBrevets,
    'paiements' => $chartPaiements,
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

    /* Chart.js canvas container */
    .chart-canvas-wrap {
        position: relative;
        height: 240px;
    }

    .chart-canvas-wrap.chart-canvas-wrap--donut {
        height: 200px;
        width: 200px;
        margin: 0 auto;
    }

    .chart-empty-state {
        color: #64748B;
        text-align: center;
        padding: 20px;
        font-size: 14px;
    }

    /* Donut Chart layout (canvas + legend) */
    .donut-chart-container { display: flex; align-items: center; gap: 24px; }

    .donut-legend { display: flex; flex-direction: column; gap: 10px; flex: 1; }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #334155;
    }

    .legend-dot { width: 12px; height: 12px; border-radius: 3px; flex-shrink: 0; }
    .legend-value { margin-left: auto; font-weight: 600; }

    /* Value legend used under bar/line charts */
    .chart-value-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #F1F5F9;
        max-height: 150px;
        overflow-y: auto;
    }

    .chart-value-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #334155;
    }

    .chart-value-legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
    .chart-value-legend-count { font-weight: 700; color: #1A2744; }

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
            <?php if (!empty($vehiculesByType)): ?>
                <div class="chart-canvas-wrap">
                    <canvas id="vehiculesByTypeChart" role="img" aria-label="Véhicules par type"></canvas>
                </div>
                <div class="chart-value-legend">
                    <?php foreach ($vehiculesByType as $i => $vehicule): ?>
                        <span class="chart-value-legend-item">
                            <span class="chart-value-legend-dot" style="background: <?= htmlspecialchars($chartVehicules['colors'][$i] ?? '#3B82F6') ?>;"></span>
                            <?= htmlspecialchars(ucfirst($vehicule['type_vehicule'] ?? '-')) ?>
                            <span class="chart-value-legend-count"><?= formatNumber($vehicule['total'] ?? 0) ?></span>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="chart-empty-state">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <!-- Conducteurs par statut -->
        <div class="chart-card">
            <h3 class="chart-title">Conducteurs par statut</h3>
            <?php if (!empty($conducteursByStatus)): ?>
                <?php
                $total = 0;
                foreach ($conducteursByStatus as $c) { $total += $c['total'] ?? 0; }
                ?>
                <div class="donut-chart-container">
                    <div class="chart-canvas-wrap chart-canvas-wrap--donut">
                        <canvas id="conducteursByStatusChart" role="img" aria-label="Conducteurs par statut"></canvas>
                    </div>
                    <div class="donut-legend">
                        <?php foreach ($conducteursByStatus as $i => $conducteur): ?>
                            <div class="legend-item">
                                <div class="legend-dot" style="background: <?= htmlspecialchars($chartConducteurs['colors'][$i] ?? '#8B5CF6') ?>;"></div>
                                <span><?= htmlspecialchars(ucfirst($conducteur['statut'] ?? '-')) ?></span>
                                <span class="legend-value"><?= formatNumber($conducteur['total'] ?? 0) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="legend-item" style="border-top: 1px solid #F1F5F9; padding-top: 10px; margin-top: 2px;">
                            <span style="font-weight: 600; color: #1A2744;">Total</span>
                            <span class="legend-value"><?= formatNumber($total) ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="chart-empty-state">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <!-- Brevets par statut -->
        <div class="chart-card">
            <h3 class="chart-title">Brevets par statut</h3>
            <?php if (!empty($brevetsByStatus)): ?>
                <div class="donut-chart-container">
                    <div class="chart-canvas-wrap chart-canvas-wrap--donut">
                        <canvas id="brevetsByStatusChart" role="img" aria-label="Brevets par statut"></canvas>
                    </div>
                    <div class="donut-legend">
                        <?php foreach ($brevetsByStatus as $i => $b):
                            $statut = $b['statut_brevet'] ?? 'nouveau';
                            $label = $brevetStatusLabels[$statut] ?? $statut;
                        ?>
                            <div class="legend-item">
                                <div class="legend-dot" style="background: <?= htmlspecialchars($chartBrevets['colors'][$i] ?? '#6366F1') ?>;"></div>
                                <span><?= htmlspecialchars($label) ?></span>
                                <span class="legend-value"><?= formatNumber($b['total'] ?? 0) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="legend-item" style="border-top: 1px solid #F1F5F9; padding-top: 10px; margin-top: 2px;">
                            <span style="font-weight: 600; color: #1A2744;">Total</span>
                            <span class="legend-value"><?= formatNumber($totalBrevets) ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="chart-empty-state">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>

        <!-- Paiements par mois (chart) -->
        <div class="chart-card">
            <h3 class="chart-title">Paiements par mois</h3>
            <?php if (!empty($paiementsByMonth)): ?>
                <div class="chart-canvas-wrap">
                    <canvas id="paiementsByMonthChart" role="img" aria-label="Paiements par mois"></canvas>
                </div>
                <div class="chart-value-legend">
                    <?php foreach ($paiementsChrono as $i => $paiement): ?>
                        <span class="chart-value-legend-item">
                            <span class="chart-value-legend-dot" style="background: #D97706;"></span>
                            <?= htmlspecialchars($chartPaiements['labels'][$i] ?? '-') ?>
                            <span class="chart-value-legend-count"><?= formatCurrency($paiement['total'] ?? 0) ?></span>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="chart-empty-state">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="application/json" id="statistiques-chart-data"><?= json_encode($statistiquesChartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (typeof Chart === 'undefined') { return; }

    var dataEl = document.getElementById('statistiques-chart-data');
    var chartData = {};
    try {
        chartData = JSON.parse((dataEl && dataEl.textContent) || '{}');
    } catch (e) {
        chartData = {};
    }

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var animation = reduceMotion ? false : { duration: 700, easing: 'easeOutQuart' };

    // Small plugin to draw a "total" label in the center of a doughnut, mirroring
    // the look of the previous CSS-donut center label.
    var centerTextPlugin = {
        id: 'centerText',
        afterDraw: function (chart) {
            var opts = chart.config.options.plugins && chart.config.options.plugins.centerText;
            if (!opts || !opts.text) { return; }
            var ctx = chart.ctx;
            var area = chart.chartArea;
            var cx = (area.left + area.right) / 2;
            var cy = (area.top + area.bottom) / 2;
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = '700 20px sans-serif';
            ctx.fillStyle = '#1A2744';
            ctx.fillText(opts.text, cx, cy - 9);
            ctx.font = '12px sans-serif';
            ctx.fillStyle = '#64748B';
            ctx.fillText(opts.subtext || '', cx, cy + 12);
            ctx.restore();
        }
    };

    var veh = chartData.vehicules || { labels: [], values: [], colors: [] };
    var vehCanvas = document.getElementById('vehiculesByTypeChart');
    if (vehCanvas && veh.labels && veh.labels.length) {
        new Chart(vehCanvas, {
            type: 'bar',
            data: {
                labels: veh.labels,
                datasets: [{
                    label: 'Véhicules',
                    data: veh.values,
                    backgroundColor: veh.colors,
                    borderRadius: 4,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animation,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.label + ': ' + ctx.parsed.y.toLocaleString('fr-FR');
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } }
                }
            }
        });
    }

    var cond = chartData.conducteurs || { labels: [], values: [], colors: [] };
    var condCanvas = document.getElementById('conducteursByStatusChart');
    if (condCanvas && cond.labels && cond.labels.length) {
        var condTotal = cond.values.reduce(function (a, b) { return a + b; }, 0);
        new Chart(condCanvas, {
            type: 'doughnut',
            data: {
                labels: cond.labels,
                datasets: [{
                    data: cond.values,
                    backgroundColor: cond.colors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                animation: animation,
                plugins: {
                    legend: { display: false },
                    centerText: { text: condTotal.toLocaleString('fr-FR'), subtext: 'Total' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.label + ': ' + ctx.parsed.toLocaleString('fr-FR');
                            }
                        }
                    }
                }
            },
            plugins: [centerTextPlugin]
        });
    }

    var brev = chartData.brevets || { labels: [], values: [], colors: [] };
    var brevCanvas = document.getElementById('brevetsByStatusChart');
    if (brevCanvas && brev.labels && brev.labels.length) {
        var brevTotal = brev.values.reduce(function (a, b) { return a + b; }, 0);
        new Chart(brevCanvas, {
            type: 'doughnut',
            data: {
                labels: brev.labels,
                datasets: [{
                    data: brev.values,
                    backgroundColor: brev.colors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                animation: animation,
                plugins: {
                    legend: { display: false },
                    centerText: { text: brevTotal.toLocaleString('fr-FR'), subtext: 'Total' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.label + ': ' + ctx.parsed.toLocaleString('fr-FR');
                            }
                        }
                    }
                }
            },
            plugins: [centerTextPlugin]
        });
    }

    var pai = chartData.paiements || { labels: [], values: [] };
    var paiCanvas = document.getElementById('paiementsByMonthChart');
    if (paiCanvas && pai.labels && pai.labels.length) {
        new Chart(paiCanvas, {
            type: 'line',
            data: {
                labels: pai.labels,
                datasets: [{
                    label: 'Paiements (CDF)',
                    data: pai.values,
                    borderColor: '#D97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#D97706',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animation,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.parsed.y.toLocaleString('fr-FR') + ' CDF';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: {
                            callback: function (value) { return value.toLocaleString('fr-FR'); }
                        }
                    }
                }
            }
        });
    }
})();
</script>
