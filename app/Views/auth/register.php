<?php
/** @var string|null $flash */
/** @var array $steps */
/** @var array $roleOptions */

use App\Controllers\ConfigController;

$appName = ConfigController::get('app_name', 'Ministère des Transports');
$appSlogan = ConfigController::get('app_slogan', 'Portail Numérique');
$appLogo = ConfigController::get('app_logo', '');
?>
<style>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: translateY(0); }
}

.auth-login-root {
  min-height: 100vh;
  display: flex;
}
.auth-left-panel {
  flex: 0 0 40%;
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
  align-items: flex-start;
  justify-content: center;
  padding: 40px 24px;
  background: #FAFAFA;
}
.auth-right-inner {
  width: 100%;
  max-width: 560px;
  animation: fadeInUp 0.5s ease;
}

/* Step indicator */
.register-steps {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 32px;
}
.register-step {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 8px;
}
.register-step-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #EAF4FF;
  border: 1.5px solid #B9DEFF;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #007FFF;
  flex-shrink: 0;
}
.register-step-label {
  font-size: 12.5px;
  font-weight: 600;
  color: #1A1A2E;
}
.register-step-desc {
  font-size: 11px;
  color: #999;
  line-height: 1.3;
}
.register-step-connector {
  flex: 0 0 auto;
  width: 100%;
  max-width: 40px;
  height: 1.5px;
  background: #E0E0E0;
  margin-top: 20px;
}

/* Role cards */
.role-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: 20px;
}
.role-card-input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}
.role-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 6px;
  padding: 14px 10px;
  border: 1.5px solid #E0E0E0;
  border-radius: 10px;
  cursor: pointer;
  background: white;
  transition: all 0.2s;
}
.role-card i {
  width: 20px;
  height: 20px;
  color: #888;
  transition: color 0.2s;
}
.role-card-label {
  font-size: 12.5px;
  font-weight: 600;
  color: #333;
}
.role-card-desc {
  font-size: 10.5px;
  color: #999;
  line-height: 1.3;
}
.role-card-input:checked + .role-card {
  border-color: #007FFF;
  background: #EAF4FF;
  box-shadow: 0 2px 10px rgba(0,127,255,0.15);
}
.role-card-input:checked + .role-card i,
.role-card-input:checked + .role-card .role-card-label {
  color: #007FFF;
}
.role-card-input:focus-visible + .role-card {
  outline: 2px solid #007FFF;
  outline-offset: 2px;
}

