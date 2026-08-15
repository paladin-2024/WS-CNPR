<!-- ============================================================
     HERO CAROUSEL
     ============================================================ -->
<section id="hero-carousel" style="min-height:100vh;display:flex;align-items:center;position:relative;overflow:hidden;transition:background 0.7s ease;">
    <!-- Pattern SVGs -->
    <svg id="hero-pattern-dots" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;opacity:0.07;pointer-events:none;">
        <pattern id="pdots" x="0" y="0" width="30" height="30" patternUnits="userSpaceOnUse">
            <circle cx="3" cy="3" r="3" fill="white"/>
        </pattern>
        <rect width="100%" height="100%" fill="url(#pdots)"/>
    </svg>
    <svg id="hero-pattern-grid" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;opacity:0.06;pointer-events:none;display:none;">
        <pattern id="pgrid" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
            <path d="M40 0L0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
        </pattern>
        <rect width="100%" height="100%" fill="url(#pgrid)"/>
    </svg>
    <svg id="hero-pattern-circles" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;opacity:0.06;pointer-events:none;display:none;">
        <pattern id="pcirc" x="0" y="0" width="60" height="60" patternUnits="userSpaceOnUse">
            <circle cx="30" cy="30" r="25" fill="none" stroke="white" stroke-width="1"/>
        </pattern>
        <rect width="100%" height="100%" fill="url(#pcirc)"/>
    </svg>

    <!-- Decorative circles -->
    <div id="hero-circle-1" style="position:absolute;right:-10%;top:-10%;width:55vw;height:55vw;border-radius:50%;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);pointer-events:none;transition:all 0.7s;"></div>
    <div id="hero-circle-2" style="position:absolute;right:5%;bottom:-15%;width:35vw;height:35vw;border-radius:50%;pointer-events:none;transition:all 0.7s;"></div>

    <!-- Large decorative icon -->
    <div id="hero-deco-icon" style="position:absolute;right:8%;top:50%;transform:translateY(-50%);opacity:0.08;pointer-events:none;transition:all 0.5s;">
        <i data-lucide="id-card" style="width:320px;height:320px;stroke-width:0.5;color:white;"></i>
    </div>

    <!-- Content -->
    <div style="max-width:1200px;margin:0 auto;padding:0 32px;width:100%;position:relative;z-index:2;">
        <div style="max-width:680px;" id="hero-content">
            <div id="hero-badge" style="display:inline-flex;align-items:center;gap:8px;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;font-family:Poppins,sans-serif;margin-bottom:24px;animation:fadeInUp 0.5s ease;">
                <i data-lucide="id-card" style="width:14px;height:14px;"></i>
                <span id="hero-badge-text"></span>
            </div>
            <h1 id="hero-title" style="color:white;font-size:clamp(32px,5.5vw,64px);font-family:Poppins,sans-serif;font-weight:700;line-height:1.15;margin-bottom:20px;white-space:pre-line;animation:fadeInUp 0.55s 0.1s both ease;"></h1>
            <p id="hero-subtitle" style="color:rgba(255,255,255,0.75);font-size:clamp(15px,2vw,19px);line-height:1.7;margin-bottom:36px;max-width:560px;animation:fadeInUp 0.6s 0.2s both ease;"></p>
            <div id="hero-cta" style="display:flex;gap:14px;flex-wrap:wrap;animation:fadeInUp 0.65s 0.3s both ease;">
                <a id="hero-cta-primary" href="#" style="display:inline-flex;align-items:center;gap:8px;padding:14px 30px;border-radius:10px;font-weight:700;font-family:Poppins,sans-serif;font-size:15px;text-decoration:none;transition:transform 0.2s,box-shadow 0.2s;">
                    <span id="hero-cta-primary-label"></span>
                    <i data-lucide="chevron-right" style="width:16px;height:16px;stroke-width:2.5;"></i>
                </a>
                <a id="hero-cta-secondary" href="#" style="display:inline-flex;align-items:center;gap:8px;padding:14px 26px;background:rgba(255,255,255,0.1);backdrop-filter:blur(6px);color:white;border:1px solid rgba(255,255,255,0.25);border-radius:10px;font-weight:500;font-family:Poppins,sans-serif;font-size:15px;text-decoration:none;transition:background 0.2s;">
                    <span id="hero-cta-secondary-label"></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation arrows -->
    <button id="hero-prev" aria-label="Slide précédent" style="position:absolute;left:24px;top:50%;transform:translateY(-50%);width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.2);color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;">
        <i data-lucide="chevron-left" style="width:22px;height:22px;"></i>
    </button>
    <button id="hero-next" aria-label="Slide suivant" style="position:absolute;right:24px;top:50%;transform:translateY(-50%);width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.2);color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;">
        <i data-lucide="chevron-right" style="width:22px;height:22px;"></i>
    </button>

    <!-- Dots -->
    <div id="hero-dots" style="position:absolute;bottom:36px;left:50%;transform:translateX(-50%);display:flex;gap:10px;z-index:10;"></div>

    <!-- Counter -->
    <div id="hero-counter" style="position:absolute;bottom:36px;right:32px;color:rgba(255,255,255,0.4);font-size:13px;font-family:Poppins,sans-serif;z-index:10;"></div>

    <!-- Flag band bottom -->
    <div style="position:absolute;bottom:0;left:0;right:0;height:4px;display:flex;">
        <div style="flex:1;background:#007FFF;"></div>
        <div style="flex:1;background:#FCD116;"></div>
        <div style="flex:1;background:#CE1021;"></div>
    </div>
