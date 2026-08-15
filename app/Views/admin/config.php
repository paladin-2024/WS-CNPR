<?php
$appName = $config['app_name'] ?? 'Ministère des Transports';
$appSlogan = $config['app_slogan'] ?? 'Portail Numérique';
$appLogo = $config['app_logo'] ?? '';
$carteLogoGauche = $config['carte_logo_gauche'] ?? '';
$carteLogoDroite = $config['carte_logo_droite'] ?? '';
$carteSignature = $config['carte_signature'] ?? '';
$carteTitreGauche = $config['carte_titre_gauche'] ?? 'République Démocratique du Congo';
$carteTitreDroite = $config['carte_titre_droite'] ?? 'Direction Provinciale de la CNPR';
?>
<div style="max-width:800px;margin:0 auto;">
    <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #E5EAF2;">
            <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#007FFF,#005FCC);display:flex;align-items:center;justify-content:center;">
                <i data-lucide="settings" style="width:20px;height:20px;color:white;"></i>
            </div>
            <div>
                <h2 style="margin:0;font-size:18px;font-weight:700;color:#1A2744;">Configuration de l'application</h2>
                <p style="margin:4px 0 0;font-size:13px;color:#6B7280;">Personnalisez le nom et le logo de l'application</p>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div style="background:#ECFDF5;border:1px solid #059669;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                <i data-lucide="check-circle" style="width:18px;height:18px;color:#059669;"></i>
                <span style="color:#065F46;font-size:14px;"><?= htmlspecialchars($_GET['success']) ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div style="background:#FEF2F2;border:1px solid #DC2626;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                <i data-lucide="alert-circle" style="width:18px;height:18px;color:#DC2626;"></i>
                <span style="color:#991B1B;font-size:14px;"><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_PATH ?>/admin/config/save" enctype="multipart/form-data">
                <?= \App\Core\Csrf::field() ?>
            <div style="display:grid;gap:20px;">
                <div>
                    <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Nom de l'application *</label>
                    <input type="text" name="app_name" value="<?= htmlspecialchars($appName) ?>" required
                        style="width:100%;padding:10px 14px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;outline:none;transition:border-color 0.15s;"
                        onfocus="this.style.borderColor='#007FFF'"
                        onblur="this.style.borderColor='#D1D5DB'">
                    <p style="margin:6px 0 0;font-size:12px;color:#6B7280;">Ce nom apparaîtra dans l'en-tête et le titre des pages</p>
                </div>

                <div>
                    <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Slogan</label>
                    <input type="text" name="app_slogan" value="<?= htmlspecialchars($appSlogan) ?>"
                        style="width:100%;padding:10px 14px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;outline:none;transition:border-color 0.15s;"
                        onfocus="this.style.borderColor='#007FFF'"
                        onblur="this.style.borderColor='#D1D5DB'">
                    <p style="margin:6px 0 0;font-size:12px;color:#6B7280;">Un court slogan ou description de l'application</p>
                </div>

                <div>
                    <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Logo de l'application</label>
                    
                    <?php if (!empty($appLogo)): ?>
                    <div style="background:#F9FAFB;border:1px solid #E5EAF2;border-radius:8px;padding:16px;margin-bottom:12px;display:flex;align-items:center;gap:16px;">
                        <img src="<?= BASE_PATH ?>/public/<?= htmlspecialchars($appLogo) ?>" alt="Logo actuel" style="max-width:80px;max-height:80px;object-fit:contain;">
                        <div>
                            <p style="margin:0 0 4px;font-size:13px;color:#374151;font-weight:500;">Logo actuel</p>
                            <p style="margin:0;font-size:12px;color:#6B7280;"><?= htmlspecialchars($appLogo) ?></p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div style="background:#F9FAFB;border:2px dashed #D1D5DB;border-radius:8px;padding:24px;text-align:center;margin-bottom:12px;">
                        <i data-lucide="image" style="width:32px;height:32px;color:#9CA3AF;margin-bottom:8px;"></i>
                        <p style="margin:0;font-size:13px;color:#6B7280;">Aucun logo configuré</p>
                    </div>
                    <?php endif; ?>
                    
                    <input type="file" name="app_logo" id="app_logo" accept="image/*" 
                        style="display:none;"
                        onchange="previewLogo(this)">
                    <label for="app_logo" style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:white;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;color:#374151;cursor:pointer;transition:all 0.15s;">
                        <i data-lucide="upload" style="width:16px;height:16px;"></i>
                        <?= !empty($appLogo) ? 'Remplacer le logo' : 'Télécharger un logo' ?>
                    </label>
                    <p style="margin:8px 0 0;font-size:12px;color:#6B7280;">Formats acceptés: JPG, PNG, GIF, WebP, SVG. Taille max: 2 Mo</p>
                    
                    <div id="logo-preview" style="display:none;margin-top:12px;padding:12px;background:#F0F7FF;border-radius:8px;">
                        <p style="margin:0 0 8px;font-size:13px;font-weight:500;color:#1A2744;">Aperçu du nouveau logo:</p>
                        <img id="preview-image" src="" alt="Aperçu" style="max-width:120px;max-height:120px;object-fit:contain;">
                    </div>
                </div>
            </div>

            <div style="margin-top:32px;padding-top:24px;border-top:2px solid #E5EAF2;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#059669,#047857);display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="credit-card" style="width:18px;height:18px;color:white;"></i>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:16px;font-weight:700;color:#1A2744;">Configuration de la carte brevet</h3>
                        <p style="margin:4px 0 0;font-size:13px;color:#6B7280;">Logos et signature affichés sur les cartes imprimées</p>
                    </div>
                </div>

                <div style="display:grid;gap:24px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Titre gauche de la carte</label>
                            <input type="text" name="carte_titre_gauche" value="<?= htmlspecialchars($carteTitreGauche) ?>"
                                style="width:100%;padding:10px 14px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;outline:none;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#007FFF'"
                                onblur="this.style.borderColor='#D1D5DB'">
                            <p style="margin:6px 0 0;font-size:12px;color:#6B7280;">Ex: République Démocratique du Congo</p>
                        </div>

                        <div>
                            <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Titre droite de la carte</label>
                            <input type="text" name="carte_titre_droite" value="<?= htmlspecialchars($carteTitreDroite) ?>"
                                style="width:100%;padding:10px 14px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;outline:none;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#007FFF'"
                                onblur="this.style.borderColor='#D1D5DB'">
                            <p style="margin:6px 0 0;font-size:12px;color:#6B7280;">Ex: Direction Provinciale de la CNPR</p>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Logo gauche de la carte</label>
                            
                            <?php if (!empty($carteLogoGauche)): ?>
                            <div style="background:#F9FAFB;border:1px solid #E5EAF2;border-radius:8px;padding:12px;margin-bottom:12px;display:flex;align-items:center;gap:12px;">
                                <img src="<?= BASE_PATH ?>/public/<?= htmlspecialchars($carteLogoGauche) ?>" alt="Logo gauche" style="max-width:60px;max-height:60px;object-fit:contain;">
                                <div>
                                    <p style="margin:0 0 4px;font-size:13px;color:#374151;font-weight:500;">Logo gauche actuel</p>
                                    <p style="margin:0;font-size:12px;color:#6B7280;"><?= htmlspecialchars($carteLogoGauche) ?></p>
                                </div>
                            </div>
                            <?php else: ?>
                            <div style="background:#F9FAFB;border:2px dashed #D1D5DB;border-radius:8px;padding:20px;text-align:center;margin-bottom:12px;">
                                <i data-lucide="image" style="width:28px;height:28px;color:#9CA3AF;margin-bottom:6px;"></i>
                                <p style="margin:0;font-size:13px;color:#6B7280;">Aucun logo gauche configuré</p>
                            </div>
                            <?php endif; ?>
                            
                            <input type="file" name="carte_logo_gauche" id="carte_logo_gauche" accept="image/*" 
                                style="display:none;"
                                onchange="previewImage(this, 'preview-gauche')">
                            <label for="carte_logo_gauche" style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:white;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;color:#374151;cursor:pointer;transition:all 0.15s;">
                                <i data-lucide="upload" style="width:16px;height:16px;"></i>
                                <?= !empty($carteLogoGauche) ? 'Remplacer' : 'Télécharger' ?>
                            </label>
                            
                            <div id="preview-gauche" style="display:none;margin-top:12px;padding:12px;background:#F0F7FF;border-radius:8px;">
                                <p style="margin:0 0 8px;font-size:13px;font-weight:500;color:#1A2744;">Aperçu:</p>
                                <img id="preview-gauche-image" src="" alt="Aperçu" style="max-width:80px;max-height:80px;object-fit:contain;">
                            </div>
                        </div>

                        <div>
                            <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Logo droite de la carte</label>
                            
                            <?php if (!empty($carteLogoDroite)): ?>
                            <div style="background:#F9FAFB;border:1px solid #E5EAF2;border-radius:8px;padding:12px;margin-bottom:12px;display:flex;align-items:center;gap:12px;">
                                <img src="<?= BASE_PATH ?>/public/<?= htmlspecialchars($carteLogoDroite) ?>" alt="Logo droite" style="max-width:60px;max-height:60px;object-fit:contain;">
                                <div>
                                    <p style="margin:0 0 4px;font-size:13px;color:#374151;font-weight:500;">Logo droite actuel</p>
                                    <p style="margin:0;font-size:12px;color:#6B7280;"><?= htmlspecialchars($carteLogoDroite) ?></p>
                                </div>
                            </div>
                            <?php else: ?>
                            <div style="background:#F9FAFB;border:2px dashed #D1D5DB;border-radius:8px;padding:20px;text-align:center;margin-bottom:12px;">
                                <i data-lucide="image" style="width:28px;height:28px;color:#9CA3AF;margin-bottom:6px;"></i>
                                <p style="margin:0;font-size:13px;color:#6B7280;">Aucun logo droite configuré</p>
                            </div>
                            <?php endif; ?>
                            
                            <input type="file" name="carte_logo_droite" id="carte_logo_droite" accept="image/*" 
                                style="display:none;"
                                onchange="previewImage(this, 'preview-droite')">
                            <label for="carte_logo_droite" style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:white;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;color:#374151;cursor:pointer;transition:all 0.15s;">
                                <i data-lucide="upload" style="width:16px;height:16px;"></i>
                                <?= !empty($carteLogoDroite) ? 'Remplacer' : 'Télécharger' ?>
                            </label>
                            
                            <div id="preview-droite" style="display:none;margin-top:12px;padding:12px;background:#F0F7FF;border-radius:8px;">
                                <p style="margin:0 0 8px;font-size:13px;font-weight:500;color:#1A2744;">Aperçu:</p>
                                <img id="preview-droite-image" src="" alt="Aperçu" style="max-width:80px;max-height:80px;object-fit:contain;">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Signature électronique de l'autorité</label>
                        
                        <?php if (!empty($carteSignature)): ?>
                        <div style="background:#F9FAFB;border:1px solid #E5EAF2;border-radius:8px;padding:12px;margin-bottom:12px;display:flex;align-items:center;gap:16px;">
                            <img src="<?= BASE_PATH ?>/public/<?= htmlspecialchars($carteSignature) ?>" alt="Signature" style="max-width:150px;max-height:60px;object-fit:contain;">
                            <div>
                                <p style="margin:0 0 4px;font-size:13px;color:#374151;font-weight:500;">Signature actuelle</p>
                                <p style="margin:0;font-size:12px;color:#6B7280;"><?= htmlspecialchars($carteSignature) ?></p>
                            </div>
                        </div>
                        <?php else: ?>
                        <div style="background:#F9FAFB;border:2px dashed #D1D5DB;border-radius:8px;padding:24px;text-align:center;margin-bottom:12px;">
                            <i data-lucide="pen-tool" style="width:32px;height:32px;color:#9CA3AF;margin-bottom:8px;"></i>
                            <p style="margin:0;font-size:13px;color:#6B7280;">Aucune signature configurée</p>
                        </div>
                        <?php endif; ?>
                        
                        <input type="file" name="carte_signature" id="carte_signature" accept="image/*" 
                            style="display:none;"
                            onchange="previewImage(this, 'preview-signature')">
                        <label for="carte_signature" style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:white;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;color:#374151;cursor:pointer;transition:all 0.15s;">
                            <i data-lucide="upload" style="width:16px;height:16px;"></i>
                            <?= !empty($carteSignature) ? 'Remplacer la signature' : 'Télécharger la signature' ?>
                        </label>
                        <p style="margin:8px 0 0;font-size:12px;color:#6B7280;">Formats acceptés: PNG, JPG avec fond transparent recommandé. Taille max: 2 Mo</p>
                        
                        <div id="preview-signature" style="display:none;margin-top:12px;padding:12px;background:#F0F7FF;border-radius:8px;">
                            <p style="margin:0 0 8px;font-size:13px;font-weight:500;color:#1A2744;">Aperçu de la signature:</p>
                            <img id="preview-signature-image" src="" alt="Aperçu" style="max-width:180px;max-height:80px;object-fit:contain;">
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top:28px;padding-top:20px;border-top:1px solid #E5EAF2;display:flex;justify-content:flex-end;gap:12px;">
                <a href="<?= BASE_PATH ?>/admin" style="padding:10px 20px;border:1px solid #D1D5DB;border-radius:8px;text-decoration:none;color:#374151;font-size:14px;font-weight:500;transition:all 0.15s;">
                    Annuler
                </a>
                <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,#007FFF,#005FCC);color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.15s;">
                    <i data-lucide="save" style="width:16px;height:16px;"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <div style="background:#FEF3C7;border:1px solid #F59E0B;border-radius:12px;padding:16px;display:flex;gap:12px;">
        <i data-lucide="info" style="width:20px;height:20px;color:#D97706;flex-shrink:0;margin-top:2px;"></i>
        <div>
            <p style="margin:0 0 4px;font-size:14px;font-weight:600;color:#92400E;">À savoir</p>
            <p style="margin:0;font-size:13px;color:#92400E;">Le logo téléchargé sera utilisé comme favicon du site et dans l'en-tête de l'application. Assurez-vous d'utiliser une image de bonne qualité.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    function previewLogo(input) {
        const preview = document.getElementById('logo-preview');
        const previewImage = document.getElementById('preview-image');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            if (file.size > 2 * 1024 * 1024) {
                alert('Le fichier est trop volumineux. Taille maximale: 2 Mo');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const previewImage = document.getElementById(previewId + '-image');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            if (file.size > 2 * 1024 * 1024) {
                alert('Le fichier est trop volumineux. Taille maximale: 2 Mo');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
</script>
