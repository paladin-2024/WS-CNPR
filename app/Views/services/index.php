<!-- ============================================================
     SERVICES PAGE - Nos Services
     ============================================================ -->
<section style="min-height:100vh;padding:120px 0 80px;background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);position:relative;overflow:hidden;">
    <!-- Background Pattern -->
    <svg width="100%" height="100%" style="position:absolute;inset:0;opacity:0.04;pointer-events:none;">
        <pattern id="service-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
            <circle cx="20" cy="20" r="2" fill="#007FFF"/>
        </pattern>
        <rect width="100%" height="100%" fill="url(#service-pattern)"/>
    </svg>

    <div style="max-width:1200px;margin:0 auto;padding:0 32px;position:relative;z-index:2;">
        <!-- Page Header -->
        <div style="text-align:center;margin-bottom:60px;animation:fadeInUp 0.5s ease;">
            <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;background:rgba(0,127,255,0.1);border-radius:20px;font-size:13px;font-weight:600;color:#007FFF;margin-bottom:20px;">
                <i data-lucide="layers" style="width:14px;height:14px;"></i>
                Services Disponibles
            </div>
            <h1 style="font-size:clamp(32px,5vw,48px);font-family:Poppins,sans-serif;font-weight:700;color:#1a1a2e;margin-bottom:16px;line-height:1.2;">
                Nos Services en Ligne
            </h1>
            <p style="font-size:clamp(15px,2vw,18px);color:#64748b;max-width:640px;margin:0 auto;line-height:1.7;">
                Accédez facilement à tous les services du Ministère des Transports. Démarches simplifiées, rapides et sécurisées.
            </p>
        </div>

        <!-- Services Grid -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:24px;">
            <?php foreach ($services as $index => $service): ?>
                <div style="background:white;border-radius:16px;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.04);transition:all 0.3s ease;animation:fadeInUp 0.5s <?= $index * 0.1 ?>s both ease;position:relative;overflow:hidden;"
                     onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.1)';"
                     onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 24px rgba(0,0,0,0.06)';">
                    
                    <!-- Colored Top Bar -->
                    <div style="position:absolute;top:0;left:0;right:0;height:4px;background:<?= htmlspecialchars($service['color']) ?>;"></div>
                    
                    <!-- Icon -->
                    <div style="width:56px;height:56px;border-radius:14px;background:<?= htmlspecialchars($service['color']) ?>15;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <?php 
                        $iconMap = [
                            'id-card' => 'id-card',
                            'car' => 'car',
                            'credit-card' => 'credit-card',
                            'shield-check' => 'shield-check',
                            'file-text' => 'file-text',
                            'headphones' => 'headphones'
                        ];
                        $icon = $iconMap[$service['icon']] ?? 'circle';
                        ?>
                        <i data-lucide="<?= $icon ?>" style="width:28px;height:28px;color:<?= htmlspecialchars($service['color']) ?>;"></i>
                    </div>
                    
                    <!-- Content -->
                    <h3 style="font-size:20px;font-family:Poppins,sans-serif;font-weight:600;color:#1a1a2e;margin-bottom:12px;">
                        <?= htmlspecialchars($service['title']) ?>
                    </h3>
                    <p style="font-size:15px;color:#64748b;line-height:1.6;margin-bottom:20px;">
                        <?= htmlspecialchars($service['description']) ?>
                    </p>
                    
                    <!-- Details List -->
                    <ul style="list-style:none;padding:0;margin:0 0 24px;">
                        <?php foreach ($service['details'] as $detail): ?>
                            <li style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f1f5f9;font-size:14px;color:#475569;">
                                <i data-lucide="check-circle" style="width:16px;height:16px;color:<?= htmlspecialchars($service['color']) ?>;flex-shrink:0;"></i>
                                <?= htmlspecialchars($detail) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- Action Button -->
                    <a href="#" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px 24px;background:<?= htmlspecialchars($service['color']) ?>;color:white;border:none;border-radius:10px;font-size:15px;font-weight:600;font-family:Poppins,sans-serif;text-decoration:none;cursor:pointer;transition:all 0.2s ease;"
                       onmouseover="this.style.filter='brightness(1.1)';"
                       onmouseout="this.style.filter='brightness(1)';">
                        Accéder au service
                        <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Help Section -->
        <div style="margin-top:80px;padding:48px;background:linear-gradient(135deg,#007FFF,#005FCC);border-radius:20px;text-align:center;position:relative;overflow:hidden;animation:fadeInUp 0.6s 0.5s both ease;">
            <!-- Decorative Elements -->
            <div style="position:absolute;right:-50px;top:-50px;width:200px;height:200px;background:rgba(255,255,255,0.1);border-radius:50%;"></div>
            <div style="position:absolute;left:10%;bottom:-30px;width:100px;height:100px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
            
            <div style="position:relative;z-index:2;">
                <div style="width:64px;height:64px;background:rgba(255,255,255,0.2);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
                    <i data-lucide="headphones" style="width:32px;height:32px;color:white;"></i>
                </div>
                <h3 style="font-size:clamp(22px,3vw,28px);font-family:Poppins,sans-serif;font-weight:700;color:white;margin-bottom:12px;">
                    Besoin d'aide ?
                </h3>
                <p style="font-size:16px;color:rgba(255,255,255,0.85);max-width:500px;margin:0 auto 28px;line-height:1.6;">
                    Notre équipe de support est disponible pour vous accompagner dans toutes vos démarches.
                </p>
                <a href="<?= BASE_PATH ?>/contact" style="display:inline-flex;align-items:center;gap:8px;padding:14px 32px;background:white;color:#007FFF;border:none;border-radius:10px;font-size:15px;font-weight:600;font-family:Poppins,sans-serif;text-decoration:none;cursor:pointer;transition:all 0.2s ease;"
                   onmouseover="this.style.transform='scale(1.02)';"
                   onmouseout="this.style.transform='scale(1)';">
                    <i data-lucide="message-circle" style="width:18px;height:18px;"></i>
                    Nous contacter
                </a>
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
