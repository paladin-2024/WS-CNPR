<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Controllers\ConfigController;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            $this->redirect('/admin');
            return;
        }

        $flashData = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $flash = is_array($flashData) ? ($flashData['message'] ?? null) : $flashData;

        $appName = ConfigController::get('app_name', 'Ministère des Transports');
        $appSlogan = ConfigController::get('app_slogan', 'Portail Numérique');
        $appLogo = ConfigController::get('app_logo', '');

        $this->render('auth/login', [
            'pageTitle' => 'Connexion',
            'currentPage' => '/login',
            'appName' => $appName,
            'appSlogan' => $appSlogan,
            'appLogo' => $appLogo,
            'flash' => $flash,
        ], 'none');
    }

    // Verrouillage après échecs répétés : au-delà de ce nombre de tentatives
    // échouées consécutives, le compte est bloqué temporairement (indépendant
    // du CAPTCHA/2FA qui n'existent pas dans cette app - c'est la seule
    // protection contre le brute-force sur /login).
    private const MAX_TENTATIVES = 5;
    private const DUREE_VERROUILLAGE = '15 minutes';

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Veuillez remplir tous les champs.'];
            $this->redirect('/login');
            return;
        }

        try {
            $db = Database::getInstance();
            $user = $db->fetchOne(
                "SELECT * FROM utilisateurs WHERE email = ? AND statut = 'actif' LIMIT 1",
                [$email]
            );

            if ($user && !empty($user['verrouille_jusqu_a']) && strtotime($user['verrouille_jusqu_a']) > time()) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Compte temporairement verrouillé après plusieurs échecs. Réessayez dans quelques minutes.'];
                $this->redirect('/login');
                return;
            }

            if (!$user || !password_verify($password, $user['mot_de_passe'])) {
                if ($user) {
                    $tentatives = (int)$user['tentatives_echouees'] + 1;
                    if ($tentatives >= self::MAX_TENTATIVES) {
                        $db->query(
                            "UPDATE utilisateurs SET tentatives_echouees = ?, verrouille_jusqu_a = NOW() + INTERVAL '" . self::DUREE_VERROUILLAGE . "' WHERE id = ?",
                            [$tentatives, $user['id']]
                        );
                    } else {
                        $db->query(
                            "UPDATE utilisateurs SET tentatives_echouees = ? WHERE id = ?",
                            [$tentatives, $user['id']]
                        );
                    }
                }
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email ou mot de passe incorrect.'];
                $this->redirect('/login');
                return;
            }

            $db->query(
                "UPDATE utilisateurs SET derniere_connexion = NOW(), tentatives_echouees = 0, verrouille_jusqu_a = NULL WHERE id = ?",
                [$user['id']]
            );

            Auth::login($user);
            $this->redirect('/admin');
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Erreur de connexion. Veuillez réessayer.'];
            $this->redirect('/login');
        }
    }

    public function registerForm()
    {
        if (Auth::check()) {
            $this->redirect('/admin');
            return;
        }

        $flashData = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $flash = is_array($flashData) ? ($flashData['message'] ?? null) : $flashData;

        $steps = [
            ['label' => 'Identité',  'description' => 'Vos informations personnelles', 'icon' => 'user'],
            ['label' => 'Contact',   'description' => 'Email et téléphone',             'icon' => 'mail'],
            ['label' => 'Sécurité',  'description' => 'Mot de passe',                   'icon' => 'lock'],
            ['label' => 'Révision',  'description' => 'Vérifiez vos données',           'icon' => 'check-circle-2'],
        ];

        // Only self-service roles - AuthController::register() now rejects
        // anything else. Ministry/admin accounts are provisioned by an
        // existing admin (/admin/utilisateurs), never via public signup.
        $roleOptions = [
            ['value' => 'citoyen',    'label' => 'Citoyen',        'icon' => 'user-circle', 'description' => 'Accès aux services publics'],
            ['value' => 'conducteur', 'label' => 'Conducteur',     'icon' => 'truck',       'description' => 'Carte professionnelle, immatriculation'],
        ];

        $this->render('auth/register', [
            'pageTitle' => 'Inscription',
            'currentPage' => '/register',
            'steps' => $steps,
            'roleOptions' => $roleOptions,
            'flash' => $flash,
        ], 'none');
    }

    public function register()
    {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $role = $_POST['role'] ?? 'citoyen';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($password)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Veuillez remplir tous les champs.'];
            $this->redirect('/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Adresse email invalide.'];
            $this->redirect('/register');
            return;
        }

        if (strlen($password) < 6) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Le mot de passe doit contenir au moins 6 caractères.'];
            $this->redirect('/register');
            return;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Les mots de passe ne correspondent pas.'];
            $this->redirect('/register');
            return;
        }

        // Public self-registration must never grant a privileged role. 'agent'
        // and the other administrative roles (operateur_saisie, validateur,
        // receveur, instructeur, admin, minister_admin, ...) are provisioned
        // by an existing admin (/admin/utilisateurs), not chosen by an
        // anonymous visitor - this used to accept all of them here.
        $allowedRoles = ['citoyen', 'conducteur'];
        if (!in_array($role, $allowedRoles, true)) {
            $role = 'citoyen';
        }

        try {
            $db = Database::getInstance();

            $existing = $db->fetchOne("SELECT id FROM utilisateurs WHERE email = ? LIMIT 1", [$email]);
            if ($existing) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Un compte avec cet email existe déjà.'];
                $this->redirect('/register');
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $db->query(
                "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut) VALUES (?, ?, ?, ?, ?, ?, 'actif')",
                [$nom, $prenom, $email, $telephone, $hashedPassword, $role]
            );

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Compte créé avec succès ! Connectez-vous.'];
            $this->redirect('/login');
        } catch (\Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => "Erreur lors de l'inscription. Veuillez réessayer."];
            $this->redirect('/register');
        }
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('/');
    }
}
