<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class ConfigController extends Controller
{
    // Don't trust the client-supplied MIME type ($file['type']) or filename
    // extension - both are request fields the client fully controls.
    // getimagesize() parses the file's real header bytes, so a non-image
    // (or a polyglot, e.g. a .php payload disguised via a spoofed MIME type)
    // is rejected instead of trusted.
    private const ALLOWED_UPLOAD_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    private function handleLogoUpload($file, $prefix = 'logo')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $info = @getimagesize($file['tmp_name']);
        $detectedMime = $info['mime'] ?? null;

        if (!$detectedMime || !isset(self::ALLOWED_UPLOAD_MIMES[$detectedMime])) {
            return null;
        }

        $uploadDir = ROOT_DIR . '/public/uploads/logos';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = self::ALLOWED_UPLOAD_MIMES[$detectedMime];
        $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;
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

        $info = @getimagesize($file['tmp_name']);
        $detectedMime = $info['mime'] ?? null;

        if (!$detectedMime || !isset(self::ALLOWED_UPLOAD_MIMES[$detectedMime])) {
            return null;
        }

        $uploadDir = ROOT_DIR . '/public/uploads/signatures';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = self::ALLOWED_UPLOAD_MIMES[$detectedMime];
        $filename = 'signature_' . uniqid() . '_' . time() . '.' . $extension;
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
            $db->query("INSERT INTO configuration (cle, valeur) VALUES ('app_name', ?) ON CONFLICT (cle) DO UPDATE SET valeur = ?", [$app_name, $app_name]);
            $db->query("INSERT INTO configuration (cle, valeur) VALUES ('app_slogan', ?) ON CONFLICT (cle) DO UPDATE SET valeur = ?", [$app_slogan, $app_slogan]);

            $logoUpload = $this->handleLogoUpload($_FILES['app_logo'] ?? null);
            if ($logoUpload) {
                $db->query("INSERT INTO configuration (cle, valeur) VALUES ('app_logo', ?) ON CONFLICT (cle) DO UPDATE SET valeur = ?", [$logoUpload, $logoUpload]);
            }

            $carteLogoGauche = $this->handleLogoUpload($_FILES['carte_logo_gauche'] ?? null, 'carte_gauche');
            if ($carteLogoGauche) {
                $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_logo_gauche', ?) ON CONFLICT (cle) DO UPDATE SET valeur = ?", [$carteLogoGauche, $carteLogoGauche]);
            }

            $carteLogoDroite = $this->handleLogoUpload($_FILES['carte_logo_droite'] ?? null, 'carte_droite');
            if ($carteLogoDroite) {
                $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_logo_droite', ?) ON CONFLICT (cle) DO UPDATE SET valeur = ?", [$carteLogoDroite, $carteLogoDroite]);
            }

            $carteSignature = $this->handleSignatureUpload($_FILES['carte_signature'] ?? null);
            if ($carteSignature) {
                $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_signature', ?) ON CONFLICT (cle) DO UPDATE SET valeur = ?", [$carteSignature, $carteSignature]);
            }

            $carteTitreGauche = trim($_POST['carte_titre_gauche'] ?? '');
            $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_titre_gauche', ?) ON CONFLICT (cle) DO UPDATE SET valeur = ?", [$carteTitreGauche, $carteTitreGauche]);

            $carteTitreDroite = trim($_POST['carte_titre_droite'] ?? '');
            $db->query("INSERT INTO configuration (cle, valeur) VALUES ('carte_titre_droite', ?) ON CONFLICT (cle) DO UPDATE SET valeur = ?", [$carteTitreDroite, $carteTitreDroite]);

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
