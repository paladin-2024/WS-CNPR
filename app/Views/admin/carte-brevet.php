<?php
$c           = $conducteur ?? [];
$photoBase64 = $photoBase64 ?? null;
$qrCodeUrl   = $qrCodeUrl   ?? '';
$age         = $age         ?? null;
$carteLogoGauche = $carteLogoGauche ?? null;
$carteLogoDroite = $carteLogoDroite ?? null;
$carteSignature  = $carteSignature  ?? null;
$carteTitreGauche = $carteTitreGauche ?? 'République Démocratique du Congo';
$carteTitreDroite = $carteTitreDroite ?? 'Direction Provinciale de la CNPR';

$nomComplet   = trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? ''));
$numeroPermis = $c['numero_permis'] ?? '';
$categorie    = strtoupper($c['categorie_permis'] ?? '');
$telephone    = $c['telephone'] ?? '';
$adresse      = $c['adresse'] ?? '';
$lieuNaiss    = $c['lieu_naissance'] ?? '';
$dateNaiss    = !empty($c['date_naissance'])      ? date('d/m/Y', strtotime($c['date_naissance']))      : '';
$dateEnreg    = !empty($c['date_enregistrement']) ? date('d/m/Y', strtotime($c['date_enregistrement'])) : '';
$dateExpirPermis = !empty($c['date_expiration_permis']) ? date('d/m/Y', strtotime($c['date_expiration_permis'])) : '';
$syndicat     = $c['syndicat'] ?? '';
$association  = $c['association'] ?? '';
$idNum        = str_pad($c['id'] ?? '0', 3, '0', STR_PAD_LEFT);
$slug         = preg_replace('/\s+/', '_', strtolower($nomComplet));

$categories = ['A' => 'Moto', 'B' => 'Voiture', 'C' => 'Bus', 'D' => 'Mini-Camion', 'E' => 'Camion'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carte Brevet — <?= htmlspecialchars($nomComplet) ?></title>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
/* ═══════════════════════════════════
   BASE
═══════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --blue:   #0050A0;
    --yellow: #F7C800;
    --red:    #C8102E;
    --dark:   #0F172A;
    --grey:   #64748B;
    --light:  #F1F5F9;
    --scale:  2.4;   /* zoom d'affichage écran */
}

body {
    font-family: 'Open Sans', Arial, sans-serif;
    background: #CBD5E1;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 16px 56px;
}

