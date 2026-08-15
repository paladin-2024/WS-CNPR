<?php
// Variables injectées par le contrôleur
$stats = $stats ?? [];
$brevetStats = $brevetStats ?? [];
$recentActivity = $recentActivity ?? [];
$vehicleDistribution = $vehicleDistribution ?? [];
$registrationTrend = $registrationTrend ?? [];
$quickActions = $quickActions ?? [];

// Fixed color per vehicle type (not per array position, which depends on the
// unspecified GROUP BY/ORDER BY result order) so a given type always reads the
// same color across dashboard and statistiques pages.
$vehicleTypeColors = [
    'Taxi' => '#3B82F6',
    'Bus' => '#8B5CF6',
    'Camion' => '#D97706',
    'Moto' => '#10B981',
    'Voiture' => '#6366F1',
];

$dashboardChartData = [
    'vehicleDistribution' => [
        'labels' => array_map(fn($v) => (string)($v['type'] ?? ''), $vehicleDistribution),
        'values' => array_map(fn($v) => (int)($v['count'] ?? 0), $vehicleDistribution),
        'colors' => array_map(fn($v) => $vehicleTypeColors[$v['type'] ?? ''] ?? ($v['color'] ?? '#3B82F6'), $vehicleDistribution),
    ],
    'registrationTrend' => [
        'labels' => array_map(function ($t) {
            $moisMap = ['01' => 'Jan', '02' => 'Fév', '03' => 'Mar', '04' => 'Avr', '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Aoû', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Déc'];
            $parts = explode('-', $t['mois'] ?? '');
            return isset($parts[1], $moisMap[$parts[1]]) ? $moisMap[$parts[1]] . ' ' . $parts[0] : ($t['mois'] ?? '');
        }, $registrationTrend),
        'values' => array_map(fn($t) => (int)($t['count'] ?? 0), $registrationTrend),
    ],
];

// Fonction helper pour formater les nombres
function formatNumber($num, $isCurrency = false) {
    if ($num >= 1000000000) {
        return round($num / 1000000000, 1) . ' Md';
    } elseif ($num >= 1000000) {
        return round($num / 1000000, 1) . ' M';
    } elseif ($num >= 1000) {
        return round($num / 1000, 1) . ' K';
    }
    if ($isCurrency) {
        return number_format($num, 2, ',', ' ') . ' USD';
    }
    return number_format($num, 0, ',', ' ');
}

function formatCurrency($amount) {
    return number_format($amount, 0, ',', ' ') . ' CDF';
}
?>

<style>
    .dashboard-page {
        padding: 24px;
    }

    .dashboard-header {
        margin-bottom: 24px;
    }

    .dashboard-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #1A2744;
        margin: 0 0 4px 0;
    }

    .dashboard-header p {
        color: #64748B;
        margin: 0;
    }

    /* Stats Grid */
    .dashboard-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .stat-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon-wrap svg {
        width: 22px;
        height: 22px;
    }

    .stat-delta {
        font-size: 12px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
    }

    .stat-delta.positive {
        background: #ECFDF5;
        color: #059669;
    }

    .stat-label {
        font-size: 13px;
        color: #64748B;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #1A2744;
    }

    /* Brevet Stats */
    .brevet-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1A2744;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .brevet-section-title svg {
        width: 20px;
        height: 20px;
        color: #6366F1;
    }

    .brevet-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .brevet-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .brevet-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .brevet-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .brevet-icon-wrap svg {
        width: 24px;
        height: 24px;
    }

    .brevet-card-label {
        font-size: 13px;
        color: #64748B;
        margin-bottom: 2px;
    }

    .brevet-card-value {
        font-size: 24px;
        font-weight: 700;
        color: #1A2744;
    }

    /* Dashboard Row 2 */
    .dashboard-row2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .dashboard-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .dashboard-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .dashboard-card-title {
        font-size: 16px;
        font-weight: 600;
        color: #1A2744;
    }

    .dashboard-card-link {
        font-size: 13px;
        color: #3B82F6;
        text-decoration: none;
    }

    .dashboard-card-link:hover {
        text-decoration: underline;
    }

    /* Activity List */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 8px;
        background: #F8FAFC;
    }

    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .activity-icon svg {
        width: 18px;
        height: 18px;
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-label {
        font-size: 13px;
        color: #334155;
        font-weight: 500;
    }

    .activity-name {
        font-size: 12px;
        color: #64748B;
    }

    .activity-time {
        font-size: 11px;
        color: #94A3B8;
        flex-shrink: 0;
    }

    /* Vehicle Distribution chart */
    .chart-canvas-wrap {
        position: relative;
        height: 220px;
    }

    .chart-empty-state {
        color: #64748B;
        text-align: center;
        padding: 40px 20px;
        font-size: 13px;
    }

    .chart-value-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #F1F5F9;
    }

    .chart-value-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #334155;
    }

    .chart-value-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .chart-value-legend-count {
        font-weight: 700;
        color: #1A2744;
    }

    /* Row 3: registration trend */
    .dashboard-row3 {
        margin-bottom: 24px;
    }

    /* Quick Actions */
    .quick-actions-section {
        margin-bottom: 24px;
    }

    .quick-actions-title {
        font-size: 16px;
        font-weight: 600;
        color: #1A2744;
        margin-bottom: 16px;
    }

    .quick-actions-list {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        background: white;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s;
    }

    .quick-action-btn:hover {
        border-color: var(--action-color);
        background: var(--action-bg);
        color: var(--action-color);
    }

    .quick-action-btn svg {
        width: 18px;
        height: 18px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 900px) {
        .dashboard-row2 {
            grid-template-columns: 1fr;
        }
        .brevet-stats-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .dashboard-stats-grid {
            grid-template-columns: 1fr;
        }
        
        .dashboard-page {
            padding: 16px;
        }
    }
