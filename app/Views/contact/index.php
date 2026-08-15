<!-- ============================================================
     CONTACT PAGE - Nous contacter
     ============================================================ -->
<section style="min-height:100vh;padding:120px 0 80px;background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);position:relative;overflow:hidden;">
    <!-- Background Pattern -->
    <svg width="100%" height="100%" style="position:absolute;inset:0;opacity:0.04;pointer-events:none;">
        <pattern id="contact-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
            <circle cx="20" cy="20" r="2" fill="#005FCC"/>
        </pattern>
        <rect width="100%" height="100%" fill="url(#contact-pattern)"/>
    </svg>

    <div style="max-width:1200px;margin:0 auto;padding:0 32px;position:relative;z-index:2;">
        <!-- Page Header -->
        <div style="text-align:center;margin-bottom:60px;animation:fadeInUp 0.5s ease;">
            <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;background:rgba(0,95,204,0.1);border-radius:20px;font-size:13px;font-weight:600;color:#005FCC;margin-bottom:20px;">
                <i data-lucide="message-circle" style="width:14px;height:14px;"></i>
                Contact
            </div>
            <h1 style="font-size:clamp(32px,5vw,48px);font-family:Poppins,sans-serif;font-weight:700;color:#1a1a2e;margin-bottom:16px;line-height:1.2;">
                Nous contacter
            </h1>
            <p style="font-size:clamp(15px,2vw,18px);color:#64748b;max-width:640px;margin:0 auto;line-height:1.7;">
                Une question ? Notre équipe est disponible pour vous répondre.
            </p>
        </div>

        <!-- Contact Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;margin-bottom:60px;">
            <?php foreach ($contacts as $index => $contact): ?>
                <div style="background:white;border-radius:16px;padding:32px;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.04);transition:all 0.3s ease;animation:fadeInUp 0.5s <?= $index * 0.1 ?>s both ease;"
                     onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.1)';"
                     onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 24px rgba(0,0,0,0.06)';">
                    
                    <!-- Icon -->
                    <div style="width:64px;height:64px;border-radius:16px;background:<?= htmlspecialchars($contact['color']) ?>15;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <i data-lucide="<?= htmlspecialchars($contact['icon']) ?>" style="width:28px;height:28px;color:<?= htmlspecialchars($contact['color']) ?>;"></i>
                    </div>
                    
                    <!-- Content -->
                    <h3 style="font-size:16px;font-weight:600;color:#64748b;margin:0 0 8px 0;text-transform:uppercase;letter-spacing:0.5px;">
                        <?= htmlspecialchars($contact['label']) ?>
                    </h3>
                    <p style="font-size:16px;color:#1a1a2e;font-weight:500;font-family:Poppins,sans-serif;margin:0;line-height:1.5;">
                        <?= htmlspecialchars($contact['value']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Contact Form & FAQ -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:32px;margin-bottom:60px;">
            <!-- Contact Form -->
            <div style="background:white;border-radius:20px;padding:40px;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.04);animation:fadeInUp 0.5s 0.3s both ease;">
                <h2 style="font-size:24px;font-family:Poppins,sans-serif;font-weight:600;color:#1a1a2e;margin:0 0 24px 0;display:flex;align-items:center;gap:12px;">
                    <i data-lucide="send" style="width:24px;height:24px;color:#005FCC;"></i>
                    Envoyez un message
                </h2>

                <?php if (!empty($flash)): ?>
                <div style="padding:14px 18px;border-radius:10px;margin-bottom:24px;<?= strpos($flash, 'succès') !== false ? 'background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;' : 'background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;' ?>">
                    <?= htmlspecialchars($flash) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_PATH ?>/contact/send">
                <?= \App\Core\Csrf::field() ?>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;font-family:Poppins,sans-serif;">Nom <span style="color:#dc2626;">*</span></label>
                            <input type="text" name="nom" required style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:10px;font-size:15px;color:#1e293b;background:#f8fafc;transition:all 0.2s ease;font-family:inherit;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#005FCC';this.style.background='white';this.style.boxShadow='0 0 0 4px rgba(0,95,204,0.1)';"
                                   onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none';"
                                   placeholder="Votre nom">
                        </div>
                        <div>
                            <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;font-family:Poppins,sans-serif;">Email <span style="color:#dc2626;">*</span></label>
                            <input type="email" name="email" required style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:10px;font-size:15px;color:#1e293b;background:#f8fafc;transition:all 0.2s ease;font-family:inherit;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='#005FCC';this.style.background='white';this.style.boxShadow='0 0 0 4px rgba(0,95,204,0.1)';"
                                   onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none';"
                                   placeholder="votre@email.com">
                        </div>
                    </div>
                    
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;font-family:Poppins,sans-serif;">Sujet <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="sujet" required style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:10px;font-size:15px;color:#1e293b;background:#f8fafc;transition:all 0.2s ease;font-family:inherit;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#005FCC';this.style.background='white';this.style.boxShadow='0 0 0 4px rgba(0,95,204,0.1)';"
                               onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none';"
                               placeholder="Sujet de votre message">
                    </div>
                    
                    <div style="margin-bottom:24px;">
                        <label style="display:block;font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;font-family:Poppins,sans-serif;">Message <span style="color:#dc2626;">*</span></label>
                        <textarea name="message" rows="5" required style="width:100%;padding:14px 16px;border:2px solid #e2e8f0;border-radius:10px;font-size:15px;color:#1e293b;background:#f8fafc;transition:all 0.2s ease;font-family:inherit;box-sizing:border-box;resize:vertical;"
                                  onfocus="this.style.borderColor='#005FCC';this.style.background='white';this.style.boxShadow='0 0 0 4px rgba(0,95,204,0.1)';"
                                  onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none';"
                                  placeholder="Votre message..."></textarea>
                    </div>
                    
                    <button type="submit" style="width:100%;padding:16px 24px;background:linear-gradient(135deg,#005FCC,#004499);color:white;border:none;border-radius:12px;font-size:16px;font-weight:600;font-family:Poppins,sans-serif;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;transition:all 0.2s ease;box-shadow:0 4px 12px rgba(0,95,204,0.3);"
                            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(0,95,204,0.4)';"
                            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(0,95,204,0.3)';">
                        <i data-lucide="send" style="width:18px;height:18px;"></i>
                        Envoyer le message
                    </button>
                </form>
            </div>

            <!-- FAQ -->
            <div style="animation:fadeInUp 0.5s 0.4s both ease;">
                <h2 style="font-size:24px;font-family:Poppins,sans-serif;font-weight:600;color:#1a1a2e;margin:0 0 24px 0;display:flex;align-items:center;gap:12px;">
                    <i data-lucide="help-circle" style="width:24px;height:24px;color:#005FCC;"></i>
                    Questions fréquentes
                </h2>
                
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <?php foreach ($faq as $index => $item): ?>
                    <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,0.04);border:1px solid rgba(0,0,0,0.04);transition:all 0.2s ease;"
                         onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)';"
                         onmouseout="this.style.boxShadow='0 2px 12px rgba(0,0,0,0.04)';">
                        <h4 style="font-size:15px;font-weight:600;color:#1a1a2e;margin:0 0 10px 0;font-family:Poppins,sans-serif;display:flex;align-items:flex-start;gap:10px;">
                            <i data-lucide="message-square" style="width:18px;height:18px;color:#005FCC;flex-shrink:0;margin-top:2px;"></i>
                            <?= htmlspecialchars($item['question']) ?>
                        </h4>
                        <p style="font-size:14px;color:#64748b;margin:0;line-height:1.6;padding-left:28px;">
                            <?= htmlspecialchars($item['answer']) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>