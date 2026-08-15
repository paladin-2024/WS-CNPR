<?php
$conducteur = $conducteur ?? null;
$isAuthentique = $conducteur && $conducteur['statut_brevet'] === 'imprime' && $conducteur['statut'] === 'actif';
$isFound = $conducteur !== null;
?>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.verif-root {
    min-height: 100vh;
    background: linear-gradient(135deg, #F0F4F8 0%, #E2E8F0 100%);
    font-family: 'Poppins', sans-serif;
    padding: 20px;
}

.verif-container {
    max-width: 520px;
    margin: 0 auto;
    animation: fadeInUp 0.5s ease;
}

/* Header */
.verif-header {
    text-align: center;
    margin-bottom: 24px;
}

.verif-logo {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, #005FCC, #003A8C);
    border-radius: 14px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 700; color: white;
    margin-bottom: 12px;
    box-shadow: 0 4px 16px rgba(0,95,204,0.3);
}

.verif-header h1 {
    font-size: 20px; font-weight: 700; color: #1A2744; margin: 0 0 4px 0;
}

.verif-header p {
    font-size: 13px; color: #64748B; margin: 0;
}

/* Drapeau */
.flag-bar {
    display: flex; height: 4px; border-radius: 2px; overflow: hidden;
    width: 60px; margin: 12px auto 0;
}
.flag-bar span:nth-child(1) { flex: 1; background: #007FFF; }
.flag-bar span:nth-child(2) { flex: 1; background: #FCD116; }
.flag-bar span:nth-child(3) { flex: 1; background: #CE1021; }

/* Status Banner */
.status-banner {
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.status-banner svg { width: 32px; height: 32px; flex-shrink: 0; }

.status-banner.authentic {
    background: linear-gradient(135deg, #ECFDF5, #D1FAE5);
    border: 2px solid #10B981;
    color: #065F46;
}

.status-banner.not-found {
    background: linear-gradient(135deg, #FEF2F2, #FEE2E2);
    border: 2px solid #EF4444;
    color: #991B1B;
}

.status-banner.not-printed {
    background: linear-gradient(135deg, #FFFBEB, #FEF3C7);
    border: 2px solid #F59E0B;
    color: #92400E;
}

.status-text h3 { margin: 0; font-size: 16px; font-weight: 700; }
.status-text p { margin: 4px 0 0 0; font-size: 12px; }

/* Card */
.verif-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 20px;
}

.verif-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    align-items: center;
    gap: 16px;
}

.verif-photo {
    width: 72px; height: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #E2E8F0;
    flex-shrink: 0;
}

.verif-photo-placeholder {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #005FCC, #003A8C);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; font-weight: 700; color: white;
    flex-shrink: 0;
}

.verif-name { font-size: 20px; font-weight: 700; color: #1A2744; margin: 0 0 4px 0; }

.verif-permis {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px;
    background: #EFF6FF; color: #3B82F6;
    border-radius: 20px; font-size: 12px; font-weight: 600;
}

.verif-card-body { padding: 20px 24px; }

.verif-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.verif-field {}
.verif-field-label {
    font-size: 11px; font-weight: 600; color: #94A3B8;
    text-transform: uppercase; letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.verif-field-value {
    font-size: 14px; font-weight: 500; color: #1A2744;
}

.verif-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
}
.verif-badge.actif { background: #ECFDF5; color: #059669; }
.verif-badge.suspendu { background: #FEF2F2; color: #DC2626; }
.verif-badge.expire { background: #FFFBEB; color: #D97706; }
.verif-badge.imprime { background: #ECFDF5; color: #10B981; }
.verif-badge.en_cours { background: #FFFBEB; color: #F59E0B; }
.verif-badge.nouveau { background: #EEF2FF; color: #6366F1; }

/* Fraud Report Section */
.fraud-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 20px;
}

.fraud-header {
    padding: 16px 24px;
    background: linear-gradient(135deg, #FEF2F2, #FEE2E2);
    border-bottom: 1px solid #FECACA;
    display: flex; align-items: center; gap: 10px;
    cursor: pointer;
    user-select: none;
}

.fraud-header svg { width: 20px; height: 20px; color: #DC2626; flex-shrink: 0; }
.fraud-header h3 { margin: 0; font-size: 15px; font-weight: 600; color: #991B1B; flex: 1; }

.fraud-header .chevron {
    width: 18px; height: 18px; color: #DC2626;
    transition: transform 0.3s;
}
.fraud-header.open .chevron { transform: rotate(180deg); }

.fraud-body {
    padding: 20px 24px;
    display: none;
}
.fraud-body.open { display: block; }

.fraud-form { display: flex; flex-direction: column; gap: 14px; }

.fraud-group { display: flex; flex-direction: column; gap: 4px; }
.fraud-group label {
    font-size: 12px; font-weight: 600; color: #374151;
}
.fraud-group input,
.fraud-group textarea {
    padding: 10px 12px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-size: 14px; font-family: inherit;
    outline: none; transition: border-color 0.2s;
}
.fraud-group input:focus,
.fraud-group textarea:focus { border-color: #DC2626; }
.fraud-group textarea { resize: vertical; min-height: 80px; }

.fraud-photo-area {
    border: 2px dashed #E2E8F0;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    position: relative;
}
.fraud-photo-area:hover { border-color: #DC2626; background: #FEF2F2; }
.fraud-photo-area svg { width: 32px; height: 32px; color: #94A3B8; margin-bottom: 8px; }
.fraud-photo-area p { margin: 0; font-size: 13px; color: #64748B; }
.fraud-photo-area small { color: #94A3B8; font-size: 11px; }
.fraud-photo-area input { display: none; }

.fraud-preview {
    margin-top: 10px;
    display: none;
}
.fraud-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    border: 1px solid #E2E8F0;
}

.fraud-msg {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    display: none;
}
.fraud-msg.error { background: #FEF2F2; color: #DC2626; }
.fraud-msg.success { background: #ECFDF5; color: #059669; }

.btn-fraud {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 8px; padding: 12px 20px;
    background: #DC2626; color: white;
    border: none; border-radius: 10px;
    font-size: 14px; font-weight: 600; font-family: inherit;
    cursor: pointer; transition: background 0.2s;
}
.btn-fraud:hover { background: #B91C1C; }
.btn-fraud:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-fraud svg { width: 18px; height: 18px; }

/* Footer */
.verif-footer {
    text-align: center;
    padding: 16px;
    font-size: 11px;
    color: #94A3B8;
}

@media (max-width: 500px) {
    .verif-grid { grid-template-columns: 1fr; }
    .verif-card-header { flex-direction: column; text-align: center; }
}
</style>

<div class="verif-root">
    <div class="verif-container">

        <!-- Header -->
        <div class="verif-header">
            <div class="verif-logo">MT</div>
            <h1>Vérification de Brevet</h1>
            <p>Ministère Provincial des Transports</p>
            <div class="flag-bar"><span></span><span></span><span></span></div>
        </div>

        <?php if (!$isFound): ?>
            <!-- Non trouvé -->
            <div class="status-banner not-found">
                <i data-lucide="x"></i>
                <div class="status-text">
                    <h3>Brevet non trouvé</h3>
                    <p>Aucun conducteur ne correspond à cet identifiant. Ce brevet pourrait être frauduleux.</p>
                </div>
            </div>

            <!-- Signalement pour brevet introuvable -->
            <div class="fraud-section">
                <div class="fraud-header open" onclick="toggleFraud()">
                    <i data-lucide="circle-alert"></i>
                    <h3>Signaler un cas de fraude</h3>
                    <i data-lucide="chevron-down" class="chevron"></i>
                </div>
                <div class="fraud-body open">
                    <form class="fraud-form" id="fraudForm" enctype="multipart/form-data">
                <?= \App\Core\Csrf::field() ?>
                        <input type="hidden" name="conducteur_id" value="0">
                        <div id="fraudMsg" class="fraud-msg"></div>
                        <div class="fraud-group">
                            <label>Votre nom complet *</label>
                            <input type="text" name="nom_verificateur" required placeholder="Ex: Jean Kabongo">
                        </div>
                        <div class="fraud-group">
                            <label>Votre téléphone</label>
                            <input type="tel" name="telephone_verificateur" placeholder="Ex: +243 ...">
                        </div>
                        <div class="fraud-group">
                            <label>Description de la fraude *</label>
                            <textarea name="description" required placeholder="Décrivez les circonstances : lieu, date, observations..."></textarea>
                        </div>
                        <div class="fraud-group">
                            <label>Photo du brevet suspect</label>
                            <div class="fraud-photo-area" onclick="document.getElementById('fraudPhoto').click()">
                                <i data-lucide="camera"></i>
                                <p>Appuyez pour prendre une photo</p>
                                <small>JPEG, PNG — max 5 Mo</small>
                                <input type="file" id="fraudPhoto" name="photo" accept="image/*" capture="environment" onchange="previewPhoto(this)">
                            </div>
                            <div class="fraud-preview" id="fraudPreview">
                                <img id="fraudPreviewImg" src="" alt="Aperçu">
                            </div>
                        </div>
                        <button type="submit" class="btn-fraud" id="fraudSubmitBtn">
                            <i data-lucide="circle-alert"></i>
                            Envoyer le signalement
                        </button>
                    </form>
                </div>
            </div>

        <?php elseif ($isAuthentique): ?>
            <!-- Authentique -->
            <div class="status-banner authentic" style="animation: pulse 2s ease-in-out;">
                <i data-lucide="shield-check"></i>
                <div class="status-text">
                    <h3>✓ Brevet authentique</h3>
                    <p>Ce brevet est vérifié et valide</p>
                </div>
            </div>

        <?php else: ?>
            <!-- Non imprimé ou suspendu -->
            <div class="status-banner not-printed">
                <i data-lucide="circle-alert"></i>
                <div class="status-text">
                    <h3>⚠ Brevet non validé</h3>
                    <p>
                        <?php if ($conducteur['statut'] !== 'actif'): ?>
                            Ce conducteur est actuellement <strong><?= htmlspecialchars($conducteur['statut']) ?></strong>.
                        <?php elseif ($conducteur['statut_brevet'] === 'nouveau'): ?>
                            Ce brevet n'a pas encore été imprimé.
                        <?php elseif ($conducteur['statut_brevet'] === 'en_cours_impression'): ?>
                            Ce brevet est en cours d'impression.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isFound): ?>
            <!-- Driver Card -->
            <div class="verif-card">
                <div class="verif-card-header">
                    <?php
                    $initials = strtoupper(mb_substr($conducteur['prenom'] ?? '', 0, 1) . mb_substr($conducteur['nom'] ?? '', 0, 1));
                    $photoPath = $conducteur['photo_url'] ? BASE_PATH . '/public/' . $conducteur['photo_url'] : null;
                    ?>
                    <?php if ($photoPath): ?>
                        <img class="verif-photo" src="<?= htmlspecialchars($photoPath) ?>" alt="Photo">
                    <?php else: ?>
                        <div class="verif-photo-placeholder"><?= htmlspecialchars($initials) ?></div>
                    <?php endif; ?>
                    <div>
                        <h2 class="verif-name"><?= htmlspecialchars(($conducteur['prenom'] ?? '') . ' ' . ($conducteur['nom'] ?? '')) ?></h2>
                        <span class="verif-permis">
                            <i data-lucide="credit-card" style="width:14px;height:14px;"></i>
                            <?= htmlspecialchars($conducteur['numero_permis'] ?? '-') ?> — Cat. <?= htmlspecialchars($conducteur['categorie_permis'] ?? '-') ?>
                        </span>
                    </div>
                </div>
                <div class="verif-card-body">
                    <div class="verif-grid">
                        <div class="verif-field">
                            <div class="verif-field-label">Date de naissance</div>
                            <div class="verif-field-value"><?= $conducteur['date_naissance'] ? date('d/m/Y', strtotime($conducteur['date_naissance'])) : '-' ?></div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Lieu de naissance</div>
                            <div class="verif-field-value"><?= htmlspecialchars($conducteur['lieu_naissance'] ?? '-') ?></div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Téléphone</div>
                            <div class="verif-field-value"><?= htmlspecialchars($conducteur['telephone'] ?? '-') ?></div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Adresse</div>
                            <div class="verif-field-value"><?= htmlspecialchars($conducteur['adresse'] ?? '-') ?></div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Association</div>
                            <div class="verif-field-value"><?= htmlspecialchars($conducteur['association'] ?? '-') ?></div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Syndicat</div>
                            <div class="verif-field-value"><?= htmlspecialchars($conducteur['syndicat'] ?? '-') ?></div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Enregistrement</div>
                            <div class="verif-field-value"><?= $conducteur['date_enregistrement'] ? date('d/m/Y', strtotime($conducteur['date_enregistrement'])) : '-' ?></div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Expiration permis</div>
                            <div class="verif-field-value">
                                <?php
                                $expPermis = $conducteur['date_expiration_permis'] ?? null;
                                if ($expPermis) {
                                    $isExpired = strtotime($expPermis) < time();
                                    echo '<span style="color:' . ($isExpired ? '#DC2626' : '#059669') . ';font-weight:600;">' . date('d/m/Y', strtotime($expPermis)) . '</span>';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Statut conducteur</div>
                            <div class="verif-field-value">
                                <span class="verif-badge <?= htmlspecialchars($conducteur['statut'] ?? '') ?>">
                                    <?= htmlspecialchars(ucfirst($conducteur['statut'] ?? '-')) ?>
                                </span>
                            </div>
                        </div>
                        <div class="verif-field">
                            <div class="verif-field-label">Statut brevet</div>
                            <div class="verif-field-value">
                                <?php
                                $sb = $conducteur['statut_brevet'] ?? 'nouveau';
                                $sbLabels = ['nouveau' => 'Nouveau', 'en_cours_impression' => 'En cours', 'imprime' => 'Imprimé'];
                                $sbClasses = ['nouveau' => 'nouveau', 'en_cours_impression' => 'en_cours', 'imprime' => 'imprime'];
                                ?>
                                <span class="verif-badge <?= $sbClasses[$sb] ?? '' ?>">
                                    <?= htmlspecialchars($sbLabels[$sb] ?? $sb) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fraud Report -->
            <div class="fraud-section">
                <div class="fraud-header" onclick="toggleFraud()">
                    <i data-lucide="circle-alert"></i>
                    <h3>Ce brevet n'est pas authentique ?</h3>
                    <i data-lucide="chevron-down" class="chevron"></i>
                </div>
                <div class="fraud-body" id="fraudBody">
                    <form class="fraud-form" id="fraudForm" enctype="multipart/form-data">
                <?= \App\Core\Csrf::field() ?>
                        <input type="hidden" name="conducteur_id" value="<?= (int)$conducteur['id'] ?>">
                        <div id="fraudMsg" class="fraud-msg"></div>
                        <div class="fraud-group">
                            <label>Votre nom complet *</label>
                            <input type="text" name="nom_verificateur" required placeholder="Ex: Jean Kabongo">
                        </div>
                        <div class="fraud-group">
                            <label>Votre téléphone</label>
                            <input type="tel" name="telephone_verificateur" placeholder="Ex: +243 ...">
                        </div>
                        <div class="fraud-group">
                            <label>Description de la fraude *</label>
                            <textarea name="description" required placeholder="Décrivez les circonstances : lieu, date, observations sur le brevet suspect..."></textarea>
                        </div>
                        <div class="fraud-group">
                            <label>Photo du brevet suspect</label>
                            <div class="fraud-photo-area" onclick="document.getElementById('fraudPhoto').click()">
                                <i data-lucide="camera"></i>
                                <p>Appuyez pour prendre une photo</p>
                                <small>JPEG, PNG — max 5 Mo</small>
                                <input type="file" id="fraudPhoto" name="photo" accept="image/*" capture="environment" onchange="previewPhoto(this)">
                            </div>
                            <div class="fraud-preview" id="fraudPreview">
                                <img id="fraudPreviewImg" src="" alt="Aperçu">
                            </div>
                        </div>
                        <button type="submit" class="btn-fraud" id="fraudSubmitBtn">
                            <i data-lucide="circle-alert"></i>
                            Envoyer le signalement
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="verif-footer">
            © <?= date('Y') ?> Ministère Provincial des Transports — Vérification officielle
        </div>
    </div>
</div>

<script>
const BASE_PATH = '<?= BASE_PATH ?>';

function toggleFraud() {
    const header = document.querySelector('.fraud-header');
    const body = document.querySelector('.fraud-body');
    header.classList.toggle('open');
    body.classList.toggle('open');
}

function previewPhoto(input) {
    const preview = document.getElementById('fraudPreview');
    const img = document.getElementById('fraudPreviewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

const fraudForm = document.getElementById('fraudForm');
if (fraudForm) {
    fraudForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('fraudSubmitBtn');
        const msg = document.getElementById('fraudMsg');

        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="refresh-cw" style="width:18px;height:18px;animation:spin 1s linear infinite;"></i> Envoi en cours...';
        if (window.lucide) lucide.createIcons();

        try {
            const formData = new FormData(this);
            const response = await fetch(BASE_PATH + '/verification/signaler', {
                method: 'POST',
                headers: { 'X-CSRF-Token': CSRF_TOKEN },
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                msg.className = 'fraud-msg success';
                msg.textContent = '✓ Signalement envoyé avec succès. Merci pour votre vigilance.';
                msg.style.display = 'block';
                btn.style.display = 'none';
                this.querySelectorAll('input:not([type=hidden]), textarea').forEach(el => el.disabled = true);
            } else {
                msg.className = 'fraud-msg error';
                msg.textContent = result.error || 'Erreur lors de l\'envoi';
                msg.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="circle-alert" style="width:18px;height:18px;"></i> Envoyer le signalement';
                if (window.lucide) lucide.createIcons();
            }
        } catch (error) {
            msg.className = 'fraud-msg error';
            msg.textContent = 'Erreur de connexion';
            msg.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="circle-alert" style="width:18px;height:18px;"></i> Envoyer le signalement';
            if (window.lucide) lucide.createIcons();
        }
    });
}
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
