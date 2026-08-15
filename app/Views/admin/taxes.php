<?php
// Variables injectées par le contrôleur
$taxes = $taxes ?? [];
$paiements = $paiements ?? [];

function getPaiementStatutMeta($statut) {
    $metas = [
        'en_attente' => ['label' => 'En attente', 'color' => '#D97706', 'bg' => '#FFFBEB'],
        'valide' => ['label' => 'Validé', 'color' => '#059669', 'bg' => '#ECFDF5'],
        'rejete' => ['label' => 'Rejeté', 'color' => '#DC2626', 'bg' => '#FEF2F2'],
        'rembourse' => ['label' => 'Remboursé', 'color' => '#64748B', 'bg' => '#F1F5F9'],
    ];
    return $metas[$statut] ?? ['label' => $statut, 'color' => '#64748B', 'bg' => '#F1F5F9'];
}

function formatCurrency($amount) {
    return number_format($amount ?? 0, 0, ',', ' ') . ' CDF';
}

function formatDate($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($date) {
    if (!$date) return '-';
    return date('d/m/Y H:i', strtotime($date));
}
?>

<style>
    .taxes-page {
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
        background: #D97706;
        color: white;
    }

    .btn-primary:hover {
        background: #B45309;
    }

    .btn svg {
        width: 18px;
        height: 18px;
    }

    /* Section */
    .section {
        margin-bottom: 32px;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1A2744;
        margin: 0;
    }

    /* Cards Grid */
    .taxes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .tax-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .tax-type {
        font-size: 16px;
        font-weight: 600;
        color: #1A2744;
        margin-bottom: 8px;
    }

    .tax-description {
        font-size: 13px;
        color: #64748B;
        margin-bottom: 12px;
    }

    .tax-amount {
        font-size: 24px;
        font-weight: 700;
        color: #059669;
    }

    .tax-period {
        font-size: 12px;
        color: #94A3B8;
        margin-top: 4px;
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

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .paiement-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .paiement-ref {
        font-weight: 500;
        color: #1A2744;
        font-family: monospace;
    }

    .paiement-type {
        font-size: 12px;
        color: #64748B;
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

    .action-btn-view {
        background: #EFF6FF;
        color: #3B82F6;
    }

    .action-btn-view:hover {
        background: #DBEAFE;
    }

    .action-btn-verify {
        background: #ECFDF5;
        color: #059669;
    }

    .action-btn-verify:hover {
        background: #D1FAE5;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748B;
    }

    .empty-state svg {
        width: 48px;
        height: 48px;
        color: #CBD5E1;
        margin-bottom: 12px;
    }

    .empty-state h4 {
        margin: 0 0 4px 0;
        color: #334155;
    }

    .empty-state p {
        margin: 0;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .taxes-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="taxes-page">
    <div class="page-header">
        <h1 class="page-title">Taxes & Paiements</h1>
        <button class="btn btn-primary">
            <i data-lucide="plus"></i>
            Nouvelle taxe
        </button>
    </div>

    <!-- Taxes Grid -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Types de taxes</h2>
        </div>
        
        <?php if (empty($taxes)): ?>
            <div class="empty-state">
                <i data-lucide="ticket-percent"></i>
                <h4>Aucune taxe définie</h4>
                <p>Commencez par créer des types de taxes</p>
            </div>
        <?php else: ?>
            <div class="taxes-grid">
                <?php foreach ($taxes as $tax): ?>
                    <div class="tax-card">
                        <div class="tax-type"><?= htmlspecialchars($tax['type_taxe'] ?? '') ?></div>
                        <div class="tax-description"><?= htmlspecialchars($tax['description'] ?? '') ?></div>
                        <div class="tax-amount"><?= formatCurrency($tax['montant'] ?? 0) ?></div>
                        <div class="tax-period"><?= htmlspecialchars(ucfirst($tax['periodicite'] ?? 'annuel')) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Paiements Table -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Historique des paiements</h2>
        </div>
        
        <div class="table-container">
            <?php if (empty($paiements)): ?>
                <div class="empty-state">
                    <i data-lucide="credit-card"></i>
                    <h4>Aucun paiement enregistré</h4>
                    <p>Les paiements apparaîtront ici</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Type Taxe</th>
                            <th>Conducteur</th>
                            <th>Véhicule</th>
                            <th>Montant</th>
                            <th>Mode Paiement</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paiements as $paiement): 
                            $statutMeta = getPaiementStatutMeta($paiement['statut'] ?? 'en_attente');
                        ?>
                            <tr>
                                <td>
                                    <div class="paiement-info">
                                        <span class="paiement-ref"><?= htmlspecialchars($paiement['numero_transaction'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($paiement['type_taxe'] ?? '-') ?></td>
                                <td><?= htmlspecialchars(($paiement['conducteur_prenom'] ?? '') . ' ' . ($paiement['conducteur_nom'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($paiement['numero_plaque'] ?? '-') ?></td>
                                <td><?= formatCurrency($paiement['montant'] ?? 0) ?></td>
                                <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $paiement['mode_paiement'] ?? '-'))) ?></td>
                                <td><?= formatDateTime($paiement['date_paiement'] ?? null) ?></td>
                                <td>
                                    <span class="badge" style="background: <?= $statutMeta['bg'] ?>; color: <?= $statutMeta['color'] ?>;">
                                        <?= htmlspecialchars($statutMeta['label']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn action-btn-view" title="Voir">
                                            <i data-lucide="eye"></i>
                                        </button>
                                        <button class="action-btn action-btn-verify" title="Vérifier">
                                            <i data-lucide="circle-check-big"></i>
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
</div>