</section>

<script>
(function() {
    var heroSlides = [
        {
            id: 1,
            badge: 'Portail Officiel',
            title: "Obtenez votre\nBrévet de recyclage en ligne",
            subtitle: "Obtenez votre Brévet de recyclage en quelques étapes simples, depuis chez vous ou au bureau.",
            cta: { label: 'Commencer maintenant', href: '<?= BASE_PATH ?>/login' },
            ctaSecondary: { label: 'En savoir plus', href: '<?= BASE_PATH ?>/services' },
            icon: 'id-card',
            accent: '#FCD116',
            bg: 'linear-gradient(140deg, #003A8C 0%, #005FCC 50%, #007FFF 100%)',
            pattern: 'dots'
        },
        {
            id: 2,
            badge: 'Immatriculation',
            title: "Immatriculez votre\nVéhicule en Ligne",
            subtitle: "Déposez votre dossier d'immatriculation de transport en ligne. Rapide, sécurisé et sans déplacement.",
            cta: { label: 'Immatriculer un véhicule', href: '<?= BASE_PATH ?>/services' },
            ctaSecondary: { label: 'Voir les tarifs', href: '<?= BASE_PATH ?>/services' },
            icon: 'car',
            accent: '#CE1021',
            bg: 'linear-gradient(140deg, #1a0a0a 0%, #6B0010 50%, #CE1021 100%)',
            pattern: 'grid'
        },
        {
            id: 3,
            badge: 'Paiement Fiscal',
            title: "Payez vos Taxes\nde Transport",
            subtitle: "Acquittez vos taxes de circulation et d'exploitation directement en ligne. Mobile money, carte ou virement.",
            cta: { label: 'Payer mes taxes', href: '<?= BASE_PATH ?>/services' },
            ctaSecondary: { label: 'Consulter mon compte', href: '<?= BASE_PATH ?>/login' },
            icon: 'credit-card',
            accent: '#FCD116',
            bg: 'linear-gradient(140deg, #1a1400 0%, #7A6000 50%, #e6a800 100%)',
            pattern: 'circles'
        },
        {
            id: 4,
            badge: 'Vérification',
            title: "Vérifiez la Validité\nde vos Documents",
            subtitle: "Consultez en temps réel le statut de vos cartes, permis et attestations officielles du Ministère.",
            cta: { label: 'Vérifier un document', href: '<?= BASE_PATH ?>/services' },
            ctaSecondary: { label: 'Créer un compte', href: '<?= BASE_PATH ?>/register' },
            icon: 'shield-check',
            accent: '#28A745',
            bg: 'linear-gradient(140deg, #001a08 0%, #0a5c27 50%, #1a9e48 100%)',
            pattern: 'dots'
        }
    ];

    var current = 0;
    var animating = false;
    var total = heroSlides.length;
    var autoTimer;
    var section = document.getElementById('hero-carousel');
    var dotsContainer = document.getElementById('hero-dots');

    // Build dots
    for (var i = 0; i < total; i++) {
        var dot = document.createElement('button');
        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
        dot.setAttribute('data-index', i);
        dot.style.cssText = 'height:8px;border-radius:4px;border:none;cursor:pointer;padding:0;transition:all 0.35s ease;';
        dot.addEventListener('click', function() { goTo(parseInt(this.getAttribute('data-index'))); });
        dotsContainer.appendChild(dot);
    }

    function renderSlide() {
        var slide = heroSlides[current];
        var isDarkAccent = (slide.accent === '#FCD116');

        section.style.background = slide.bg;

        // Pattern
        document.getElementById('hero-pattern-dots').style.display = (slide.pattern === 'dots') ? '' : 'none';
        document.getElementById('hero-pattern-grid').style.display = (slide.pattern === 'grid') ? '' : 'none';
        document.getElementById('hero-pattern-circles').style.display = (slide.pattern === 'circles') ? '' : 'none';

        // Circle 2 accent
        document.getElementById('hero-circle-2').style.background = slide.accent + '12';

        // Deco icon
        var decoIcon = document.getElementById('hero-deco-icon');
        decoIcon.innerHTML = '<i data-lucide="' + slide.icon + '" style="width:320px;height:320px;stroke-width:0.5;color:white;"></i>';

        // Badge
        var badge = document.getElementById('hero-badge');
        badge.style.background = slide.accent + '25';
        badge.style.border = '1px solid ' + slide.accent + '60';
        badge.style.color = slide.accent;
        badge.querySelector('i').setAttribute('data-lucide', slide.icon);
        document.getElementById('hero-badge-text').textContent = slide.badge;

        // Title / subtitle
        document.getElementById('hero-title').textContent = slide.title;
        document.getElementById('hero-subtitle').textContent = slide.subtitle;

        // CTA primary
        var ctaPrimary = document.getElementById('hero-cta-primary');
        ctaPrimary.href = slide.cta.href;
        ctaPrimary.style.background = slide.accent;
        ctaPrimary.style.color = isDarkAccent ? '#1A1400' : 'white';
        ctaPrimary.style.boxShadow = '0 8px 24px ' + slide.accent + '50';
        document.getElementById('hero-cta-primary-label').textContent = slide.cta.label;

        // CTA secondary
        var ctaSecondary = document.getElementById('hero-cta-secondary');
        ctaSecondary.href = slide.ctaSecondary.href;
        document.getElementById('hero-cta-secondary-label').textContent = slide.ctaSecondary.label;

        // Dots
        var dots = dotsContainer.querySelectorAll('button');
        for (var j = 0; j < dots.length; j++) {
            dots[j].style.width = (j === current) ? '32px' : '8px';
            dots[j].style.backgroundColor = (j === current) ? slide.accent : 'rgba(255,255,255,0.35)';
        }

        // Counter
        var counterNum = String(current + 1).padStart(2, '0');
        var totalNum = String(total).padStart(2, '0');
        document.getElementById('hero-counter').innerHTML = '<span style="color:' + slide.accent + ';font-weight:600;">' + counterNum + '</span> / ' + totalNum;

        // Re-init lucide icons in hero
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function goTo(index) {
        if (animating) return;
        animating = true;
        current = index;
        renderSlide();
        resetAutoplay();
        setTimeout(function() { animating = false; }, 600);
    }

    function resetAutoplay() {
        clearInterval(autoTimer);
        autoTimer = setInterval(function() { goTo((current + 1) % total); }, 6000);
    }

    document.getElementById('hero-prev').addEventListener('click', function() {
        goTo((current - 1 + total) % total);
    });
    document.getElementById('hero-next').addEventListener('click', function() {
        goTo((current + 1) % total);
    });

    // Initial render
    renderSlide();
    resetAutoplay();
})();
</script>


<!-- ============================================================
     SERVICES SECTION
     ============================================================ -->
<section style="padding:80px 0;background-color:white;">
    <div class="container">
        <div class="scroll-reveal">
            <h2 style="text-align:center;margin-bottom:8px;">Nos Services en Ligne</h2>
            <p style="text-align:center;color:#888;margin-bottom:48px;">
                Des outils numériques pensés pour simplifier vos démarches
            </p>
        </div>
        <div class="grid grid-4">
            <?php
            $serviceIcons = ['id-card', 'car', 'credit-card', 'shield-check'];
            $serviceColors = ['#007FFF', '#CE1021', '#e6a800', '#28A745'];
            foreach ($services as $i => $service):
                $icon = $serviceIcons[$i] ?? 'circle';
                $color = $serviceColors[$i] ?? '#007FFF';
            ?>
            <div class="scroll-reveal" data-delay="<?= $i * 100 ?>">
                <div class="card" style="text-align:center;padding:32px 24px;border-top-color:<?= $color ?>;">
                    <div style="display:inline-flex;align-items:center;justify-content:center;width:68px;height:68px;background:<?= $color ?>18;border-radius:50%;margin-bottom:20px;">
                        <i data-lucide="<?= $icon ?>" style="width:32px;height:32px;color:<?= $color ?>;stroke-width:1.5;"></i>
                    </div>
                    <h3 style="font-size:17px;margin-bottom:12px;"><?= htmlspecialchars($service['title']) ?></h3>
                    <p style="color:#666;margin-bottom:20px;font-size:14px;line-height:1.6;"><?= htmlspecialchars($service['description']) ?></p>
                    <a href="<?= BASE_PATH ?>/services" class="btn btn-outline" style="width:100%;font-size:14px;">
                        En savoir plus
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ============================================================
     STATS SECTION
     ============================================================ -->
<section style="padding:60px 0;background:linear-gradient(135deg,#007FFF 0%,#005FCC 100%);">
    <div class="container">
        <div class="grid grid-4" style="text-align:center;">
            <?php
            $statIcons = ['users', 'truck', 'file-text', 'globe'];
            foreach ($stats as $i => $stat):
                $icon = $statIcons[$i] ?? 'circle';
            ?>
            <div class="scroll-reveal" data-delay="<?= $i * 80 ?>">
                <div style="color:white;">
                    <div style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;background-color:rgba(255,255,255,0.15);border-radius:50%;margin-bottom:12px;">
                        <i data-lucide="<?= $icon ?>" style="width:24px;height:24px;color:white;stroke-width:1.5;"></i>
                    </div>
                    <div class="count-up" data-target="<?= htmlspecialchars($stat['value']) ?>" style="font-size:36px;font-weight:bold;color:#FCD116;line-height:1;">
                        <?= htmlspecialchars($stat['value']) ?>
                    </div>
                    <div style="color:rgba(255,255,255,0.85);margin-top:8px;font-size:14px;"><?= htmlspecialchars($stat['label']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ============================================================
     ACTUALITÉS SECTION
     ============================================================ -->
<section style="padding:80px 0;background-color:#F8F9FA;">
    <div class="container">
        <div class="scroll-reveal">
            <h2 style="text-align:center;margin-bottom:8px;">Actualités &amp; Communiqués</h2>
            <p style="text-align:center;color:#888;margin-bottom:48px;">
                Restez informé des dernières nouvelles du Ministère
            </p>
        </div>

        <div id="actualites-carousel" style="position:relative;">
            <!-- Viewport -->
            <div id="actualites-viewport" style="overflow:hidden;">
                <div id="actualites-track" style="display:flex;gap:24px;transition:transform 0.4s ease;">
                    <?php
                    $tagMeta = [
                        'Annonce'     => ['icon' => 'megaphone',    'color' => '#007FFF'],
                        'Mise à jour' => ['icon' => 'refresh-cw',   'color' => '#28A745'],
                        'Événement'   => ['icon' => 'party-popper', 'color' => '#CE1021'],
                    ];
                    foreach ($actualites as $i => $actu):
                        $meta = $tagMeta[$actu['tag']] ?? $tagMeta['Annonce'];
                        $tagColor = $meta['color'];
                        $tagIcon = $meta['icon'];
                    ?>
                    <div class="carousel-slide" style="flex:0 0 calc(33.333% - 16px);min-width:0;">
                        <div class="card" style="padding:28px;height:100%;">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;background-color:<?= $tagColor ?>18;color:<?= $tagColor ?>;padding:3px 10px;border-radius:20px;border:1px solid <?= $tagColor ?>40;">
                                    <i data-lucide="<?= $tagIcon ?>" style="width:11px;height:11px;"></i>
                                    <?= htmlspecialchars($actu['tag']) ?>
                                </span>
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:#aaa;">
                                    <i data-lucide="calendar-days" style="width:12px;height:12px;"></i>
                                    <?= htmlspecialchars($actu['date']) ?>
                                </span>
                            </div>
                            <h3 style="font-size:17px;margin-bottom:10px;line-height:1.4;"><?= htmlspecialchars($actu['titre']) ?></h3>
                            <p style="color:#666;margin-bottom:18px;font-size:14px;line-height:1.6;"><?= htmlspecialchars($actu['resume']) ?></p>
                            <a href="#" style="color:#007FFF;font-weight:600;font-size:14px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
                                Lire la suite <i data-lucide="chevron-right" style="width:15px;height:15px;"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Controls -->
            <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-top:24px;">
                <button id="actu-prev" class="carousel-btn" aria-label="Précédent">
                    <i data-lucide="chevron-left" style="width:18px;height:18px;"></i>
                </button>
                <div id="actu-dots" style="display:flex;gap:8px;align-items:center;"></div>
                <button id="actu-next" class="carousel-btn" aria-label="Suivant">
                    <i data-lucide="chevron-right" style="width:18px;height:18px;"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    var track = document.getElementById('actualites-track');
    var slides = track.querySelectorAll('.carousel-slide');
    var totalSlides = slides.length;
    var visibleSlides = 3;
    var currentActu = 0;
    var maxIndex = Math.max(0, totalSlides - visibleSlides);
    var dotsContainer = document.getElementById('actu-dots');
    var autoTimer;

    // Build dots
    for (var i = 0; i <= maxIndex; i++) {
        var dot = document.createElement('button');
        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
        dot.setAttribute('data-index', i);
        dot.style.cssText = 'height:8px;border-radius:4px;border:none;cursor:pointer;padding:0;transition:all 0.3s ease;';
        dot.addEventListener('click', function() { goToActu(parseInt(this.getAttribute('data-index'))); });
        dotsContainer.appendChild(dot);
    }

    function updateActu() {
        var slideWidth = slides[0].offsetWidth + 24;
        track.style.transform = 'translateX(-' + (currentActu * slideWidth) + 'px)';
        var dots = dotsContainer.querySelectorAll('button');
        for (var j = 0; j < dots.length; j++) {
            dots[j].style.width = (j === currentActu) ? '24px' : '8px';
            dots[j].style.backgroundColor = (j === currentActu) ? '#007FFF' : '#ccc';
        }
    }

    function goToActu(index) {
        currentActu = Math.max(0, Math.min(index, maxIndex));
        updateActu();
        resetActuAutoplay();
    }

    function resetActuAutoplay() {
        clearInterval(autoTimer);
        autoTimer = setInterval(function() {
            goToActu(currentActu >= maxIndex ? 0 : currentActu + 1);
        }, 4000);
    }

    document.getElementById('actu-prev').addEventListener('click', function() {
        goToActu(currentActu <= 0 ? maxIndex : currentActu - 1);
    });
    document.getElementById('actu-next').addEventListener('click', function() {
        goToActu(currentActu >= maxIndex ? 0 : currentActu + 1);
    });

    updateActu();
    resetActuAutoplay();
})();
</script>


<!-- ============================================================
     CTA SECTION
     ============================================================ -->
<section style="padding:80px 0;background:linear-gradient(135deg,#CE1021 0%,#A00D1A 100%);color:white;text-align:center;">
    <div class="container">
        <div class="scroll-reveal">
            <div style="display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;background-color:rgba(255,255,255,0.15);border-radius:50%;margin-bottom:20px;">
                <i data-lucide="phone-call" style="width:30px;height:30px;color:white;stroke-width:1.5;"></i>
            </div>
            <h2 style="color:white;margin-bottom:16px;font-size:clamp(22px,4vw,36px);">
                Vous avez besoin d'aide ?
            </h2>
            <p style="margin-bottom:32px;opacity:0.9;max-width:500px;margin:0 auto 32px;">
                Notre équipe est disponible 24h/24 pour répondre à toutes vos questions
            </p>
            <a href="<?= BASE_PATH ?>/contact" class="btn" style="background-color:#FCD116;color:#333;padding:14px 44px;font-size:16px;font-weight:600;">
                Nous contacter
            </a>
        </div>
    </div>
</section>
