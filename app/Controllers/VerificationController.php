<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class VerificationController extends Controller
{
    public function show($id)
    {
        $db = Database::getInstance();

        try {
            $conducteur = $db->fetchOne(
                "SELECT id, nom, prenom, date_naissance, lieu_naissance, telephone, adresse,
                        numero_permis, categorie_permis, date_expiration_permis, photo_url,
                        association, syndicat, date_enregistrement, date_expiration,
                        statut, statut_brevet
                 FROM conducteurs WHERE id = ?",
                [$id]
            );
        } catch (\Exception $e) {
            $conducteur = null;
        }

        $this->render('verification/show', [
            'pageTitle' => 'Vérification de brevet',
            'conducteur' => $conducteur,
        ], 'none');
    }

    public function signalerFraude()
    {
        $db = Database::getInstance();

        $conducteurId = $_POST['conducteur_id'] ?? null;
        $nomVerificateur = trim($_POST['nom_verificateur'] ?? '');
        $telephoneVerificateur = trim($_POST['telephone_verificateur'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$conducteurId || empty($nomVerificateur) || empty($description)) {
            $this->json(['error' => 'Veuillez remplir tous les champs obligatoires'], 400);
            return;
        }

        $photoUrl = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            // Don't trust the client-supplied MIME type ($file['type']) or filename
            // extension - both are request headers/fields the client fully controls.
            // getimagesize() actually parses the file's real header bytes, so a
            // non-image (or a polyglot) fails here instead of being trusted.
            $allowedMimes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            ];

            $info = @getimagesize($file['tmp_name']);
            $detectedMime = $info['mime'] ?? null;

            if ($detectedMime && isset($allowedMimes[$detectedMime]) && $file['size'] <= 5 * 1024 * 1024) {
                $uploadDir = ROOT_DIR . '/public/uploads/signalements';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $ext = $allowedMimes[$detectedMime];
                $filename = 'fraude_' . $conducteurId . '_' . time() . '.' . $ext;
                $destination = $uploadDir . '/' . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $photoUrl = 'uploads/signalements/' . $filename;
                }
            }
        }

        try {
            $db->query(
                "INSERT INTO signalements_fraude (conducteur_id, nom_verificateur, telephone_verificateur, description, photo_url)
                 VALUES (?, ?, ?, ?, ?)",
                [$conducteurId, $nomVerificateur, $telephoneVerificateur, $description, $photoUrl]
            );
            $this->json(['success' => true]);
        } catch (\Exception $e) {
            error_log('[VerificationController::signalerFraude] ' . $e->getMessage());
            $this->json(['error' => 'Erreur lors de l\'envoi du signalement'], 500);
        }
    }
}
