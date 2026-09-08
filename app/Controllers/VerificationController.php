<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class VerificationController extends Controller
{
    // Requests allowed per IP in the trailing window before this public,
    // unauthenticated lookup starts refusing - see verification_attempts in
    // database/schema.sql for why this exists.
    private const RATE_LIMIT_MAX_ATTEMPTS = 15;
    private const RATE_LIMIT_WINDOW_MINUTES = 10;

    public function show($identifiant)
    {
        $db = Database::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Opportunistic cleanup so this table doesn't grow unbounded - cheap,
        // and only ever removes rows already outside the window we check.
        $db->query("DELETE FROM verification_attempts WHERE created_at < NOW() - INTERVAL '1 hour'");

        $recentAttempts = $db->fetchOne(
            "SELECT COUNT(*) AS n FROM verification_attempts WHERE ip = ? AND created_at > NOW() - INTERVAL '" . self::RATE_LIMIT_WINDOW_MINUTES . " minutes'",
            [$ip]
        );

        if (($recentAttempts['n'] ?? 0) >= self::RATE_LIMIT_MAX_ATTEMPTS) {
            $this->render('verification/show', [
                'pageTitle' => 'Vérification de brevet',
                'conducteur' => null,
                'rateLimited' => true,
            ], 'none');
            return;
        }

        $db->query("INSERT INTO verification_attempts (ip) VALUES (?)", [$ip]);

        // Only what's needed to visually/administratively confirm a brevet
        // is genuine and see who it belongs to - phone/adresse/date et lieu
        // de naissance/association/syndicat are not required for that and
        // were being exposed to anyone who could guess or enumerate an
        // identifiant (identifiant_conducteur_seq generates sequential,
        // guessable values), so they're intentionally left out here.
        try {
            $conducteur = $db->fetchOne(
                "SELECT id, nom, prenom, numero_permis, categorie_permis,
                        date_expiration_permis, photo_url,
                        date_enregistrement, statut, statut_brevet
                 FROM conducteurs WHERE numero_permis = ?",
                [$identifiant]
            );
        } catch (\Exception $e) {
            $conducteur = null;
        }

        // PDOStatement::fetch() (Database::fetchOne()) returns false, not
        // null, when no row matches - the view's "not found" branch checks
        // for null, so without this a real "brevet not found" lookup was
        // silently falling into the "found" rendering path instead and
        // throwing warnings trying to read fields off `false`.
        if ($conducteur === false) {
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

        // conducteur_id is a FK to a SERIAL column and gets spliced directly
        // into the uploaded photo's filename below - cast it before use so an
        // attacker-controlled string (e.g. containing "../") can't escape the
        // uploads directory, matching how every other upload handler in this
        // app builds filenames from trusted/generated values only.
        $conducteurId = (int) $conducteurId;

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
