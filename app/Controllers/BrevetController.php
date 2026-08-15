<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BrevetController extends Controller
{
    public function imprimeur()
    {
        $db = Database::getInstance();
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;

        try {
            $sql = "SELECT * FROM conducteurs WHERE statut_brevet = 'nouveau'";
            $params = [];

            if ($dateDebut && $dateFin) {
                $sql .= " AND date_enregistrement BETWEEN ? AND ?";
                $params[] = $dateDebut;
                $params[] = $dateFin;
            }

            $sql .= " ORDER BY date_enregistrement DESC";
            $conducteurs = $db->fetchAll($sql, $params);

            $sqlEnCours = "SELECT * FROM conducteurs WHERE statut_brevet = 'en_cours_impression'";
            $paramsEnCours = [];

            if ($dateDebut && $dateFin) {
                $sqlEnCours .= " AND date_enregistrement BETWEEN ? AND ?";
                $paramsEnCours[] = $dateDebut;
                $paramsEnCours[] = $dateFin;
            }

            $sqlEnCours .= " ORDER BY date_enregistrement DESC";
            $conducteursEnCours = $db->fetchAll($sqlEnCours, $paramsEnCours);

            $sqlImprimes = "SELECT * FROM conducteurs WHERE statut_brevet = 'imprime'";
            $paramsImprimes = [];

            if ($dateDebut && $dateFin) {
                $sqlImprimes .= " AND date_enregistrement BETWEEN ? AND ?";
                $paramsImprimes[] = $dateDebut;
                $paramsImprimes[] = $dateFin;
            }

            $sqlImprimes .= " ORDER BY date_enregistrement DESC";
            $conducteursImprimes = $db->fetchAll($sqlImprimes, $paramsImprimes);
        } catch (\Exception $e) {
            error_log('[BrevetController::imprimeur] ' . $e->getMessage());
            $conducteurs = [];
            $conducteursEnCours = [];
            $conducteursImprimes = [];
        }

        $this->render('admin/imprimeur', [
            'pageTitle' => 'Imprimeur - Brevets',
            'currentPage' => '/admin/imprimeur',
            'conducteurs' => $conducteurs,
            'conducteursEnCours' => $conducteursEnCours,
            'conducteursImprimes' => $conducteursImprimes,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ], 'admin');
    }

    public function downloadExcel()
    {
        $db = Database::getInstance();
        $dateDebut = $_GET['date_debut'] ?? '';
        $dateFin = $_GET['date_fin'] ?? '';

        $sql = "SELECT * FROM conducteurs WHERE statut_brevet = 'nouveau'";
        $params = [];
        if ($dateDebut !== '' && $dateFin !== '') {
            $sql .= " AND date_enregistrement BETWEEN ? AND ?";
            $params[] = $dateDebut;
            $params[] = $dateFin;
        }
        $sql .= " ORDER BY date_enregistrement DESC";

        try {
            $conducteurs = $db->fetchAll($sql, $params);
        } catch (\Exception $e) {
            error_log('[BrevetController::downloadExcel] ' . $e->getMessage());
            header('Location: ' . BASE_PATH . '/admin/imprimeur?error=' . urlencode('Erreur lors de la génération du fichier Excel.'));
            exit;
        }

        $cheminPhotos = $_GET['chemin_photos'] ?? '';
        $cheminQrcodes = $_GET['chemin_qrcodes'] ?? '';

        // Normaliser les chemins (ajouter un séparateur final si besoin)
        if ($cheminPhotos !== '' && !preg_match('/[\/\\\\]$/', $cheminPhotos)) {
            $cheminPhotos .= '\\';
        }
        if ($cheminQrcodes !== '' && !preg_match('/[\/\\\\]$/', $cheminQrcodes)) {
            $cheminQrcodes .= '\\';
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Conducteurs');

        $lastCol = 'P';

        // ── Titre principal ──
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'MINISTÈRE DES TRANSPORTS — LISTE DES CONDUCTEURS');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0A1628']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // ── Sous-titre période ──
        $sheet->mergeCells("A2:{$lastCol}2");
        $dateDebutFmt = $dateDebut ? date('d/m/Y', strtotime($dateDebut)) : '';
        $dateFinFmt = $dateFin ? date('d/m/Y', strtotime($dateFin)) : '';
        $sheet->setCellValue('A2', "Période : {$dateDebutFmt} — {$dateFinFmt}   |   Total : " . count($conducteurs) . " conducteur(s)");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF334155']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(28);

        // ── En-têtes colonnes ──
        $headers = [
            'A' => 'ID',
            'B' => 'Nom',
            'C' => 'Prénom',
            'D' => 'Date de naissance',
            'E' => 'Lieu de naissance',
            'F' => 'Téléphone',
            'G' => 'Adresse',
            'H' => 'N° Permis',
            'I' => 'Catégorie',
            'J' => 'Expiration Permis',
            'K' => 'Association',
            'L' => 'Syndicat',
            'M' => 'Date Enregistrement',
            'N' => 'Statut',
            'O' => 'Lien Photo',
            'P' => 'Lien QR Code',
        ];

        $headerRow = 4;
        foreach ($headers as $col => $label) {
            $sheet->setCellValue($col . $headerRow, $label);
        }
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF3B82F6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF2563EB']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(30);

        // ── Colonne F (Téléphone) en format texte ──
        $sheet->getStyle('F1:F' . ($headerRow + count($conducteurs)))
              ->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // ── Données ──
        $dataRow = $headerRow + 1;
        foreach ($conducteurs as $i => $c) {
            $sheet->setCellValue("A{$dataRow}", $c['id']);
            $sheet->setCellValue("B{$dataRow}", $c['nom']);
            $sheet->setCellValue("C{$dataRow}", $c['prenom']);
            $sheet->setCellValue("D{$dataRow}", $c['date_naissance'] ? date('d/m/Y', strtotime($c['date_naissance'])) : '');
            $sheet->setCellValue("E{$dataRow}", $c['lieu_naissance'] ?? '');
            // Téléphone en texte explicite
            $sheet->setCellValueExplicit("F{$dataRow}", $c['telephone'] ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("G{$dataRow}", $c['adresse'] ?? '');
            $sheet->setCellValue("H{$dataRow}", $c['numero_permis']);
            $sheet->setCellValue("I{$dataRow}", $c['categorie_permis']);
            $sheet->setCellValue("J{$dataRow}", $c['date_expiration_permis'] ? date('d/m/Y', strtotime($c['date_expiration_permis'])) : '');
            $sheet->setCellValue("K{$dataRow}", $c['association'] ?? '');
            $sheet->setCellValue("L{$dataRow}", $c['syndicat'] ?? '');
            $sheet->setCellValue("M{$dataRow}", $c['date_enregistrement'] ? date('d/m/Y', strtotime($c['date_enregistrement'])) : '');
            $sheet->setCellValue("N{$dataRow}", ucfirst($c['statut'] ?? ''));

            // Lien photo
            $photoExt = !empty($c['photo_url']) ? pathinfo($c['photo_url'], PATHINFO_EXTENSION) : 'jpg';
            $photoFile = $cheminPhotos . $c['id'] . '.' . $photoExt;
            $sheet->setCellValue("O{$dataRow}", $photoFile);

            // Lien QR code
            $qrFile = $cheminQrcodes . $c['id'] . '.png';
            $sheet->setCellValue("P{$dataRow}", $qrFile);

            // Alternance de couleur (zébré)
            $bgColor = ($i % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
            $sheet->getStyle("A{$dataRow}:{$lastCol}{$dataRow}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($dataRow)->setRowHeight(22);

            $dataRow++;
        }

        // ── Largeur auto des colonnes ──
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Centrer certaines colonnes ──
        $lastRow = $dataRow - 1;
        if ($lastRow >= $headerRow + 1) {
            foreach (['A', 'D', 'I', 'J', 'M', 'N'] as $col) {
                $sheet->getStyle("{$col}" . ($headerRow + 1) . ":{$col}{$lastRow}")
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // ── Figer les volets (en-têtes visibles au scroll) ──
        $sheet->freezePane('A' . ($headerRow + 1));

        // ── Filtre automatique ──
        $sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$lastRow}");

        // ── Envoi du fichier ──
        $filename = "conducteurs_{$dateDebut}_{$dateFin}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        exit;
    }

    public function downloadPhotos()
    {
        $db = Database::getInstance();
        $dateDebut = $_GET['date_debut'] ?? '';
        $dateFin = $_GET['date_fin'] ?? '';

        $sql = "SELECT * FROM conducteurs WHERE statut_brevet = 'nouveau' AND photo_url IS NOT NULL";
        $params = [];
        if ($dateDebut !== '' && $dateFin !== '') {
            $sql .= " AND date_enregistrement BETWEEN ? AND ?";
            $params[] = $dateDebut;
            $params[] = $dateFin;
        }
        $sql .= " ORDER BY id";

        try {
            $conducteurs = $db->fetchAll($sql, $params);
        } catch (\Exception $e) {
            error_log('[BrevetController::downloadPhotos] ' . $e->getMessage());
            header('Location: ' . BASE_PATH . '/admin/imprimeur?error=' . urlencode('Erreur lors de la récupération des photos.'));
            exit;
        }

        $filename = "photos_conducteurs_{$dateDebut}_{$dateFin}.zip";
        $tmpFile = tempnam(sys_get_temp_dir(), 'photos_');

        $zip = new \ZipArchive();
        $openResult = $zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            error_log('[BrevetController::downloadPhotos] ZipArchive::open failed with code ' . $openResult);
            @unlink($tmpFile);
            header('Location: ' . BASE_PATH . '/admin/imprimeur?error=' . urlencode('Erreur lors de la création de l\'archive ZIP.'));
            exit;
        }

        // tmpFile is cleaned up on every exit path (success, exception, or early
        // return) via finally - previously only the happy path unlink()ed it.
        try {
            foreach ($conducteurs as $c) {
                $photoPath = ROOT_DIR . '/public/' . $c['photo_url'];
                if (file_exists($photoPath)) {
                    $extension = pathinfo($c['photo_url'], PATHINFO_EXTENSION);
                    $zip->addFile($photoPath, $c['id'] . '.' . $extension);
                }
            }

            $zip->close();

            // ZipArchive::close() deletes the temp file instead of writing a
            // valid empty archive when zero entries were added (e.g. no
            // conducteur in range still has its photo on disk) - write a
            // minimal empty ZIP by hand so the response stays a well-formed
            // archive instead of a missing file / broken Content-Length.
            if (!file_exists($tmpFile)) {
                file_put_contents($tmpFile, "PK\x05\x06" . str_repeat("\x00", 18));
            }

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($tmpFile));
            header('Cache-Control: no-cache, no-store, must-revalidate');

            readfile($tmpFile);
        } catch (\Exception $e) {
            error_log('[BrevetController::downloadPhotos] ' . $e->getMessage());
        } finally {
            @unlink($tmpFile);
        }
        exit;
    }

    public function downloadQrcodes()
    {
        $db = Database::getInstance();
        $dateDebut = $_GET['date_debut'] ?? '';
        $dateFin = $_GET['date_fin'] ?? '';

        $sql = "SELECT * FROM conducteurs WHERE statut_brevet = 'nouveau'";
        $params = [];
        if ($dateDebut !== '' && $dateFin !== '') {
            $sql .= " AND date_enregistrement BETWEEN ? AND ?";
            $params[] = $dateDebut;
            $params[] = $dateFin;
        }
        $sql .= " ORDER BY id";

        try {
            $conducteurs = $db->fetchAll($sql, $params);
        } catch (\Exception $e) {
            error_log('[BrevetController::downloadQrcodes] ' . $e->getMessage());
            header('Location: ' . BASE_PATH . '/admin/imprimeur?error=' . urlencode('Erreur lors de la récupération des conducteurs.'));
            exit;
        }

        $filename = "qrcodes_conducteurs_{$dateDebut}_{$dateFin}.zip";
        $tmpFile = tempnam(sys_get_temp_dir(), 'qrcodes_');

        $zip = new \ZipArchive();
        $openResult = $zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            error_log('[BrevetController::downloadQrcodes] ZipArchive::open failed with code ' . $openResult);
            @unlink($tmpFile);
            header('Location: ' . BASE_PATH . '/admin/imprimeur?error=' . urlencode('Erreur lors de la création de l\'archive ZIP.'));
            exit;
        }

        // Construire l'URL de base du site pour les QR codes
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $siteUrl = $protocol . '://' . $host . BASE_PATH;

        // tmpFile is cleaned up on every exit path (success, exception, or early
        // return) via finally - previously only the happy path unlink()ed it.
        try {
            foreach ($conducteurs as $c) {
                $verificationUrl = $siteUrl . '/verification/' . $c['id'];

                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&format=png&data=' . urlencode($verificationUrl);
                $ctx = stream_context_create(['http' => ['timeout' => 10]]);
                $qrImage = @file_get_contents($qrUrl, false, $ctx);

                if ($qrImage !== false) {
                    $zip->addFromString($c['id'] . '.png', $qrImage);
                }
            }

            $zip->close();

            // Same empty-archive quirk as downloadPhotos(): close() removes
            // the temp file rather than writing a valid empty ZIP when no
            // QR code was successfully generated for any conducteur in range.
            if (!file_exists($tmpFile)) {
                file_put_contents($tmpFile, "PK\x05\x06" . str_repeat("\x00", 18));
            }

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($tmpFile));
            header('Cache-Control: no-cache, no-store, must-revalidate');

            readfile($tmpFile);
        } catch (\Exception $e) {
            error_log('[BrevetController::downloadQrcodes] ' . $e->getMessage());
        } finally {
            @unlink($tmpFile);
        }
        exit;
    }

    public function marquerEnCoursImpression()
    {
        $db = Database::getInstance();
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $dateDebut = $data['date_debut'] ?? '';
        $dateFin = $data['date_fin'] ?? '';

        try {
            $sql = "UPDATE conducteurs SET statut_brevet = 'en_cours_impression' WHERE statut_brevet = 'nouveau'";
            $params = [];
            if ($dateDebut !== '' && $dateFin !== '') {
                $sql .= " AND date_enregistrement BETWEEN ? AND ?";
                $params[] = $dateDebut;
                $params[] = $dateFin;
            }
            $db->query($sql, $params);

            header('Location: ' . BASE_PATH . '/admin/imprimeur?date_debut=' . urlencode($dateDebut) . '&date_fin=' . urlencode($dateFin) . '&success=' . urlencode('Conducteurs marqués en cours d\'impression avec succès.'));
            exit;
        } catch (\Exception $e) {
            error_log('[BrevetController::marquerEnCoursImpression] ' . $e->getMessage());
            header('Location: ' . BASE_PATH . '/admin/imprimeur?error=' . urlencode('Erreur lors du marquage des brevets.'));
            exit;
        }
    }

    public function apiImprimeur()
    {
        $db = Database::getInstance();
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;
        $statut = $_GET['statut'] ?? 'nouveau';

        $sql = "SELECT * FROM conducteurs WHERE statut_brevet = ?";
        $params = [$statut];

        if ($dateDebut && $dateFin) {
            $sql .= " AND date_enregistrement BETWEEN ? AND ?";
            $params[] = $dateDebut;
            $params[] = $dateFin;
        }

        $sql .= " ORDER BY date_enregistrement DESC";

        try {
            $conducteurs = $db->fetchAll($sql, $params);
            $this->json($conducteurs);
        } catch (\Exception $e) {
            error_log('[BrevetController::apiImprimeur] ' . $e->getMessage());
            $this->json([]);
        }
    }

    public function receptionnaire()
    {
        $db = Database::getInstance();
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;

        try {
            $sql = "SELECT * FROM conducteurs WHERE statut_brevet = 'en_cours_impression'";
            $params = [];

            if ($dateDebut && $dateFin) {
                $sql .= " AND date_enregistrement BETWEEN ? AND ?";
                $params[] = $dateDebut;
                $params[] = $dateFin;
            }

            $sql .= " ORDER BY date_enregistrement DESC";
            $conducteurs = $db->fetchAll($sql, $params);

            $sqlImprimes = "SELECT * FROM conducteurs WHERE statut_brevet = 'imprime'";
            $paramsImprimes = [];

            if ($dateDebut && $dateFin) {
                $sqlImprimes .= " AND date_enregistrement BETWEEN ? AND ?";
                $paramsImprimes[] = $dateDebut;
                $paramsImprimes[] = $dateFin;
            }

            $sqlImprimes .= " ORDER BY date_enregistrement DESC";
            $conducteursImprimes = $db->fetchAll($sqlImprimes, $paramsImprimes);
        } catch (\Exception $e) {
            error_log('[BrevetController::receptionnaire] ' . $e->getMessage());
            $conducteurs = [];
            $conducteursImprimes = [];
        }

        $this->render('admin/receptionnaire', [
            'pageTitle' => 'Réceptionnaire - Brevets',
            'currentPage' => '/admin/receptionnaire',
            'conducteurs' => $conducteurs,
            'conducteursImprimes' => $conducteursImprimes,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ], 'admin');
    }

    public function apiReceptionnaire()
    {
        $db = Database::getInstance();
        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;
        $statut = $_GET['statut'] ?? 'en_cours_impression';

        $sql = "SELECT * FROM conducteurs WHERE statut_brevet = ?";
        $params = [$statut];

        if ($dateDebut && $dateFin) {
            $sql .= " AND date_enregistrement BETWEEN ? AND ?";
            $params[] = $dateDebut;
            $params[] = $dateFin;
        }

        $sql .= " ORDER BY date_enregistrement DESC";

        try {
            $conducteurs = $db->fetchAll($sql, $params);
            $this->json($conducteurs);
        } catch (\Exception $e) {
            error_log('[BrevetController::apiReceptionnaire] ' . $e->getMessage());
            $this->json([]);
        }
    }

    public function confirmerImpression()
    {
        $db = Database::getInstance();
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $conducteurId = $data['conducteur_id'] ?? null;

        if (!$conducteurId) {
            $this->json(['error' => 'conducteur_id requis'], 400);
        }

        try {
            $db->query(
                "UPDATE conducteurs SET statut_brevet = 'imprime' WHERE id = ? AND statut_brevet = 'en_cours_impression'",
                [$conducteurId]
            );
            $this->json(['success' => true]);
        } catch (\Exception $e) {
            error_log('[BrevetController::confirmerImpression] ' . $e->getMessage());
            $this->json(['error' => 'Erreur lors de la confirmation.'], 500);
        }
    }

    public function confirmerImpressionLot()
    {
        $db = Database::getInstance();
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $conducteurIds = $data['conducteur_ids'] ?? null;
        $dateDebut = $data['date_debut'] ?? null;
        $dateFin = $data['date_fin'] ?? null;

        try {
            if ($conducteurIds && is_array($conducteurIds)) {
                $placeholders = implode(',', array_fill(0, count($conducteurIds), '?'));
                $db->query(
                    "UPDATE conducteurs SET statut_brevet = 'imprime' WHERE id IN ($placeholders) AND statut_brevet = 'en_cours_impression'",
                    $conducteurIds
                );
            } elseif ($dateDebut && $dateFin) {
                $db->query(
                    "UPDATE conducteurs SET statut_brevet = 'imprime' WHERE statut_brevet = 'en_cours_impression' AND date_enregistrement BETWEEN ? AND ?",
                    [$dateDebut, $dateFin]
                );
            } else {
                $this->json(['error' => 'conducteur_ids ou date_debut/date_fin requis'], 400);
            }

            $this->json(['success' => true]);
        } catch (\Exception $e) {
            error_log('[BrevetController::confirmerImpressionLot] ' . $e->getMessage());
            $this->json(['error' => 'Erreur lors de la confirmation en lot.'], 500);
        }
    }

    /**
     * Afficher la carte brevet (recto + verso) prête à imprimer
     */
    public function carteBrevet($id)
    {
        $db = Database::getInstance();

        try {
            $conducteur = $db->fetchOne("SELECT * FROM conducteurs WHERE id = ?", [$id]);
        } catch (\Exception $e) {
            error_log('[BrevetController::carteBrevet] ' . $e->getMessage());
            header('Location: ' . BASE_PATH . '/admin/imprimeur?error=' . urlencode('Erreur lors du chargement du conducteur.'));
            exit;
        }

        if (!$conducteur) {
            header('Location: ' . BASE_PATH . '/admin/imprimeur?error=Conducteur introuvable');
            exit;
        }

        // Construire l'URL de vérification pour le QR code
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $siteUrl  = $protocol . '://' . $host . BASE_PATH;
        $verificationUrl = $siteUrl . '/verification/' . $id;

        // QR code en base64 (évite CORS avec html2canvas)
        $qrApiUrl  = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&format=png&data=' . urlencode($verificationUrl);
        $qrCodeUrl = $qrApiUrl; // fallback src
        $qrBase64  = null;
        $ctx = stream_context_create(['http' => ['timeout' => 8]]);
        $qrRaw = @file_get_contents($qrApiUrl, false, $ctx);
        if ($qrRaw !== false) {
            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrRaw);
        }

        // Photo conducteur
        $photoBase64 = null;
        if (!empty($conducteur['photo_url'])) {
            $photoPath = ROOT_DIR . '/public/' . $conducteur['photo_url'];
            if (file_exists($photoPath)) {
                $ext = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION));
                $mime = in_array($ext, ['jpg','jpeg']) ? 'jpeg' : $ext;
                $photoBase64 = 'data:image/' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
            }
        }

        // Calculer l'âge
        $age = null;
        if (!empty($conducteur['date_naissance'])) {
            $age = (int) date_diff(new \DateTime($conducteur['date_naissance']), new \DateTime())->y;
        }

        // Récupérer la configuration de la carte
        $carteLogoGauche = ConfigController::get('carte_logo_gauche', '');
        $carteLogoDroite = ConfigController::get('carte_logo_droite', '');
        $carteSignature = ConfigController::get('carte_signature', '');
        $carteTitreGauche = ConfigController::get('carte_titre_gauche', 'République Démocratique du Congo');
        $carteTitreDroite = ConfigController::get('carte_titre_droite', 'Direction Provinciale de la CNPR');

        // Convertir les logos en base64 si nécessaire
        $logoGaucheBase64 = null;
        if (!empty($carteLogoGauche)) {
            $logoPath = ROOT_DIR . '/public/' . $carteLogoGauche;
            if (file_exists($logoPath)) {
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = $ext === 'svg' ? 'svg+xml' : $ext;
                $mime = $mime === 'jpeg' ? 'jpeg' : $mime;
                $logoGaucheBase64 = 'data:image/' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

        $logoDroiteBase64 = null;
        if (!empty($carteLogoDroite)) {
            $logoPath = ROOT_DIR . '/public/' . $carteLogoDroite;
            if (file_exists($logoPath)) {
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = $ext === 'svg' ? 'svg+xml' : $ext;
                $mime = $mime === 'jpeg' ? 'jpeg' : $mime;
                $logoDroiteBase64 = 'data:image/' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

        $signatureBase64 = null;
        if (!empty($carteSignature)) {
            $sigPath = ROOT_DIR . '/public/' . $carteSignature;
            if (file_exists($sigPath)) {
                $ext = strtolower(pathinfo($sigPath, PATHINFO_EXTENSION));
                $mime = $ext === 'svg' ? 'svg+xml' : $ext;
                $mime = $mime === 'jpeg' ? 'jpeg' : $mime;
                $signatureBase64 = 'data:image/' . $mime . ';base64,' . base64_encode(file_get_contents($sigPath));
            }
        }

        $this->render('admin/carte-brevet', [
            'pageTitle'       => 'Carte Brevet — ' . $conducteur['prenom'] . ' ' . $conducteur['nom'],
            'conducteur'      => $conducteur,
            'photoBase64'     => $photoBase64,
            'qrCodeUrl'       => $qrBase64 ?? $qrCodeUrl,
            'verificationUrl' => $verificationUrl,
            'age'             => $age,
            'carteLogoGauche' => $logoGaucheBase64,
            'carteLogoDroite' => $logoDroiteBase64,
            'carteSignature'  => $signatureBase64,
            'carteTitreGauche' => $carteTitreGauche,
            'carteTitreDroite' => $carteTitreDroite,
        ], 'none');
    }

    /**
     * Télécharger la photo d'un seul conducteur
     */
    public function downloadSinglePhoto($id)
    {
        $db = Database::getInstance();

        try {
            $conducteur = $db->fetch("SELECT * FROM conducteurs WHERE id = ?", [$id]);
        } catch (\Exception $e) {
            error_log('[BrevetController::downloadSinglePhoto] ' . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Erreur lors du chargement du conducteur.'];
            $this->redirect('/admin/imprimeur');
            return;
        }

        if (!$conducteur || empty($conducteur['photo_url'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Photo non trouvée pour ce conducteur.'];
            $this->redirect('/admin/imprimeur');
            return;
        }

        $photoPath = ROOT_DIR . '/public/' . $conducteur['photo_url'];
        
        if (!file_exists($photoPath)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Le fichier photo n\'existe pas sur le serveur.'];
            $this->redirect('/admin/imprimeur');
            return;
        }

        $extension = pathinfo($conducteur['photo_url'], PATHINFO_EXTENSION);
        $filename = $conducteur['prenom'] . '_' . $conducteur['nom'] . '_photo.' . $extension;

        header('Content-Type: image/' . $extension);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($photoPath));
        readfile($photoPath);
        exit;
    }

    /**
     * Télécharger le QR code d'un seul conducteur
     */
    public function downloadSingleQrcode($id)
    {
        $db = Database::getInstance();

        try {
            $conducteur = $db->fetch("SELECT * FROM conducteurs WHERE id = ?", [$id]);
        } catch (\Exception $e) {
            error_log('[BrevetController::downloadSingleQrcode] ' . $e->getMessage());
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Erreur lors du chargement du conducteur.'];
            $this->redirect('/admin/imprimeur');
            return;
        }

        if (!$conducteur) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Conducteur non trouvé.'];
            $this->redirect('/admin/imprimeur');
            return;
        }

        // Construire l'URL de vérification
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $siteUrl = $protocol . '://' . $host . BASE_PATH;
        $verificationUrl = $siteUrl . '/verification/' . $id;

        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&format=png&data=' . urlencode($verificationUrl);
        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $qrImage = @file_get_contents($qrUrl, false, $ctx);

        if ($qrImage === false) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Impossible de générer le QR code.'];
            $this->redirect('/admin/imprimeur');
            return;
        }

        $filename = $conducteur['prenom'] . '_' . $conducteur['nom'] . '_qrcode.png';

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($qrImage));
        echo $qrImage;
        exit;
    }
}
