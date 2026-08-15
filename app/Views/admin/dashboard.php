<?php
// Variables injectées par le contrôleur
$stats = $stats ?? [];
$brevetStats = $brevetStats ?? [];
$recentActivity = $recentActivity ?? [];
$vehicleDistribution = $vehicleDistribution ?? [];
$quickActions = $quickActions ?? [];

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

    /* Vehicle Distribution */
    .vehicle-dist-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .vehicle-dist-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .vehicle-dist-label {
        width: 70px;
        font-size: 13px;
        color: #334155;
        flex-shrink: 0;
    }

    .vehicle-dist-bar-wrap {
        flex: 1;
        height: 24px;
        background: #E2E8F0;
        border-radius: 4px;
        overflow: hidden;
    }

    .vehicle-dist-bar {
        height: 100%;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
    }

    .vehicle-dist-value {
        font-size: 11px;
        font-weight: 600;
        color: white;
    }

    .vehicle-dist-count {
        width: 50px;
        text-align: right;
        font-size: 13px;
        color: #64748B;
        flex-shrink: 0;
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
            <div class="vehicle-dist-list">
                <?php 
                $colors = ['#3B82F6', '#8B5CF6', '#059669', '#D97706', '#EF4444'];
                $i = 0;
                foreach ($vehicleDistribution as $vehicle): 
                ?>
                    <div class="vehicle-dist-item">
                        <div class="vehicle-dist-label"><?= htmlspecialchars($vehicle['type'] ?? '') ?></div>
                        <div class="vehicle-dist-bar-wrap">
                            <div class="vehicle-dist-bar" style="width: <?= htmlspecialchars($vehicle['percent'] ?? 0) ?>%; background: <?= $colors[$i % count($colors)] ?>;">
                                <span class="vehicle-dist-value"><?= htmlspecialchars($vehicle['percent'] ?? 0) ?>%</span>
                            </div>
                        </div>
                        <div class="vehicle-dist-count"><?= number_format($vehicle['count'] ?? 0, 0, ',', ' ') ?></div>
                    </div>
                <?php 
                $i++;
                endforeach; 
                ?>
            </div>
        </div>
    </div>
</div>