/* ═══════════════════════════════════
   TOOLBAR
═══════════════════════════════════ */
.toolbar {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    background: #0F172A; border-radius: 10px;
    padding: 10px 18px; margin-bottom: 32px;
    width: 100%; max-width: 900px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
.toolbar-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 14px; font-weight: 700; color: white; flex: 1;
    display: flex; align-items: center; gap: 8px;
}
.toolbar-title svg { width: 18px; height: 18px; color: #60A5FA; flex-shrink: 0; }

.tbtn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 13px; border-radius: 7px; font-size: 12px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600; cursor: pointer; border: none;
    text-decoration: none; transition: all 0.15s; white-space: nowrap;
}
.tbtn svg { width: 14px; height: 14px; }
.tbtn-back  { background: rgba(255,255,255,0.1); color: #CBD5E1; }
.tbtn-back:hover { background: rgba(255,255,255,0.2); }
.tbtn-print { background: #3B82F6; color: white; }
.tbtn-print:hover { background: #2563EB; }
.tbtn-dl-r  { background: #059669; color: white; }
.tbtn-dl-r:hover { background: #047857; }
.tbtn-dl-v  { background: #7C3AED; color: white; }
.tbtn-dl-v:hover { background: #6D28D9; }
.tbtn-dl-both { background: #D97706; color: white; }
.tbtn-dl-both:hover { background: #B45309; }

/* ═══════════════════════════════════
   LABEL DE FACE
═══════════════════════════════════ */
.face-label {
    font-family: 'Montserrat', sans-serif;
    font-size: 11px; font-weight: 800; color: #475569;
    letter-spacing: 2px; text-transform: uppercase;
    align-self: flex-start; margin-bottom: 8px; padding-left: 4px;
}

/* ═══════════════════════════════════
   WRAPPER POUR LE ZOOM ÉCRAN
   La carte réelle est 85.6×54 mm.
   On l'affiche à ×scale sur écran.
═══════════════════════════════════ */
.cards-wrapper {
    display: flex; flex-direction: column;
    align-items: center; gap: 12px;
}

.card-zoom-host {
    /* Réserve la place visuelle à l'échelle screen */
    width:  calc(85.6mm * var(--scale));
    height: calc(54mm   * var(--scale));
    display: flex; align-items: flex-start; justify-content: center;
    flex-shrink: 0;
}

/* ═══════════════════════════════════
   CARTE (dimensions réelles PVC)
═══════════════════════════════════ */
.card {
    width:  85.6mm;
    height: 54mm;
    border-radius: 3.5mm;
    overflow: hidden;
    background: white;
    position: relative;
    flex-shrink: 0;
    transform: scale(var(--scale));
    transform-origin: top left;
    box-shadow: 0 12px 40px rgba(0,0,0,0.35);
}

/* ═══════════════════════════════════
   BANDE TRICOLORE DRC (gauche)
═══════════════════════════════════ */
.stripe {
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 5mm; display: flex; flex-direction: column; z-index: 3;
}
.stripe .s1 { flex: 1; background: var(--blue); }
.stripe .s2 { flex: 1; background: var(--yellow); }
.stripe .s3 { flex: 1; background: var(--red); }

/* ═══════════════════════════════════
   RECTO
═══════════════════════════════════ */
.recto-inner {
    position: absolute; inset: 0;
    padding: 2.2mm 2mm 1.2mm 6.5mm;
    display: flex; flex-direction: column;
    background: white;
}

/* Fond subtil watermark */
.recto-inner::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse at 90% 10%, rgba(0,80,160,0.06) 0%, transparent 50%),
        radial-gradient(ellipse at 10% 90%, rgba(200,16,46,0.05) 0%, transparent 45%);
    pointer-events: none; z-index: 0;
}
.recto-inner > * { position: relative; z-index: 1; }

/* ── En-tête recto ── */
.r-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 1mm;
    margin-bottom: 1mm;
}

.r-header-top {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 2mm;
    width: 100%;
}

.r-header-texts {
    display: flex; flex-direction: column; gap: 0.2mm;
    line-height: 1.25; text-align: center;
}
.r-rdc {
    font-family: 'Montserrat', sans-serif;
    font-size: 5px; font-weight: 900; color: var(--dark);
    text-transform: uppercase; letter-spacing: 0.15px;
}
.r-province {
    font-family: 'Montserrat', sans-serif;
    font-size: 4.5px; font-weight: 800; color: var(--blue);
    text-transform: uppercase; letter-spacing: 0.15px;
}
.r-direction {
    font-family: 'Open Sans', sans-serif;
    font-size: 4.2px; color: #334155; font-weight: 700;
}
.r-sep {
    height: 0.4mm; width: 70%;
    background: linear-gradient(to right, var(--blue), var(--yellow), var(--red));
    margin: 0.6mm auto;
}
.r-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 5.5px; font-weight: 900; color: var(--red);
    text-transform: uppercase; letter-spacing: 0.3px;
    line-height: 1.2;
}

.r-header-logos {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1.5mm;
}
.r-logo {
    max-width: 12mm;
    max-height: 10mm;
    object-fit: contain;
}

/* N° ID — sous l'entête, centré */
.r-card-id-block {
    display: flex; flex-direction: row; align-items: center; justify-content: center;
    gap: 1.5mm;
    padding: 0.3mm 2mm;
    background: linear-gradient(to right, var(--blue), var(--red));
    border-radius: 1mm;
    margin-top: 0.3mm;
}
.r-card-id-label {
    font-family: 'Open Sans', sans-serif;
    font-size: 3.5px; color: white;
    text-transform: uppercase; letter-spacing: 0.3px; font-weight: 700;
}
.r-card-id-num {
    font-family: 'Montserrat', sans-serif;
    font-size: 6px; font-weight: 900; color: white;
    letter-spacing: 0.5px; line-height: 1;
}

/* ── Zone principale : photo | infos | QR ── */
.r-main {
    display: flex; flex-direction: row;
    gap: 2mm; flex: 1; align-items: flex-start;
}

/* Photo */
.r-photo {
    width: 15mm; min-height: 20mm; max-height: 22mm;
    border: 0.4mm solid #CBD5E1; border-radius: 1.2mm;
    overflow: hidden; flex-shrink: 0;
    background: var(--light);
    display: flex; align-items: center; justify-content: center;
}
.r-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
.r-photo-ph { font-size: 6px; color: #94A3B8; text-align: center; }

/* Infos */
.r-infos {
    flex: 1; display: flex; flex-direction: column; gap: 0.6mm; min-width: 0;
}
.r-sec-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 4.5px; font-weight: 900; color: var(--blue);
    text-transform: uppercase; letter-spacing: 0.4px;
    border-bottom: 0.3mm solid var(--blue);
    padding-bottom: 0.4mm; margin-bottom: 0.2mm;
}
.r-row {
    display: flex; flex-direction: row;
    gap: 0.8mm; font-size: 5px; line-height: 1.4;
    align-items: baseline;
}
.r-lbl {
    font-family: 'Open Sans', sans-serif;
    font-size: 4.2px; color: var(--grey); font-weight: 800;
    white-space: nowrap; min-width: 12mm; flex-shrink: 0;
}
.r-val {
    font-family: 'Open Sans', sans-serif;
    font-size: 5px; color: var(--dark); font-weight: 700;
    overflow: hidden; min-width: 0;
}
.r-val.strong {
    font-family: 'Montserrat', sans-serif;
    font-size: 5.5px; font-weight: 900; color: var(--dark);
}

/* QR recto */
.r-qr {
    display: flex; flex-direction: column;
    align-items: center; gap: 0.5mm; flex-shrink: 0;
}
.r-qr-box {
    width: 14mm; height: 14mm;
    border: 0.3mm solid #CBD5E1; border-radius: 1mm;
    overflow: hidden; background: white;
}
.r-qr-box img { width: 100%; height: 100%; display: block; }
.r-qr-lbl {
    font-family: 'Open Sans', sans-serif;
    font-size: 3px; color: #94A3B8; font-style: italic;
}

/* ── Pied recto ── */
.r-footer {
    display: flex; flex-direction: row;
    align-items: flex-end; justify-content: space-between;
    margin-top: 0.5mm;
}
.r-footer-left { display: flex; flex-direction: column; gap: 0.25mm; margin-top: auto; }
.r-date {
    font-family: 'Open Sans', sans-serif;
    font-size: 3.8px; color: var(--grey);
}
.r-date b {
    font-family: 'Montserrat', sans-serif;
    color: var(--dark); font-weight: 700;
}
.r-sign {
    font-family: 'Open Sans', sans-serif;
    font-size: 5px; color: var(--grey); font-weight: 700;
    min-width: 22mm; text-align: center; margin-top: 0.5mm;
}
.r-sign-img {
    max-width: 28mm;
    max-height: 10mm;
    object-fit: contain;
}
.r-footer-right {
    display: flex; flex-direction: column;
    align-items: flex-end; gap: 0.8mm;
}
.r-cat {
    border: 0.4mm solid var(--dark); border-radius: 1mm;
    padding: 0.5mm 2mm;
    display: flex; align-items: center; gap: 1mm;
    background: #FAFAFA;
}
.r-cat-lbl {
    font-family: 'Open Sans', sans-serif;
    font-size: 3.3px; color: var(--grey); text-transform: uppercase;
}
.r-cat-val {
    font-family: 'Montserrat', sans-serif;
    font-size: 8px; font-weight: 900; color: var(--red);
}

/* ═══════════════════════════════════
   VERSO
═══════════════════════════════════ */
.verso-inner {
    position: absolute; inset: 0;
    padding: 2mm 2mm 0 6.5mm;
    display: flex; flex-direction: column;
    background: white;
}
.verso-inner::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 75% 75%, rgba(0,80,160,0.05) 0%, transparent 50%);
    pointer-events: none; z-index: 0;
}
.verso-inner > * { position: relative; z-index: 1; }

/* En-tête verso */
.v-header { margin-bottom: 1mm; text-align: center; }
.v-rdc {
    font-family: 'Montserrat', sans-serif;
    font-size: 4.5px; font-weight: 900; color: var(--dark);
    text-transform: uppercase; text-align: center;
}
.v-province {
    font-family: 'Montserrat', sans-serif;
    font-size: 4.2px; font-weight: 800; color: var(--blue);
    text-transform: uppercase; text-align: center;
    margin-top: 0.1mm;
}
.v-h-sep {
    height: 0.4mm; width: 70%; margin: 0.6mm auto 0;
    background: linear-gradient(to right, var(--blue), var(--yellow), var(--red));
}

/* Corps verso */
.v-body {
    display: flex; flex-direction: row;
    gap: 2mm; flex-shrink: 0; align-items: flex-start;
    padding-top: 0.5mm;
}

/* QR verso */
.v-qr {
    display: flex; flex-direction: column;
    align-items: center; gap: 0.5mm; flex-shrink: 0;
}
.v-qr-box {
    width: 17mm; height: 17mm;
    border: 0.3mm solid #CBD5E1; border-radius: 1mm;
    overflow: hidden; background: white;
}
.v-qr-box img { width: 100%; height: 100%; display: block; }
.v-qr-lbl {
    font-family: 'Open Sans', sans-serif;
    font-size: 3px; color: #94A3B8; font-style: italic;
}

/* Infos centre vers0 */
.v-center {
    flex: 1; display: flex; flex-direction: column;
    gap: 1.2mm;
}
.v-lieu-lbl {
    font-family: 'Open Sans', sans-serif;
    font-size: 3.2px; color: var(--grey);
    text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px;
}
.v-lieu-ville {
    font-family: 'Montserrat', sans-serif;
    font-size: 6.5px; font-weight: 900; color: var(--dark); text-transform: uppercase;
    line-height: 1;
}
.v-lieu-date {
    font-family: 'Open Sans', sans-serif;
    font-size: 5px; font-weight: 700; color: var(--red);
}
.v-micro-sep { height: 0.2mm; background: #E2E8F0; margin: 0.5mm 0; }

.v-permis-lbl {
    font-family: 'Open Sans', sans-serif;
    font-size: 3.2px; color: var(--grey); text-transform: uppercase;
    font-weight: 600; letter-spacing: 0.2px;
}
.v-permis-val {
    font-family: 'Montserrat', sans-serif;
    font-size: 5px; font-weight: 800; color: var(--dark); letter-spacing: 0.3px;
}
.v-remarques { margin-top: auto; padding-top: 1mm; border-top: 0.2mm solid #E2E8F0; }
.v-rem-lbl {
    font-family: 'Open Sans', sans-serif;
    font-size: 3.2px; color: var(--grey); text-transform: uppercase;
    font-weight: 700; letter-spacing: 0.3px;
}
.v-rem-val {
    font-family: 'Montserrat', sans-serif;
    font-size: 4px; color: var(--dark); font-weight: 700; text-transform: uppercase;
}

/* Catégories verso */
.v-cats {
    width: 22mm; flex-shrink: 0;
    display: flex; flex-direction: column; gap: 0.7mm;
}
.v-cat-row {
    display: flex; flex-direction: row; align-items: center;
    gap: 1mm; border: 0.3mm solid #E2E8F0; border-radius: 1mm;
    padding: 0.6mm 1mm; background: #F8FAFC;
}
.v-cat-row.active { border-color: var(--red); background: #FFF1F2; }
.v-cat-letter {
    font-family: 'Montserrat', sans-serif;
    font-size: 5.5px; font-weight: 900; color: #94A3B8;
    min-width: 3.5mm; text-align: center;
}
.v-cat-row.active .v-cat-letter { color: var(--red); }
.v-cat-name {
    font-family: 'Open Sans', sans-serif;
    font-size: 3.6px; color: var(--grey);
}
.v-cat-row.active .v-cat-name { color: var(--dark); font-weight: 700; }

/* Texte avertissement verso */
.v-warning {
    text-align: center;
    color: #C8102E;
    font-size: 3.2px;
    font-weight: 700;
    font-family: 'Open Sans', sans-serif;
    text-transform: uppercase;
    line-height: 1.3;
    padding: 0.3mm 1mm;
    flex-shrink: 0;
}

/* Bande bas verso */
.v-footer-stripe {
    height: 2.5mm; width: 100%; flex-shrink: 0;
    display: flex; flex-direction: row; margin-top: auto;
}
.v-footer-stripe .sf1 { flex: 1; background: var(--blue); }
.v-footer-stripe .sf2 { flex: 1; background: var(--yellow); }
.v-footer-stripe .sf3 { flex: 1; background: var(--red); }

/* ═══════════════════════════════════
   IMPRESSION
═══════════════════════════════════ */
@media print {
    @page { size: A4 portrait; margin: 12mm; }
    html, body { background: white !important; padding: 0 !important; }
    .toolbar, .face-label { display: none !important; }

    .cards-wrapper {
        display: grid;
        grid-template-columns: repeat(2, 85.6mm);
        column-gap: 8mm;
        row-gap: 8mm;
        justify-content: center;
    }
    .card-zoom-host {
        width: 85.6mm !important;
        height: 54mm !important;
    }
    .card {
        transform: none !important;
        box-shadow: none !important;
        border: 0.3mm solid #CBD5E1 !important;
        page-break-inside: avoid;
    }
}

/* ═══════════════════════════════════
   LOADER
═══════════════════════════════════ */
#dlOverlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,0.7); z-index: 9999;
    align-items: center; justify-content: center;
    flex-direction: column; gap: 14px;
}
#dlOverlay.show { display: flex; }
.dl-spin {
    width: 44px; height: 44px;
    border: 4px solid rgba(255,255,255,0.2);
    border-top-color: white; border-radius: 50%;
    animation: spin 0.75s linear infinite;
}
.dl-msg {
    font-family: 'Montserrat', sans-serif;
    color: white; font-size: 14px; font-weight: 600;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>

<!-- Loader -->
<div id="dlOverlay">
    <div class="dl-spin"></div>
    <div class="dl-msg" id="dlMsg">Génération en cours…</div>
</div>

<!-- ══ TOOLBAR ══ -->
<div class="toolbar">
    <div class="toolbar-title">
        <i data-lucide="id-card"></i>
        Carte Brevet — <?= htmlspecialchars($nomComplet) ?>
    </div>

    <a href="<?= BASE_PATH ?>/admin/imprimeur" class="tbtn tbtn-back">
        <i data-lucide="arrow-left"></i>
        Retour
    </a>
    <button class="tbtn tbtn-print" onclick="window.print()">
        <i data-lucide="printer"></i>
        Imprimer
    </button>
    <button class="tbtn tbtn-dl-r" onclick="downloadCard('recto')">
        <i data-lucide="download"></i>
        Recto PNG
    </button>
    <button class="tbtn tbtn-dl-v" onclick="downloadCard('verso')">
        <i data-lucide="download"></i>
        Verso PNG
    </button>
    <button class="tbtn tbtn-dl-both" onclick="downloadCard('both')">
        <i data-lucide="download"></i>
        Recto + Verso
    </button>
</div>

<!-- ══ CARTES ══ -->
<div class="cards-wrapper">

    <!-- ▸ RECTO -->
    <span class="face-label">▸ Recto — Face avant</span>
    <div class="card-zoom-host">
    <div class="card" id="cardRecto">

        <div class="stripe"><div class="s1"></div><div class="s2"></div><div class="s3"></div></div>

        <div class="recto-inner">

            <!-- En-tête -->
            <div class="r-header">
                <div class="r-header-top">
                    <?php if ($carteLogoGauche): ?>
                        <img src="<?= $carteLogoGauche ?>" alt="Logo gauche" class="r-logo">
                    <?php endif; ?>
                    <div class="r-header-texts">
                        <div class="r-rdc"><?= htmlspecialchars($carteTitreGauche) ?></div>
                        <div class="r-province">Province de la TSHOPO</div>
                        <div class="r-direction"><?= htmlspecialchars($carteTitreDroite) ?></div>
                        <div class="r-sep"></div>
                        <div class="r-title">Brevet de Recyclage Obligatoire de Conducteurs</div>
                    </div>
                    <?php if ($carteLogoDroite): ?>
                        <img src="<?= $carteLogoDroite ?>" alt="Logo droite" class="r-logo">
                    <?php endif; ?>
                </div>
                <div class="r-card-id-block">
                    <div class="r-card-id-label">N° Brevet</div>
                    <div class="r-card-id-num"><?= htmlspecialchars($idNum) ?></div>
                </div>
            </div>

            <!-- Photo | Infos | QR -->
            <div class="r-main">

                <!-- Photo -->
                <div class="r-photo">
                    <?php if ($photoBase64): ?>
                        <img src="<?= $photoBase64 ?>" alt="Photo conducteur">
                    <?php else: ?>
                        <span class="r-photo-ph">
                            <i data-lucide="user" style="width:18px;height:18px;color:#94A3B8;"></i>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Informations -->
                <div class="r-infos">
                    <div class="r-sec-title">Identité</div>

                    <div class="r-row">
                        <span class="r-lbl">Nom &amp; Prénom</span>
                        <span class="r-val strong"><?= htmlspecialchars($nomComplet) ?></span>
                    </div>
                    <?php if ($dateNaiss || $lieuNaiss): ?>
                    <div class="r-row">
                        <span class="r-lbl">Né(e) le</span>
                        <span class="r-val"><?= htmlspecialchars($dateNaiss) ?><?= ($dateNaiss && $lieuNaiss) ? ' à ' : '' ?><?= htmlspecialchars($lieuNaiss) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($adresse): ?>
                    <div class="r-row">
                        <span class="r-lbl">Adresse</span>
                        <span class="r-val"><?= htmlspecialchars($adresse) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($telephone): ?>
                    <div class="r-row">
                        <span class="r-lbl">Téléphone</span>
                        <span class="r-val"><?= htmlspecialchars($telephone) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($association): ?>
                    <div class="r-row">
                        <span class="r-lbl">Association</span>
                        <span class="r-val"><?= htmlspecialchars($association) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($syndicat): ?>
                    <div class="r-row">
                        <span class="r-lbl">Syndicat</span>
                        <span class="r-val"><?= htmlspecialchars($syndicat) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($numeroPermis): ?>
                    <div class="r-row">
                        <span class="r-lbl">N° Permis</span>
                        <span class="r-val strong"><?= htmlspecialchars($numeroPermis) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- QR Code -->
                <div class="r-qr">
                    <?php if ($qrCodeUrl): ?>
                        <div class="r-qr-box">
                            <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code">
                        </div>
                        <div class="r-qr-lbl">Vérifier</div>
                    <?php endif; ?>
                </div>

            </div><!-- r-main -->

            <!-- Pied -->
            <div class="r-footer">
                <div class="r-footer-left">
                    <?php if ($dateEnreg): ?>
                    <div class="r-date">Délivré le : <b><?= htmlspecialchars($dateEnreg) ?></b></div>
                    <?php endif; ?>
                    <div class="r-sign">
                        <?php if ($carteSignature): ?>
                            <img src="<?= $carteSignature ?>" alt="Signature" class="r-sign-img">
                        <?php else: ?>
                            Signature &amp; Sceau DPT/CNPR
                        <?php endif; ?>
                    </div>
                </div>
                <div class="r-footer-right">
                    <div class="r-cat">
                        <span class="r-cat-lbl">Catégorie</span>
                        <span class="r-cat-val"><?= htmlspecialchars($categorie) ?></span>
                    </div>
                </div>
            </div>

        </div><!-- recto-inner -->
    </div>
    </div><!-- card-zoom-host -->

    <!-- ▸ VERSO -->
    <span class="face-label">▸ Verso — Face arrière</span>
    <div class="card-zoom-host">
    <div class="card" id="cardVerso">

        <div class="stripe"><div class="s1"></div><div class="s2"></div><div class="s3"></div></div>

        <div class="verso-inner">

            <!-- En-tête -->
            <div class="v-header">
                <div class="v-rdc"><?= htmlspecialchars($carteTitreGauche) ?></div>
                <div class="v-province">Province de la TSHOPO</div>
                <div class="v-h-sep"></div>
            </div>

            <!-- Corps -->
            <div class="v-body">

                <!-- QR côté verso -->
                <div class="v-qr">
                    <?php if ($qrCodeUrl): ?>
                        <div class="v-qr-box">
                            <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code">
                        </div>
                        <div class="v-qr-lbl">Scan pour vérifier</div>
                    <?php endif; ?>
                </div>

                <!-- Infos centre -->
                <div class="v-center">
                    <div>
                        <div class="v-lieu-lbl">Lieu &amp; Date de délivrance</div>
                        <div class="v-lieu-ville">Kisangani</div>
                        <div class="v-lieu-date">le <?= htmlspecialchars($dateEnreg ?: '—') ?></div>
                    </div>

                    <?php if ($numeroPermis): ?>
                    <div class="v-micro-sep"></div>
                    <div>
                        <div class="v-permis-lbl">N° Permis de conduire</div>
                        <div class="v-permis-val"><?= htmlspecialchars($numeroPermis) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($dateExpirPermis): ?>
                    <div class="v-micro-sep"></div>
                    <div>
                        <div class="v-permis-lbl">Expiration Permis</div>
                        <div class="v-permis-val" style="color: var(--red);"><?= htmlspecialchars($dateExpirPermis) ?></div>
                    </div>
                    <?php endif; ?>

                    <div class="v-remarques">
                        <div class="v-rem-lbl">Remarques &amp; Restrictions</div>
                        <div class="v-rem-val">Aucune</div>
                    </div>
                </div>

                <!-- Catégories -->
                <div class="v-cats">
                    <?php foreach ($categories as $letter => $name): ?>
                    <div class="v-cat-row <?= ($categorie === $letter) ? 'active' : '' ?>">
                        <span class="v-cat-letter"><?= $letter ?></span>
                        <span class="v-cat-name"><?= $name ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div><!-- v-body -->

            <!-- Texte d'avertissement -->
            <div class="v-warning">
                Les autorités tant civiles que militaires sont priées d'apporter assistance au porteur de ce document en cas de besoin
            </div>

            <!-- Bande bas -->
            <div class="v-footer-stripe">
                <div class="sf1"></div><div class="sf2"></div><div class="sf3"></div>
            </div>

        </div><!-- verso-inner -->
    </div>
    </div><!-- card-zoom-host -->

</div><!-- cards-wrapper -->

<script>
const SLUG = '<?= $slug ?>_ID<?= $idNum ?>';

// ── Résolution cible : 300 DPI ─────────────────────────────────
// 85.6 mm × (300 / 25.4) ≈ 1011 px
// 54   mm × (300 / 25.4) ≈  638 px
const TARGET_W = Math.round(85.6 * 300 / 25.4); // 1011
const TARGET_H = Math.round(54   * 300 / 25.4); // 638

/**
 * Capture une carte en 300 DPI.
 *
 * Stratégie : cloner le nœud, le placer hors-transform en position fixe
 * à (0,0), capturer le clone, puis le supprimer.
 * html2canvas voit ainsi l'élément à sa vraie taille CSS, sans transform.
 */
async function cardToCanvas(el) {
    // 1. Cloner la carte (deep = true pour inclure photo / QR)
    const clone = el.cloneNode(true);

    // 2. Positionner le clone : fixed, coin haut-gauche, sans transform,
    //    invisible pour l'utilisateur mais rendu par le navigateur
    Object.assign(clone.style, {
        position:        'fixed',
        top:             '0',
        left:            '0',
        transform:       'none',
        transformOrigin: 'top left',
        zIndex:          '-9999',
        visibility:      'visible',
        pointerEvents:   'none',
        margin:          '0',
    });

    document.body.appendChild(clone);

    // 3. Attendre deux frames : layout + paint
    await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));

    // 4. Dimensions réelles du clone (sans aucun transform)
    const rect  = clone.getBoundingClientRect();
    const scale = TARGET_W / rect.width;

    // 5. Capture
    let canvas;
    try {
        canvas = await html2canvas(clone, {
            scale:           scale,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#ffffff',
            width:           rect.width,
            height:          rect.height,
            logging:         false,
            // Ne pas utiliser x/y ici : le clone est déjà à (0,0)
        });
    } finally {
        document.body.removeChild(clone);
    }

    return canvas;
}

function canvasToBlob(canvas) {
    return new Promise(res => canvas.toBlob(res, 'image/png', 1.0));
}

function triggerDownload(blob, filename) {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = filename;
    document.body.appendChild(a); a.click();
    setTimeout(() => { URL.revokeObjectURL(url); a.remove(); }, 600);
}

function showLoader(msg) {
    document.getElementById('dlMsg').textContent = msg;
    document.getElementById('dlOverlay').classList.add('show');
}
function hideLoader() {
    document.getElementById('dlOverlay').classList.remove('show');
}

async function downloadCard(face) {
    showLoader('Préparation…');
    try {
        if (face === 'recto') {
            showLoader('Génération du recto…');
            const c = await cardToCanvas(document.getElementById('cardRecto'));
            triggerDownload(await canvasToBlob(c), SLUG + '_recto.png');

        } else if (face === 'verso') {
            showLoader('Génération du verso…');
            const c = await cardToCanvas(document.getElementById('cardVerso'));
            triggerDownload(await canvasToBlob(c), SLUG + '_verso.png');

        } else {
            showLoader('Génération du recto…');
            const c1 = await cardToCanvas(document.getElementById('cardRecto'));
            const b1 = await canvasToBlob(c1);

            showLoader('Génération du verso…');
            const c2 = await cardToCanvas(document.getElementById('cardVerso'));
            const b2 = await canvasToBlob(c2);

            triggerDownload(b1, SLUG + '_recto.png');
            await new Promise(r => setTimeout(r, 400));
            triggerDownload(b2, SLUG + '_verso.png');
        }
    } catch (e) {
        alert('Erreur de génération : ' + e.message);
    } finally {
        hideLoader();
    }
}
</script>
</body>
</html>
