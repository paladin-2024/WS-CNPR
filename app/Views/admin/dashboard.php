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
                        <?php if (($stat['icon'] ?? '') === 'user-check'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="<?= htmlspecialchars($stat['color'] ?? '#3B82F6') ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php elseif (($stat['icon'] ?? '') === 'car'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="<?= htmlspecialchars($stat['color'] ?? '#3B82F6') ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <?php elseif (($stat['icon'] ?? '') === 'credit-card'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="<?= htmlspecialchars($stat['color'] ?? '#3B82F6') ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <?php elseif (($stat['icon'] ?? '') === 'dollar'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="<?= htmlspecialchars($stat['color'] ?? '#3B82F6') ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php else: ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="<?= htmlspecialchars($stat['color'] ?? '#3B82F6') ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php endif; ?>
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
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Suivi des Brevets
        </div>
        <div class="brevet-stats-grid">
            <?php foreach ($brevetStats as $brevet): ?>
                <a href="<?= BASE_PATH ?><?= htmlspecialchars($brevet['href'] ?? '#') ?>" class="brevet-card">
                    <div class="brevet-icon-wrap" style="background: <?= htmlspecialchars($brevet['color'] ?? '#6366F1') ?>15;">
                        <?php if (($brevet['icon'] ?? '') === 'file-plus'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="<?= htmlspecialchars($brevet['color']) ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <?php elseif (($brevet['icon'] ?? '') === 'printer'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="<?= htmlspecialchars($brevet['color']) ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <?php elseif (($brevet['icon'] ?? '') === 'check-circle'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="<?= htmlspecialchars($brevet['color']) ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php endif; ?>
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
                        <?php if (($action['icon'] ?? '') === 'user-plus'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <?php elseif (($action['icon'] ?? '') === 'car-front'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <?php elseif (($action['icon'] ?? '') === 'wallet'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <?php elseif (($action['icon'] ?? '') === 'users'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <?php elseif (($action['icon'] ?? '') === 'trending-up'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <?php elseif (($action['icon'] ?? '') === 'printer'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <?php elseif (($action['icon'] ?? '') === 'clipboard-check'): ?>
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <?php endif; ?>
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
                            <?php if (($activity['icon'] ?? '') === 'user-check'): ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?php elseif (($activity['icon'] ?? '') === 'car'): ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <?php elseif (($activity['icon'] ?? '') === 'credit-card'): ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <?php elseif (($activity['icon'] ?? '') === 'wallet'): ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <?php elseif (($activity['icon'] ?? '') === 'file-plus'): ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <?php elseif (($activity['icon'] ?? '') === 'printer'): ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <?php elseif (($activity['icon'] ?? '') === 'check-circle'): ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?php else: ?>
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <?php endif; ?>
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
