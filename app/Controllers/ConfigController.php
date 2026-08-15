<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class ConfigController extends Controller
{
    private function handleLogoUpload($file, $prefix = 'logo')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $uploadDir = ROOT_DIR . '/public/uploads/logos';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/logos/' . $filename;
        }

        return null;
    }

    private function handleSignatureUpload($file)
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $uploadDir = ROOT_DIR . '/public/uploads/signatures';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'signature_' . time() . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/signatures/' . $filename;
        }

        return null;
    }

    public function index()
    {
        $db = Database::getInstance();
        $user = Auth::user();

        if ($user['role'] !== 'admin') {
            header('Location: ' . BASE_PATH . '/admin');
            exit;
        }

        try {
            $configs = $db->fetchAll("SELECT * FROM configuration ORDER BY cle ASC");
            $configData = [];
            foreach ($configs as $c) {
                $configData[$c['cle']] = $c['valeur'];
            }
        } catch (\Exception $e) {
            $configData = [
                'app_name' => 'Ministère des Transports',
                'app_logo' => '',
                'app_slogan' => 'Portail Numérique',
            ];
        }

        $this->render('admin/config', [
            'pageTitle' => 'Configuration',
            'currentPage' => '/admin/config',
            'config' => $configData,
        ], 'admin');
    }

    public function save()
    {
        $db = Database::getInstance();
        $user = Auth::user();

        if ($user['role'] !== 'admin') {
            header('Location: ' . BASE_PATH . '/admin');
            exit;
        }

        $app_name = trim($_POST['app_name'] ?? '');
        $app_slogan = trim($_POST['app_slogan'] ?? '');

        if (empty($app_name)) {
            header('Location: ' . BASE_PATH . '/admin/config?error=Le nom de l\'application est obligatoire');
            exit;
        }

        try {
            $db->query("INSERT INTO configuration (cle, valeur) VALUES ('app_name', ?) ON DUPLICATE KEY UPDATE valeur = ?", [$app_name, $app_name]);
            $db->query("INSERT INTO configuration (cle, valeur) VALUES ('app_slogan', ?) ON DUPLICATE KEY UPDATE valeur = ?", [$app_slogan, $app_slogan]);

            $logoUpload = $this->handleLogoUpload($_FILES['app_logo'] ?? null);
            if ($logoUpload) {
                $db->query("INSERT INTO configuration (cle, valeur) VALUES ('app_logo', ?) ON DUPLICATE KEY UPDATE valeur = ?", [$logoUpload, $logoUpload]);
            }

            $carteLogoGauche = $this->handleLogoUpload($_FILES['carte_logo_gauche'] ?? null, 'carte_gauche');
            if ($carteLogoGauche) {
                $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_logo_gauche', ?) ON DUPLICATE KEY UPDATE valeur = ?", [$carteLogoGauche, $carteLogoGauche]);
            }

            $carteLogoDroite = $this->handleLogoUpload($_FILES['carte_logo_droite'] ?? null, 'carte_droite');
            if ($carteLogoDroite) {
                $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_logo_droite', ?) ON DUPLICATE KEY UPDATE valeur = ?", [$carteLogoDroite, $carteLogoDroite]);
            }

            $carteSignature = $this->handleSignatureUpload($_FILES['carte_signature'] ?? null);
            if ($carteSignature) {
                $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_signature', ?) ON DUPLICATE KEY UPDATE valeur = ?", [$carteSignature, $carteSignature]);
            }

            $carteTitreGauche = trim($_POST['carte_titre_gauche'] ?? '');
            $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_titre_gauche', ?) ON DUPLICATE KEY UPDATE valeur = ?", [$carteTitreGauche, $carteTitreGauche]);

            $carteTitreDroite = trim($_POST['carte_titre_droite'] ?? '');
            $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_titre_droite', ?) ON DUPLICATE KEY UPDATE valeur = ?", [$carteTitreDroite, $carteTitreDroite]);

            header('Location: ' . BASE_PATH . '/admin/config?success=Configuration enregistrée avec succès');
            exit;
        } catch (\Exception $e) {
            header('Location: ' . BASE_PATH . '/admin/config?error=Erreur: ' . urlencode($e->getMessage()));
            exit;
        }
    }

    public static function get($key, $default = '')
    {
        $db = Database::getInstance();
        try {
            $row = $db->fetchOne("SELECT valeur FROM configuration WHERE cle = ?", [$key]);
            return $row ? $row['valeur'] : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
