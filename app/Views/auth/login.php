<?php /** @var string $flash */ ?>
<style>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes spin { to { transform: rotate(360deg); } }

.auth-login-root {
  min-height: 100vh;
  display: flex;
}
.auth-left-panel {
  flex: 0 0 44%;
  background: linear-gradient(160deg, #005FCC 0%, #003A8C 60%, #001F5B 100%);
  display: flex;
  flex-direction: column;
  padding: 48px 52px;
  position: relative;
  overflow: hidden;
}
.auth-right-panel {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  background: #FAFAFA;
}
.auth-right-inner {
  width: 100%;
  max-width: 420px;
  animation: fadeInUp 0.5s ease;
}
.float-input-wrap {
  position: relative;
  margin-bottom: 18px;
}
.float-input-wrap input {
  width: 100%;
  padding: 16px 14px 8px 42px;
  border: 1.5px solid #E0E0E0;
  border-radius: 10px;
  font-size: 15px;
  background: white;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
}
.float-input-wrap input:focus {
  border-color: #007FFF;
}
.float-input-wrap label {
  position: absolute;
  left: 42px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  color: #aaa;
  pointer-events: none;
  transition: all 0.2s;
}
.float-input-wrap input:focus + label,
.float-input-wrap input:not(:placeholder-shown) + label {
  top: 8px;
  transform: translateY(0);
  font-size: 10px;
  color: #007FFF;
}
.float-input-wrap .input-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #bbb;
}
.float-input-wrap .eye-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #bbb;
  padding: 4px;
}
.login-btn {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #007FFF, #005FCC);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  font-family: Poppins, sans-serif;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  box-shadow: 0 4px 16px rgba(0,127,255,0.3);
  transition: all 0.2s;
}
.login-btn:hover { opacity: 0.92; }

@media (max-width: 860px) {
  .auth-left-panel { display: none !important; }
}
</style>

<div class="auth-login-root">
  <!-- Panneau gauche — Branding -->
  <div class="auth-left-panel">
    <!-- Drapeau bande -->
    <div style="display:flex;height:5px;border-radius:4px;overflow:hidden;margin-bottom:40px;width:80px">
      <div style="flex:1;background:#007FFF"></div>
      <div style="flex:1;background:#FCD116"></div>
      <div style="flex:1;background:#CE1021"></div>
    </div>

    <!-- Logo -->
    <div style="margin-bottom:auto">
      <?php if (!empty($appLogo)): ?>
        <img src="<?= BASE_PATH ?>/public/<?= htmlspecialchars($appLogo) ?>" alt="Logo" style="width:56px;height:56px;object-fit:contain;margin-bottom:40px;">
      <?php else: ?>
        <div style="width:56px;height:56px;background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:white;font-family:Poppins,sans-serif;margin-bottom:40px;border:1px solid rgba(255,255,255,0.25)">
          <?= htmlspecialchars(substr($appName, 0, 2)) ?>
        </div>
      <?php endif; ?>
      <h2 style="color:white;font-size:clamp(22px,3vw,32px);font-family:Poppins,sans-serif;margin-bottom:12px;line-height:1.3">
        Bienvenue sur <?= htmlspecialchars($appSlogan) ?>
      </h2>
      <p style="color:rgba(255,255,255,0.7);font-size:15px;margin-bottom:40px;line-height:1.6">
        <?= htmlspecialchars($appName) ?>
      </p>

      <!-- Features -->
      <div style="display:flex;flex-direction:column;gap:16px">
        <div style="display:flex;align-items:center;gap:14px">
          <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
          </div>
          <span style="color:rgba(255,255,255,0.85);font-size:14px">Plateforme sécurisée et certifiée</span>
        </div>
        <div style="display:flex;align-items:center;gap:14px">
          <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          </div>
          <span style="color:rgba(255,255,255,0.85);font-size:14px">Accessible 24h/24, 7j/7</span>
        </div>
        <div style="display:flex;align-items:center;gap:14px">
          <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          </div>
          <span style="color:rgba(255,255,255,0.85);font-size:14px">Traitement rapide de vos dossiers</span>
        </div>
      </div>
    </div>

    <!-- Décorations circulaires -->
    <div style="position:absolute;right:-80px;top:-80px;width:280px;height:280px;border-radius:50%;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);pointer-events:none"></div>
    <div style="position:absolute;right:-40px;bottom:60px;width:200px;height:200px;border-radius:50%;background:rgba(252,209,22,0.08);pointer-events:none"></div>

    <p style="color:rgba(255,255,255,0.4);font-size:12px;margin-top:auto;padding-top:32px">
      © <?= date('Y') ?> <?= htmlspecialchars($appName) ?>. Tous droits réservés.
    </p>
  </div>

  <!-- Panneau droit — Formulaire -->
  <div class="auth-right-panel">
    <div class="auth-right-inner">
      <!-- Retour au portail -->
      <a href="<?= BASE_PATH ?>/" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#888;margin-bottom:28px;text-decoration:none;transition:color 0.2s">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Retour au portail
      </a>

      <!-- Flash messages -->
      <?php if (!empty($flash)): ?>
        <div style="padding:12px 16px;margin-bottom:18px;border-radius:8px;font-size:13px;
          <?= strpos($flash, 'succès') !== false || strpos($flash, 'réussie') !== false
            ? 'background:#ECFDF5;color:#059669;border:1px solid #A7F3D0'
            : 'background:#FEF2F2;color:#DC2626;border:1px solid #FECACA' ?>">
          <?= htmlspecialchars($flash) ?>
        </div>
      <?php endif; ?>

      <div style="margin-bottom:36px">
        <h1 style="font-size:28px;font-family:Poppins,sans-serif;color:#1A1A2E;margin-bottom:8px">Connexion</h1>
        <p style="color:#888;font-size:15px">Accédez à votre espace personnel</p>
      </div>

      <form method="POST" action="<?= BASE_PATH ?>/login">
        <!-- Email -->
        <div class="float-input-wrap">
          <span class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          </span>
          <input type="email" name="email" id="login-email" placeholder=" " required autocomplete="email">
          <label for="login-email">Adresse email</label>
        </div>

        <!-- Password -->
        <div class="float-input-wrap">
          <span class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input type="password" name="password" id="login-password" placeholder=" " required autocomplete="current-password">
          <label for="login-password">Mot de passe</label>
          <button type="button" class="eye-toggle" onclick="togglePassword()">
            <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>

        <!-- Remember + Forgot -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;font-size:13px">
          <label style="display:flex;align-items:center;gap:7px;cursor:pointer;color:#666">
            <input type="checkbox" name="remember" style="accent-color:#007FFF">
            Se souvenir de moi
          </label>
          <a href="#" style="color:#007FFF;font-weight:500;text-decoration:none">Mot de passe oublié ?</a>
        </div>

        <!-- Submit -->
        <button type="submit" class="login-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Se connecter
        </button>
      </form>

      

    </div>
  </div>
</div>

<script>
function togglePassword() {
  const input = document.getElementById('login-password');
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  const icon = document.getElementById('eye-icon');
  if (isPassword) {
    icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
  } else {
    icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  }
}
</script>