</style>

<div class="dashboard-page">
    <div class="dashboard-header">
        <h2>Tableau de bord</h2>
        <p><?= htmlspecialchars($dashboardSubtitle ?? 'Bienvenue sur le portail d\'administration du Ministère des Transports') ?></p>
    </div>

    <!-- Stats Grid -->
    <div class="dashboard-stats-grid">
        <?php foreach ($stats as $stat): ?>
            <a href="<?= BASE_PATH ?><?= htmlspecialchars($stat['href'] ?? '#') ?>" class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon-wrap" style="background: <?= htmlspecialchars($stat['color'] ?? '#3B82F6') ?>20;">
                        <i data-lucide="<?= htmlspecialchars(($stat['icon'] ?? '') === 'dollar' ? 'dollar-sign' : ($stat['icon'] ?? 'dollar-sign')) ?>" style="color:<?= htmlspecialchars($stat['color'] ?? '#3B82F6') ?>;"></i>
                    </div>
                    <?php if (isset($stat['delta'])): ?>
                        <span class="stat-delta <?= $stat['delta'] >= 0 ? 'positive' : 'negative' ?>">
                            <?= $stat['delta'] >= 0 ? '+' : '' ?><?= $stat['delta'] ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <div class="stat-label"><?= htmlspecialchars($stat['label'] ?? '') ?></div>
                <div class="stat-value"><?= formatNumber($stat['value'] ?? 0, ($stat['icon'] ?? '') === 'dollar') ?></div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Brevet Stats -->
    <?php if (!empty($brevetStats)): ?>
        <div class="brevet-section-title">
            <i data-lucide="file-text"></i>
            Suivi des Brevets
        </div>
        <div class="brevet-stats-grid">
            <?php foreach ($brevetStats as $brevet): ?>
                <a href="<?= BASE_PATH ?><?= htmlspecialchars($brevet['href'] ?? '#') ?>" class="brevet-card">
                    <div class="brevet-icon-wrap" style="background: <?= htmlspecialchars($brevet['color'] ?? '#6366F1') ?>15;">
                        <i data-lucide="<?= htmlspecialchars(($brevet['icon'] ?? '') === 'check-circle' ? 'circle-check-big' : ($brevet['icon'] ?? 'file-plus')) ?>" style="color:<?= htmlspecialchars($brevet['color'] ?? '#6366F1') ?>;"></i>
                    </div>
                    <div>
                        <div class="brevet-card-label"><?= htmlspecialchars($brevet['label'] ?? '') ?></div>
                        <div class="brevet-card-value"><?= formatNumber($brevet['value'] ?? 0) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <?php if (!empty($quickActions)): ?>
        <div class="quick-actions-section">
            <div class="quick-actions-title">Actions rapides</div>
            <div class="quick-actions-list">
                <?php foreach ($quickActions as $action): ?>
                    <a href="<?= BASE_PATH ?><?= htmlspecialchars($action['href'] ?? '#') ?>" class="quick-action-btn" style="--action-color: <?= htmlspecialchars($action['color'] ?? '#3B82F6') ?>; --action-bg: <?= htmlspecialchars($action['color'] ?? '#3B82F6') ?>10;">
                        <i data-lucide="<?= htmlspecialchars($action['icon'] ?? 'circle') ?>"></i>
                        <?= htmlspecialchars($action['label'] ?? '') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Row 2: Activity & Distribution -->
    <div class="dashboard-row2">
        <!-- Recent Activity -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="dashboard-card-title">Activité récente</span>
                <a href="<?= BASE_PATH ?>/admin/imprimeur" class="dashboard-card-link">Voir tout</a>
            </div>
            <div class="activity-list">
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: <?= htmlspecialchars($activity['color'] ?? '#3B82F6') ?>20; color: <?= htmlspecialchars($activity['color'] ?? '#3B82F6') ?>;">
                            <i data-lucide="<?= htmlspecialchars(($activity['icon'] ?? '') === 'check-circle' ? 'circle-check-big' : ($activity['icon'] ?? 'triangle-alert')) ?>"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-label"><?= htmlspecialchars($activity['label'] ?? '') ?></div>
                            <div class="activity-name"><?= htmlspecialchars($activity['name'] ?? '') ?></div>
                        </div>
                        <div class="activity-time"><?= htmlspecialchars($activity['time'] ?? '') ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Vehicle Distribution -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <span class="dashboard-card-title">Répartition des véhicules</span>
            </div>
            <?php if (!empty($vehicleDistribution)): ?>
                <div class="chart-canvas-wrap">
                    <canvas id="vehicleDistributionChart" role="img" aria-label="Répartition des véhicules par type"></canvas>
                </div>
                <div class="chart-value-legend">
                    <?php foreach ($vehicleDistribution as $vehicle): ?>
                        <span class="chart-value-legend-item">
                            <span class="chart-value-legend-dot" style="background: <?= htmlspecialchars($vehicleTypeColors[$vehicle['type'] ?? ''] ?? ($vehicle['color'] ?? '#3B82F6')) ?>;"></span>
                            <?= htmlspecialchars($vehicle['type'] ?? '') ?>
                            <span class="chart-value-legend-count"><?= number_format($vehicle['count'] ?? 0, 0, ',', ' ') ?></span>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="chart-empty-state">Aucune donnée disponible</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Row 3: Registration trend -->
    <?php if (!empty($registrationTrend)): ?>
        <div class="dashboard-row3">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <span class="dashboard-card-title">Nouveaux conducteurs enregistrés (6 derniers mois)</span>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="registrationTrendChart" role="img" aria-label="Nombre de conducteurs enregistrés par mois, six derniers mois"></canvas>
                </div>
                <div class="chart-value-legend">
                    <?php foreach ($registrationTrend as $i => $t): ?>
                        <span class="chart-value-legend-item">
                            <span class="chart-value-legend-dot" style="background: #3B82F6;"></span>
                            <?= htmlspecialchars($dashboardChartData['registrationTrend']['labels'][$i] ?? '') ?>
                            <span class="chart-value-legend-count"><?= number_format($t['count'] ?? 0, 0, ',', ' ') ?></span>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script type="application/json" id="dashboard-chart-data"><?= json_encode($dashboardChartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (typeof Chart === 'undefined') { return; }

    var dataEl = document.getElementById('dashboard-chart-data');
    var chartData = {};
    try {
        chartData = JSON.parse((dataEl && dataEl.textContent) || '{}');
    } catch (e) {
        chartData = {};
    }

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var animation = reduceMotion ? false : { duration: 700, easing: 'easeOutQuart' };

    var dist = chartData.vehicleDistribution || { labels: [], values: [], colors: [] };
    var distCanvas = document.getElementById('vehicleDistributionChart');
    if (distCanvas && dist.labels && dist.labels.length) {
        new Chart(distCanvas, {
            type: 'bar',
            data: {
                labels: dist.labels,
                datasets: [{
                    label: 'Véhicules',
                    data: dist.values,
                    backgroundColor: dist.colors,
                    borderRadius: 4,
                    maxBarThickness: 32
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: animation,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.label + ': ' + ctx.parsed.x.toLocaleString('fr-FR') + ' véhicule(s)';
                            }
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    var trend = chartData.registrationTrend || { labels: [], values: [] };
    var trendCanvas = document.getElementById('registrationTrendChart');
    if (trendCanvas && trend.labels && trend.labels.length) {
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: trend.labels,
                datasets: [{
                    label: 'Conducteurs enregistrés',
                    data: trend.values,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#3B82F6',
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
                                return ctx.parsed.y.toLocaleString('fr-FR') + ' conducteur(s)';
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
})();
</script>
