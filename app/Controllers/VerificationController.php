<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Env;

class VerificationController extends Controller
{
    // Requests allowed per IP in the trailing window before this public,
    // unauthenticated lookup starts refusing - see verification_attempts in
    // database/schema.sql for why this exists. Only applies to show() below
    // (the public HTML page) - showApi() is a separate, shared-secret-gated
    // surface with a different threat model, see its own comment.
    private const RATE_LIMIT_MAX_ATTEMPTS = 15;
    private const RATE_LIMIT_WINDOW_MINUTES = 10;

    // Header e-taxe-kisangani's quittance.info portal sends its shared
    // secret in when calling showApi() below.
    private const API_KEY_HEADER = 'HTTP_X_CNPR_API_KEY';

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

        $conducteur = $this->publicSafeFields($identifiant);

        $this->render('verification/show', [
            'pageTitle' => 'Vérification de brevet',
            'conducteur' => $conducteur,
        ], 'none');
    }

    /**
     * Server-to-server counterpart of show() - same lookup, same public-safe
     * field list (via publicSafeFields() below), JSON instead of HTML. Built
     * for e-taxe-kisangani's quittance.info portal to verify a ROC- driver
     * identifiant without either app touching the other's database directly.
     *
     * Deliberately NOT gated by the show()/verification_attempts per-IP
     * throttle above: every call here arrives from e-taxe-kisangani's own
     * server, never the end user's browser, so an IP-based limit would only
     * ever measure "how much has that one server called us" - it would
     * either never trip under real traffic, or trip once and lock out every
     * quittance.info visitor simultaneously. The real defense against abuse
     * is quittance.info's own per-end-user rate limit (already in place
     * before it ever reaches this route) plus the shared-secret gate below,
     * which is the only thing standing between this route and the public
     * internet - fails closed if unconfigured, never silently open.
     */
    public function showApi($identifiant)
    {
        $apiKey = Env::get('CNPR_VERIFICATION_API_KEY', '');

        if ($apiKey === '') {
            error_log('[VerificationController::showApi] CNPR_VERIFICATION_API_KEY non configuré - requête refusée.');
            $this->json(['error' => 'Service indisponible'], 503);
            return;
        }

        $providedKey = $_SERVER[self::API_KEY_HEADER] ?? '';

        if (!hash_equals($apiKey, $providedKey)) {
            $this->json(['error' => 'Non autorisé'], 401);
            return;
        }

        $conducteur = $this->publicSafeFields($identifiant);

        if ($conducteur === null) {
            $this->json(['found' => false], 404);
            return;
        }

        $this->json(['found' => true, 'conducteur' => $conducteur]);
    }

    /**
     * Single source of truth for what a public, unauthenticated caller -
     * whether a human on show() or e-taxe-kisangani's server on showApi() -
     * is allowed to see about a conducteur. phone/adresse/date et lieu de
     * naissance/association/syndicat are deliberately excluded: they're not
     * needed to visually/administratively confirm a brevet is genuine, and
     * were previously exposed to anyone who could guess or enumerate an
     * identifiant (identifiant_conducteur_seq generates sequential,
     * guessable values). Keeping both callers on this one method means a
     * future PII trim here can't accidentally apply to only one of them.
     */
    private function publicSafeFields(string $identifiant): ?array
    {
        $db = Database::getInstance();

        try {
            $conducteur = $db->fetchOne(
                "SELECT id, nom, prenom, numero_permis, categorie_permis,
                        date_expiration_permis, photo_url,
                        date_enregistrement, statut, statut_brevet
                 FROM conducteurs WHERE numero_permis = ?",
                [$identifiant]
            );
        } catch (\Exception $e) {
            return null;
        }

        // PDOStatement::fetch() (Database::fetchOne()) returns false, not
        // null, when no row matches.
        return $conducteur === false ? null : $conducteur;
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