/* Inputs */
.float-input-wrap {
  position: relative;
  margin-bottom: 18px;
}
.float-input-wrap input,
.float-input-wrap select {
  width: 100%;
  padding: 16px 14px 8px 42px;
  border: 1.5px solid #E0E0E0;
  border-radius: 10px;
  font-size: 15px;
  background: white;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
  font-family: inherit;
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
.form-row {
  display: flex;
  gap: 14px;
}
.form-row .float-input-wrap {
  flex: 1;
}
.field-hint {
  font-size: 11.5px;
  color: #999;
  margin: -12px 0 18px 4px;
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
  .register-step-desc { display: none; }
  .role-cards { grid-template-columns: 1fr; }
  .form-row { flex-direction: column; gap: 0; }
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
        Rejoignez <?= htmlspecialchars($appSlogan) ?>
      </h2>
      <p style="color:rgba(255,255,255,0.7);font-size:15px;margin-bottom:40px;line-height:1.6">
        <?= htmlspecialchars($appName) ?>
      </p>

      <!-- Features -->
      <div style="display:flex;flex-direction:column;gap:16px">
        <div style="display:flex;align-items:center;gap:14px">
          <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i data-lucide="user-plus" style="width:18px;height:18px;color:white;"></i>
          </div>
          <span style="color:rgba(255,255,255,0.85);font-size:14px">Création de compte en quelques minutes</span>
        </div>
        <div style="display:flex;align-items:center;gap:14px">
          <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i data-lucide="shield-check" style="width:18px;height:18px;color:white;"></i>
          </div>
          <span style="color:rgba(255,255,255,0.85);font-size:14px">Données protégées et confidentielles</span>
        </div>
        <div style="display:flex;align-items:center;gap:14px">
          <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i data-lucide="zap" style="width:18px;height:18px;color:white;"></i>
          </div>
          <span style="color:rgba(255,255,255,0.85);font-size:14px">Accès immédiat à vos services</span>
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
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
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

      <div style="margin-bottom:28px">
        <h1 style="font-size:28px;font-family:Poppins,sans-serif;color:#1A1A2E;margin-bottom:8px">Créer un compte</h1>
        <p style="color:#888;font-size:15px">Rejoignez le portail en quelques étapes</p>
      </div>

      <!-- Indicateur d'étapes (informatif, non interactif) -->
      <div class="register-steps">
        <?php foreach ($steps as $i => $step): ?>
          <div class="register-step">
            <div class="register-step-icon">
              <i data-lucide="<?= htmlspecialchars($step['icon']) ?>" style="width:18px;height:18px;"></i>
            </div>
            <div class="register-step-label"><?= htmlspecialchars($step['label']) ?></div>
            <div class="register-step-desc"><?= htmlspecialchars($step['description']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <form method="POST" action="<?= BASE_PATH ?>/register">
        <?= \App\Core\Csrf::field() ?>

        <!-- Étape 1 : Identité -->
        <div class="form-row">
          <div class="float-input-wrap">
            <span class="input-icon">
              <i data-lucide="user" style="width:16px;height:16px;"></i>
            </span>
            <input type="text" name="prenom" id="register-prenom" placeholder=" " required autocomplete="given-name">
            <label for="register-prenom">Prénom</label>
          </div>
          <div class="float-input-wrap">
            <span class="input-icon">
              <i data-lucide="user" style="width:16px;height:16px;"></i>
            </span>
            <input type="text" name="nom" id="register-nom" placeholder=" " required autocomplete="family-name">
            <label for="register-nom">Nom</label>
          </div>
        </div>

        <!-- Étape 2 : Contact -->
        <div class="float-input-wrap">
          <span class="input-icon">
            <i data-lucide="mail" style="width:16px;height:16px;"></i>
          </span>
          <input type="email" name="email" id="register-email" placeholder=" " required autocomplete="email">
          <label for="register-email">Adresse email</label>
        </div>

        <div class="float-input-wrap">
          <span class="input-icon">
            <i data-lucide="phone" style="width:16px;height:16px;"></i>
          </span>
          <input type="tel" name="telephone" id="register-telephone" placeholder=" " required autocomplete="tel">
          <label for="register-telephone">Téléphone</label>
        </div>

        <!-- Rôle -->
        <label style="display:block;font-size:13px;font-weight:600;color:#333;margin-bottom:10px">Vous êtes</label>
        <div class="role-cards">
          <?php foreach ($roleOptions as $i => $option): ?>
            <input
              class="role-card-input"
              type="radio"
              name="role"
              id="role-<?= htmlspecialchars($option['value']) ?>"
              value="<?= htmlspecialchars($option['value']) ?>"
              <?= $i === 0 ? 'checked' : '' ?>
            >
            <label class="role-card" for="role-<?= htmlspecialchars($option['value']) ?>">
              <i data-lucide="<?= htmlspecialchars($option['icon']) ?>"></i>
              <span class="role-card-label"><?= htmlspecialchars($option['label']) ?></span>
              <span class="role-card-desc"><?= htmlspecialchars($option['description']) ?></span>
            </label>
          <?php endforeach; ?>
        </div>

        <!-- Étape 3 : Sécurité -->
        <div class="float-input-wrap">
          <span class="input-icon">
            <i data-lucide="lock" style="width:16px;height:16px;"></i>
          </span>
          <input type="password" name="password" id="register-password" placeholder=" " required minlength="6" autocomplete="new-password">
          <label for="register-password">Mot de passe</label>
          <button type="button" class="eye-toggle" onclick="toggleRegisterPassword('register-password', this)">
            <i data-lucide="eye" style="width:16px;height:16px;"></i>
          </button>
        </div>
        <div class="field-hint">Au moins 6 caractères</div>

        <div class="float-input-wrap">
          <span class="input-icon">
            <i data-lucide="lock" style="width:16px;height:16px;"></i>
          </span>
          <input type="password" name="confirmPassword" id="register-confirm-password" placeholder=" " required minlength="6" autocomplete="new-password">
          <label for="register-confirm-password">Confirmer le mot de passe</label>
          <button type="button" class="eye-toggle" onclick="toggleRegisterPassword('register-confirm-password', this)">
            <i data-lucide="eye" style="width:16px;height:16px;"></i>
          </button>
        </div>
        <div class="field-hint" id="register-password-match-hint"></div>

        <!-- Étape 4 : Révision / soumission -->
        <button type="submit" class="login-btn">
          <i data-lucide="user-plus" style="width:18px;height:18px;"></i>
          Créer mon compte
        </button>

        <p style="text-align:center;font-size:13px;color:#888;margin-top:20px">
          Déjà un compte ?
          <a href="<?= BASE_PATH ?>/login" style="color:#007FFF;font-weight:600;text-decoration:none">Se connecter</a>
        </p>
      </form>
    </div>
  </div>
</div>

<script>
function toggleRegisterPassword(inputId, btn) {
  const input = document.getElementById(inputId);
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  const icon = btn.querySelector('i');
  if (icon) {
    icon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
    if (window.lucide) {
      lucide.createIcons();
    }
  }
}

(function () {
  const pwd = document.getElementById('register-password');
  const confirm = document.getElementById('register-confirm-password');
  const hint = document.getElementById('register-password-match-hint');
  if (!pwd || !confirm || !hint) return;

  function checkMatch() {
    if (!confirm.value) {
      hint.textContent = '';
      hint.style.color = '#999';
      return;
    }
    if (pwd.value === confirm.value) {
      hint.textContent = 'Les mots de passe correspondent.';
      hint.style.color = '#059669';
    } else {
      hint.textContent = 'Les mots de passe ne correspondent pas.';
      hint.style.color = '#DC2626';
    }
  }

  pwd.addEventListener('input', checkMatch);
  confirm.addEventListener('input', checkMatch);
})();
</script>
