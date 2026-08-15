<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        // Récupérer les infos complètes depuis la base de données
        $db = Database::getInstance();
        $userData = $db->fetch("SELECT * FROM utilisateurs WHERE id = ?", [$user['id']]);
        
        // Préparer les labels
        $roleLabel = match($userData['role'] ?? 'utilisateur') {
            'admin' => 'Administrateur',
            'conducteur' => 'Conducteur',
            'receptionnaire' => 'Réceptionnaire',
            default => 'Utilisateur'
        };
        
        $statutLabel = match($userData['statut'] ?? 'actif') {
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'suspendu' => 'Suspendu',
            default => 'Actif'
        };
        
        // Formatage des dates
        $dateCreation = !empty($userData['date_creation']) 
            ? date('d/m/Y à H:i', strtotime($userData['date_creation'])) 
            : 'N/A';
        $derniereConnexion = !empty($userData['derniere_connexion']) 
            ? date('d/m/Y à H:i', strtotime($userData['derniere_connexion'])) 
            : 'Première connexion';
        
        $this->render('profile/index', [
            'user' => $userData,
            'pageTitle' => 'Mon Profil',
            'roleLabel' => $roleLabel,
            'statutLabel' => $statutLabel,
            'dateCreation' => $dateCreation,
            'derniereConnexion' => $derniereConnexion
        ], 'admin');
    }

    /**
     * Afficher le formulaire de modification du profil
     */
    public function edit()
    {
        $user = Auth::user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $db = Database::getInstance();
        $userData = $db->fetch("SELECT * FROM utilisateurs WHERE id = ?", [$user['id']]);
        
        $this->render('profile/edit', [
            'user' => $userData,
            'pageTitle' => 'Modifier mon Profil'
        ], 'admin');
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function update()
    {
        $user = Auth::user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $db = Database::getInstance();
        
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        // Validation des champs obligatoires
        if (empty($nom)) {
            $errors[] = "Le nom est obligatoire.";
        }
        if (empty($prenom)) {
            $errors[] = "Le prénom est obligatoire.";
        }
        if (empty($telephone)) {
            $errors[] = "Le téléphone est obligatoire.";
        }

        // Si l'utilisateur veut changer son mot de passe
        if (!empty($currentPassword) || !empty($newPassword)) {
            $userData = $db->fetch("SELECT mot_de_passe FROM utilisateurs WHERE id = ?", [$user['id']]);
            
            if (empty($currentPassword)) {
                $errors[] = "Veuillez entrer le mot de passe actuel.";
            } elseif (!password_verify($currentPassword, $userData['mot_de_passe'] ?? '')) {
                $errors[] = "Le mot de passe actuel est incorrect.";
            }
            
            if (empty($newPassword)) {
                $errors[] = "Veuillez entrer un nouveau mot de passe.";
            } elseif (strlen($newPassword) < 6) {
                $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => implode(' ', $errors)];
            $this->redirect('/profile/edit');
            return;
        }

        // Préparer les données à mettre à jour
        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'telephone' => $telephone
        ];

        // Si nouveau mot de passe, le hacher et l'ajouter
        if (!empty($newPassword) && empty($errors)) {
            $data['mot_de_passe'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        // Mettre à jour l'utilisateur
        $result = $db->update('utilisateurs', $data, 'id = ?', [$user['id']]);

        if ($result) {
            // Mettre à jour la session avec les nouvelles informations
            $updatedUser = $db->fetch("SELECT * FROM utilisateurs WHERE id = ?", [$user['id']]);
            Auth::login($updatedUser);
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profil mis à jour avec succès.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Erreur lors de la mise à jour du profil.'];
        }

        $this->redirect('/profile');
    }
}