<!-- ============================================================
     ABOUT PAGE - À propos du Ministère
     ============================================================ -->
<section style="min-height:100vh;padding:120px 0 80px;background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);position:relative;overflow:hidden;">
    <!-- Background Pattern -->
    <svg width="100%" height="100%" style="position:absolute;inset:0;opacity:0.04;pointer-events:none;">
        <pattern id="about-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
            <circle cx="20" cy="20" r="2" fill="#005FCC"/>
        </pattern>
        <rect width="100%" height="100%" fill="url(#about-pattern)"/>
    </svg>

    <div style="max-width:1200px;margin:0 auto;padding:0 32px;position:relative;z-index:2;">
        <!-- Page Header -->
        <div style="text-align:center;margin-bottom:60px;animation:fadeInUp 0.5s ease;">
            <div style="display:inline-flex;align-items:center;gap:8px;padding:6px 16px;background:rgba(0,95,204,0.1);border-radius:20px;font-size:13px;font-weight:600;color:#005FCC;margin-bottom:20px;">
                <i data-lucide="building-2" style="width:14px;height:14px;"></i>
                À propos
            </div>
            <h1 style="font-size:clamp(32px,5vw,48px);font-family:Poppins,sans-serif;font-weight:700;color:#1a1a2e;margin-bottom:16px;line-height:1.2;">
                Ministère des Transports
            </h1>
            <p style="font-size:clamp(15px,2vw,18px);color:#64748b;max-width:640px;margin:0 auto;line-height:1.7;">
                Le Portail Numérique du Ministère des Transports garantit un accès simplifié aux services administratifs pour tous les citoyens.
            </p>
        </div>

        <!-- Stats Row -->
        <div style="display:flex;justify-content:center;gap:40px;margin-bottom:60px;flex-wrap:wrap;animation:fadeInUp 0.5s 0.1s both ease;">
            <?php foreach ($quickStats as $stat): ?>
            <div style="background:white;border-radius:16px;padding:24px 32px;box-shadow:0 4px 24px rgba(0,0,0,0.06);text-align:center;min-width:140px;">
                <div style="font-size:32px;font-weight:700;color:#005FCC;font-family:Poppins,sans-serif;"><?= htmlspecialchars($stat['value']) ?></div>
                <div style="font-size:14px;color:#64748b;margin-top:4px;"><?= htmlspecialchars($stat['label']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Missions Grid -->
        <?php if (!empty($missions)): ?>
        <h2 style="font-size:clamp(24px,3vw,32px);font-family:Poppins,sans-serif;font-weight:600;color:#1a1a2e;text-align:center;margin-bottom:40px;">
            Notre Mission
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;margin-bottom:60px;">
            <?php foreach ($missions as $index => $mission): ?>
                <div style="background:white;border-radius:16px;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.04);transition:all 0.3s ease;animation:fadeInUp 0.5s <?= $index * 0.1 ?>s both ease;position:relative;overflow:hidden;"
                     onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.1)';"
                     onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 24px rgba(0,0,0,0.06)';">
                    
                    <!-- Colored Top Bar -->
                    <div style="position:absolute;top:0;left:0;right:0;height:4px;background:<?= htmlspecialchars($mission['color']) ?>;"></div>
                    
                    <!-- Icon -->
                    <div style="width:56px;height:56px;border-radius:14px;background:<?= htmlspecialchars($mission['color']) ?>15;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <?php 
                        $iconMap = [
                            'shield' => 'shield',
                            'target' => 'target',
                            'users' => 'users',
                            'award' => 'award',
                        ];
                        $icon = $iconMap[$mission['icon']] ?? 'circle';
                        ?>
                        <i data-lucide="<?= $icon ?>" style="width:28px;height:28px;color:<?= htmlspecialchars($mission['color']) ?>;"></i>
                    </div>
                    
                    <!-- Content -->
                    <h3 style="font-size:20px;font-family:Poppins,sans-serif;font-weight:600;color:#1a1a2e;margin-bottom:12px;">
                        <?= htmlspecialchars($mission['title']) ?>
                    </h3>
                    <p style="font-size:15px;color:#64748b;line-height:1.6;">
                        <?= htmlspecialchars($mission['description']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Valeurs -->
        <?php if (!empty($valeurs)): ?>
        <div style="background:white;border-radius:20px;padding:48px;box-shadow:0 4px 24px rgba(0,0,0,0.06);margin-bottom:60px;animation:fadeInUp 0.5s 0.4s both ease;">
            <h2 style="font-size:clamp(24px,3vw,32px);font-family:Poppins,sans-serif;font-weight:600;color:#1a1a2e;text-align:center;margin-bottom:32px;">
                Nos Valeurs
            </h2>
            <div style="display:flex;flex-wrap:wrap;gap:16px;justify-content:center;">
                <?php foreach ($valeurs as $valeur): ?>
                <span style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);border:1px solid #bae6fd;padding:12px 24px;border-radius:30px;font-size:15px;font-weight:500;color:#0369a1;font-family:Poppins,sans-serif;">
                    <?= htmlspecialchars($valeur) ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Équipe Dirigeante -->
        <?php if (!empty($equipe)): ?>
        <h2 style="font-size:clamp(24px,3vw,32px);font-family:Poppins,sans-serif;font-weight:600;color:#1a1a2e;text-align:center;margin-bottom:40px;">
            Équipe Dirigeante
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:24px;margin-bottom:60px;">
            <?php foreach ($equipe as $membre): ?>
            <div style="background:white;border-radius:16px;padding:32px;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.04);transition:all 0.3s ease;"
                 onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.1)';"
                 onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 24px rgba(0,0,0,0.06)';">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#005FCC,#004499);color:white;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;font-family:Poppins,sans-serif;margin:0 auto 16px;">
                    <?= htmlspecialchars($membre['initiales']) ?>
                </div>
                <h4 style="font-size:18px;font-weight:600;color:#1a1a2e;font-family:Poppins,sans-serif;margin:0 0 6px 0;">
                    <?= htmlspecialchars($membre['nom']) ?>
                </h4>
                <p style="font-size:14px;color:#64748b;margin:0;"><?= htmlspecialchars($membre['poste']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Contacts -->
        <?php if (!empty($contacts)): ?>
        <h2 style="font-size:clamp(24px,3vw,32px);font-family:Poppins,sans-serif;font-weight:600;color:#1a1a2e;text-align:center;margin-bottom:40px;">
           Contactez-nous
        </h2>
        <div style="display:flex;justify-content:center;gap:24px;flex-wrap:wrap;margin-bottom:60px;">
            <?php foreach ($contacts as $contact): ?>
            <div style="background:white;border-radius:16px;padding:24px;display:flex;align-items:center;gap:16px;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid rgba(0,0,0,0.04);min-width:280px;">
                <div style="width:56px;height:56px;border-radius:14px;background:<?= htmlspecialchars($contact['color']) ?>15;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <?php 
                    $contactIconMap = [
                        'map-pin' => 'map-pin',
                        'phone' => 'phone',
                        'mail' => 'mail',
                    ];
                    $contactIcon = $contactIconMap[$contact['icon']] ?? 'circle';
                    ?>
                    <i data-lucide="<?= $contactIcon ?>" style="width:24px;height:24px;color:<?= htmlspecialchars($contact['color']) ?>;"></i>
                </div>
                <div>
                    <div style="font-size:12px;color:#64748b;margin-bottom:4px;font-weight:500;"><?= htmlspecialchars($contact['label']) ?></div>
                    <div style="font-size:15px;color:#1a1a2e;font-weight:500;font-family:Poppins,sans-serif;"><?= htmlspecialchars($contact['value']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Faits institutionnels -->
        <?php if (!empty($institutionFacts)): ?>
        <div style="background:linear-gradient(135deg,#FEF3C7,#FDE68A);border-radius:20px;padding:40px;text-align:center;animation:fadeInUp 0.5s 0.6s both ease;">
            <h3 style="font-size:20px;font-weight:600;color:#92400E;font-family:Poppins,sans-serif;margin-bottom:20px;">
                À propos de l'institution
            </h3>
            <div style="display:flex;flex-wrap:wrap;gap:20px;justify-content:center;">
                <?php foreach ($institutionFacts as $fact): ?>
                <span style="display:flex;align-items:center;gap:8px;font-size:15px;color:#B45309;">
                    <i data-lucide="check-circle" style="width:18px;height:18px;color:#059669;"></i>
                    <?= htmlspecialchars($fact) ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
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