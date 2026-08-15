<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Core\SmsService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdminController extends Controller
{
    /**
     * Handle file upload
     */
    private function handleFileUpload($fileKey, $uploadDir = 'uploads/conducteurs')
    {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$fileKey];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Create directory if not exists
        $fullPath = ROOT_DIR . '/public/' . $uploadDir;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $destination = $fullPath . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $uploadDir . '/' . $filename;
        }

        return null;
    }

    public function dashboard()
    {
        $db = Database::getInstance();
        $user = Auth::user();
        $role = $user['role'] ?? 'citoyen';

        $stats = [];
        $brevetStats = [];
        $recentActivity = [];
        $vehicleDistribution = [];
        $quickActions = [];
        $dashboardTitle = 'Tableau de bord';
        $dashboardSubtitle = 'Bienvenue sur le portail d\'administration du Ministère des Transports';

        try {
            switch ($role) {
                case 'admin':
                case 'minister_admin':
                    // Full dashboard — all stats
                    $totalConducteurs = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs")['total'] ?? 0;
                    $brevetsNouveau = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'nouveau'")['total'] ?? 0;
                    $brevetsEnCours = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'en_cours_impression'")['total'] ?? 0;
                    $brevetsImprimes = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'imprime'")['total'] ?? 0;
                    $totalPaiementsBrevet = $db->fetchOne("SELECT COALESCE(SUM(montant), 0) as total FROM paiements_brevets")['total'] ?? 0;
                    $conducteursPayes = $db->fetchOne("SELECT COUNT(*) as total FROM paiements_brevets")['total'] ?? 0;

                    $stats = [
                        ['icon' => 'user-check', 'label' => 'Total Conducteurs', 'value' => $totalConducteurs, 'color' => '#3B82F6', 'href' => '/admin/conducteurs'],
                        ['icon' => 'credit-card', 'label' => 'Total Brévets', 'value' => $brevetsNouveau + $brevetsEnCours + $brevetsImprimes, 'color' => '#059669', 'href' => '/admin/imprimeur'],
                        ['icon' => 'wallet', 'label' => 'Paiements Brevet', 'value' => $conducteursPayes, 'color' => '#7C3AED', 'href' => '/admin/paiement'],
                        ['icon' => 'dollar-sign', 'label' => 'Total Perçu', 'value' => $totalPaiementsBrevet, 'color' => '#D97706', 'href' => '/admin/paiement'],
                    ];

                    $brevetStats = [
                        ['icon' => 'file-plus', 'label' => 'Nouveaux brevets', 'value' => $brevetsNouveau, 'color' => '#6366F1', 'href' => '/admin/imprimeur'],
                        ['icon' => 'printer', 'label' => 'En cours d\'impression', 'value' => $brevetsEnCours, 'color' => '#F59E0B', 'href' => '/admin/imprimeur'],
                        ['icon' => 'check-circle', 'label' => 'Brevets imprimés', 'value' => $brevetsImprimes, 'color' => '#10B981', 'href' => '/admin/receptionnaire'],
                    ];

                    $quickActions = [
                        ['label' => 'Nouveau conducteur', 'icon' => 'user-plus', 'color' => '#3B82F6', 'href' => '/admin/conducteurs/ajouter'],
                        ['label' => 'Nouveau véhicule', 'icon' => 'car-front', 'color' => '#8B5CF6', 'href' => '/admin/vehicules'],
                        ['label' => 'Enregistrer paiement', 'icon' => 'wallet', 'color' => '#D97706', 'href' => '/admin/paiement'],
                        ['label' => 'Imprimer brevets', 'icon' => 'printer', 'color' => '#6366F1', 'href' => '/admin/imprimeur'],
                        ['label' => 'Réception brevets', 'icon' => 'clipboard-check', 'color' => '#10B981', 'href' => '/admin/receptionnaire'],
                        ['label' => 'Gérer utilisateurs', 'icon' => 'users', 'color' => '#059669', 'href' => '/admin/utilisateurs'],
                    ];
                    break;

                case 'agent':
                    // Agent: conducteurs, véhicules, paiements
                    $totalConducteurs = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs")['total'] ?? 0;
                    $totalVehicules = $db->fetchOne("SELECT COUNT(*) as total FROM vehicules")['total'] ?? 0;
                    $conducteursPayes = $db->fetchOne("SELECT COUNT(*) as total FROM paiements_brevets")['total'] ?? 0;
                    $totalPaiementsBrevet = $db->fetchOne("SELECT COALESCE(SUM(montant), 0) as total FROM paiements_brevets")['total'] ?? 0;

                    $dashboardSubtitle = 'Gestion des conducteurs et véhicules';

                    $stats = [
                        ['icon' => 'user-check', 'label' => 'Total Conducteurs', 'value' => $totalConducteurs, 'color' => '#3B82F6', 'href' => '/admin/conducteurs'],
                        ['icon' => 'car', 'label' => 'Total Véhicules', 'value' => $totalVehicules, 'color' => '#8B5CF6', 'href' => '/admin/vehicules'],
                        ['icon' => 'wallet', 'label' => 'Paiements Brevet', 'value' => $conducteursPayes, 'color' => '#7C3AED', 'href' => '/admin/paiement'],
                        ['icon' => 'dollar-sign', 'label' => 'Total Perçu', 'value' => $totalPaiementsBrevet, 'color' => '#D97706', 'href' => '/admin/paiement'],
                    ];

                    $quickActions = [
                        ['label' => 'Nouveau conducteur', 'icon' => 'user-plus', 'color' => '#3B82F6', 'href' => '/admin/conducteurs/ajouter'],
                        ['label' => 'Nouveau véhicule', 'icon' => 'car-front', 'color' => '#8B5CF6', 'href' => '/admin/vehicules'],
                        ['label' => 'Enregistrer paiement', 'icon' => 'wallet', 'color' => '#D97706', 'href' => '/admin/paiement'],
                    ];
                    break;

                case 'operateur_saisie':
                    // OPS: conducteurs uniquement, pas de paiements
                    $totalConducteurs = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs")['total'] ?? 0;
                    $conducteursActifs = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut = 'actif'")['total'] ?? 0;

                    $dashboardSubtitle = 'Saisie et gestion des conducteurs';

                    $stats = [
                        ['icon' => 'user-check', 'label' => 'Total Conducteurs', 'value' => $totalConducteurs, 'color' => '#3B82F6', 'href' => '/admin/conducteurs'],
                        ['icon' => 'check-circle', 'label' => 'Conducteurs actifs', 'value' => $conducteursActifs, 'color' => '#10B981', 'href' => '/admin/conducteurs'],
                    ];

                    $quickActions = [
                        ['label' => 'Nouveau conducteur', 'icon' => 'user-plus', 'color' => '#3B82F6', 'href' => '/admin/conducteurs/ajouter'],
                    ];
                    break;

                case 'validateur':
                    // Validateur: paiements only
                    $conducteursPayes = $db->fetchOne("SELECT COUNT(*) as total FROM paiements_brevets")['total'] ?? 0;
                    $totalPaiementsBrevet = $db->fetchOne("SELECT COALESCE(SUM(montant), 0) as total FROM paiements_brevets")['total'] ?? 0;
                    $paiementsAujourdhui = $db->fetchOne("SELECT COUNT(*) as total FROM paiements_brevets WHERE date_paiement = CURRENT_DATE")['total'] ?? 0;

                    $dashboardSubtitle = 'Validation des paiements de brevets';

                    $stats = [
                        ['icon' => 'wallet', 'label' => 'Total Paiements', 'value' => $conducteursPayes, 'color' => '#7C3AED', 'href' => '/admin/paiement'],
                        ['icon' => 'dollar-sign', 'label' => 'Total Perçu', 'value' => $totalPaiementsBrevet, 'color' => '#D97706', 'href' => '/admin/paiement'],
                        ['icon' => 'check-circle', 'label' => 'Paiements aujourd\'hui', 'value' => $paiementsAujourdhui, 'color' => '#10B981', 'href' => '/admin/paiement'],
                    ];

                    $quickActions = [
                        ['label' => 'Enregistrer paiement', 'icon' => 'wallet', 'color' => '#D97706', 'href' => '/admin/paiement'],
                    ];
                    break;

                case 'imprimeur':
                    // Imprimeur: brevets to print
                    $brevetsNouveau = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'nouveau'")['total'] ?? 0;
                    $brevetsEnCours = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'en_cours_impression'")['total'] ?? 0;

                    $dashboardSubtitle = 'Gestion de l\'impression des brevets';

                    $stats = [
                        ['icon' => 'file-plus', 'label' => 'Brevets à imprimer', 'value' => $brevetsNouveau, 'color' => '#6366F1', 'href' => '/admin/imprimeur'],
                        ['icon' => 'printer', 'label' => 'En cours d\'impression', 'value' => $brevetsEnCours, 'color' => '#F59E0B', 'href' => '/admin/imprimeur'],
                    ];

                    $quickActions = [
                        ['label' => 'Imprimer brevets', 'icon' => 'printer', 'color' => '#6366F1', 'href' => '/admin/imprimeur'],
                    ];
                    break;

                case 'receveur':
                case 'receptionnaire':
                    // Réceptionnaire: brevets printed to receive
                    $brevetsImprimes = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'imprime'")['total'] ?? 0;
                    $brevetsEnCours = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'en_cours_impression'")['total'] ?? 0;

                    $dashboardSubtitle = 'Réception et confirmation des brevets';

                    $stats = [
                        ['icon' => 'printer', 'label' => 'En cours d\'impression', 'value' => $brevetsEnCours, 'color' => '#F59E0B', 'href' => '/admin/receptionnaire'],
                        ['icon' => 'check-circle', 'label' => 'Brevets imprimés', 'value' => $brevetsImprimes, 'color' => '#10B981', 'href' => '/admin/receptionnaire'],
                    ];

                    $quickActions = [
                        ['label' => 'Réception brevets', 'icon' => 'clipboard-check', 'color' => '#10B981', 'href' => '/admin/receptionnaire'],
                    ];
                    break;

                case 'inspecteur':
                    // Inspecteur: inspections, sanctions
                    $totalInspections = $db->fetchOne("SELECT COUNT(*) as total FROM inspections WHERE inspecteur_id = ?", [$user['id']])['total'] ?? 0;
                    $inspectionsEnCours = $db->fetchOne("SELECT COUNT(*) as total FROM inspections WHERE inspecteur_id = ? AND statut = 'en_cours'", [$user['id']])['total'] ?? 0;
                    $totalSanctions = $db->fetchOne("SELECT COUNT(*) as total FROM sanctions WHERE inspecteur_id = ?", [$user['id']])['total'] ?? 0;

                    $dashboardSubtitle = 'Suivi de vos inspections et contrôles';

                    $stats = [
                        ['icon' => 'user-check', 'label' => 'Mes inspections', 'value' => $totalInspections, 'color' => '#3B82F6', 'href' => '/admin'],
                        ['icon' => 'clock', 'label' => 'En cours', 'value' => $inspectionsEnCours, 'color' => '#F59E0B', 'href' => '/admin'],
                        ['icon' => 'alert-triangle', 'label' => 'Sanctions émises', 'value' => $totalSanctions, 'color' => '#EF4444', 'href' => '/admin'],
                    ];
                    break;

                case 'gestionnaire_parking':
                    // Gestionnaire parking: parkings
                    $totalParkings = $db->fetchOne("SELECT COUNT(*) as total FROM parkings WHERE responsable_id = ?", [$user['id']])['total'] ?? 0;
                    $placesOccupees = $db->fetchOne("SELECT COUNT(*) as total FROM stationnement WHERE statut = 'en_cours'")['total'] ?? 0;

                    $dashboardSubtitle = 'Gestion de vos parkings';

                    $stats = [
                        ['icon' => 'car', 'label' => 'Mes parkings', 'value' => $totalParkings, 'color' => '#059669', 'href' => '/admin/parkings'],
                        ['icon' => 'clock', 'label' => 'Places occupées', 'value' => $placesOccupees, 'color' => '#F59E0B', 'href' => '/admin/parkings'],
                    ];

                    $quickActions = [
                        ['label' => 'Gérer parkings', 'icon' => 'car-front', 'color' => '#059669', 'href' => '/admin/parkings'],
                    ];
                    break;

                case 'conducteur':
                    // Conducteur: own profile info
                    $conducteur = $db->fetchOne("SELECT * FROM conducteurs WHERE utilisateur_id = ?", [$user['id']]);
                    $mesPaiements = $conducteur ? ($db->fetchOne("SELECT COUNT(*) as total FROM paiements_brevets WHERE conducteur_id = ?", [$conducteur['id']])['total'] ?? 0) : 0;

                    $dashboardSubtitle = 'Votre espace conducteur';

                    $stats = [
                        ['icon' => 'user-check', 'label' => 'Mon statut', 'value' => $conducteur ? ucfirst($conducteur['statut'] ?? 'N/A') : 'Non enregistré', 'color' => '#3B82F6', 'href' => '/profile'],
                        ['icon' => 'wallet', 'label' => 'Mes paiements', 'value' => $mesPaiements, 'color' => '#7C3AED', 'href' => '/profile'],
                    ];
                    break;

                case 'transporteur':
                    // Transporteur: own vehicles
                    $mesVehicules = $db->fetchOne("SELECT COUNT(*) as total FROM vehicules WHERE proprietaire_id = ?", [$user['id']])['total'] ?? 0;

                    $dashboardSubtitle = 'Gestion de votre flotte de véhicules';

                    $stats = [
                        ['icon' => 'car', 'label' => 'Mes véhicules', 'value' => $mesVehicules, 'color' => '#8B5CF6', 'href' => '/profile'],
                    ];
                    break;

                case 'citoyen':
                default:
                    // Citoyen: signalements
                    $mesSignalements = $db->fetchOne("SELECT COUNT(*) as total FROM signalements WHERE citoyen_id = ?", [$user['id']])['total'] ?? 0;
                    $signalementsTraites = $db->fetchOne("SELECT COUNT(*) as total FROM signalements WHERE citoyen_id = ? AND statut = 'traite'", [$user['id']])['total'] ?? 0;

                    $dashboardSubtitle = 'Votre espace citoyen';

                    $stats = [
                        ['icon' => 'alert-triangle', 'label' => 'Mes signalements', 'value' => $mesSignalements, 'color' => '#3B82F6', 'href' => '/profile'],
                        ['icon' => 'check-circle', 'label' => 'Traités', 'value' => $signalementsTraites, 'color' => '#10B981', 'href' => '/profile'],
                    ];
                    break;
            }

            // Recent activity — only for roles that manage brevets
            if (in_array($role, ['admin', 'minister_admin', 'agent', 'operateur_saisie', 'imprimeur', 'receveur', 'receptionnaire'])) {
                $recentRows = $db->fetchAll(
                    "SELECT id, nom, prenom, statut_brevet, date_modification FROM conducteurs ORDER BY date_modification DESC LIMIT 6"
                );
                foreach ($recentRows as $row) {
                    $labelMap = [
                        'nouveau' => 'Nouveau brevet enregistré',
                        'en_cours_impression' => 'Brevet envoyé à l\'impression',
                        'imprime' => 'Brevet imprimé confirmé',
                    ];
                    $iconMap = [
                        'nouveau' => 'file-plus',
                        'en_cours_impression' => 'printer',
                        'imprime' => 'check-circle',
                    ];
                    $colorMap = [
                        'nouveau' => '#6366F1',
                        'en_cours_impression' => '#F59E0B',
                        'imprime' => '#10B981',
                    ];
                    $statut = $row['statut_brevet'] ?? 'nouveau';
                    $diff = time() - strtotime($row['date_modification']);
                    if ($diff < 60) $timeStr = 'Il y a ' . $diff . ' s';
                    elseif ($diff < 3600) $timeStr = 'Il y a ' . floor($diff / 60) . ' min';
                    elseif ($diff < 86400) $timeStr = 'Il y a ' . floor($diff / 3600) . ' h';
                    else $timeStr = 'Il y a ' . floor($diff / 86400) . ' j';

                    $recentActivity[] = [
                        'icon' => $iconMap[$statut] ?? 'file-plus',
                        'label' => $labelMap[$statut] ?? 'Activité brevet',
                        'name' => ($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? ''),
                        'time' => $timeStr,
                        'color' => $colorMap[$statut] ?? '#6366F1',
                    ];
                }
            }

            // Vehicle distribution — only for admin/agent roles
            if (in_array($role, ['admin', 'minister_admin', 'agent'])) {
                $vTypes = $db->fetchAll("SELECT type_vehicule, COUNT(*) as cnt FROM vehicules GROUP BY type_vehicule ORDER BY cnt DESC");
                $totalV = array_sum(array_column($vTypes, 'cnt'));
                $distColors = ['#3B82F6', '#8B5CF6', '#059669', '#D97706', '#EF4444', '#EC4899', '#0EA5E9'];
                foreach ($vTypes as $i => $v) {
                    $pct = $totalV > 0 ? round($v['cnt'] / $totalV * 100) : 0;
                    $vehicleDistribution[] = [
                        'type' => ucfirst($v['type_vehicule'] ?? ''),
                        'count' => (int)$v['cnt'],
                        'percent' => $pct,
                        'color' => $distColors[$i % count($distColors)],
                    ];
                }
            }
        } catch (\Exception $e) {
            // Keep defaults (empty arrays)
        }

        $this->render('admin/dashboard', [
            'pageTitle' => 'Tableau de bord',
            'currentPage' => '/admin',
            'stats' => $stats,
            'brevetStats' => $brevetStats,
            'recentActivity' => $recentActivity,
            'vehicleDistribution' => $vehicleDistribution,
            'quickActions' => $quickActions,
            'dashboardSubtitle' => $dashboardSubtitle,
        ], 'admin');
    }

    public function utilisateurs()
    {
        $db = Database::getInstance();

        try {
            $utilisateurs = $db->fetchAll("SELECT id, nom, prenom, email, telephone, role, statut, date_creation, derniere_connexion FROM utilisateurs ORDER BY date_creation DESC");
        } catch (\Exception $e) {
            $utilisateurs = [];
        }

        $this->render('admin/utilisateurs', [
            'pageTitle' => 'Utilisateurs',
            'currentPage' => '/admin/utilisateurs',
            'utilisateurs' => $utilisateurs,
        ], 'admin');
    }

    public function conducteurs()
    {
        $db = Database::getInstance();

        try {
            $conducteurs = $db->fetchAll(
                "SELECT c.*, u.email 
                 FROM conducteurs c 
                 LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id 
                 ORDER BY c.date_creation DESC"
            );
        } catch (\Exception $e) {
            $conducteurs = [];
        }

        $this->render('admin/conducteurs', [
            'pageTitle' => 'Conducteurs',
            'currentPage' => '/admin/conducteurs',
            'conducteurs' => $conducteurs,
        ], 'admin');
    }

    public function vehicules()
    {
        $db = Database::getInstance();

        try {
            $vehicules = $db->fetchAll(
                "SELECT v.*, u.nom as proprietaire_utilisateur_nom, u.prenom as proprietaire_utilisateur_prenom
                 FROM vehicules v 
                 LEFT JOIN utilisateurs u ON v.proprietaire_id = u.id 
                 ORDER BY v.date_creation DESC"
            );
        } catch (\Exception $e) {
            $vehicules = [];
        }

        $this->render('admin/vehicules', [
            'pageTitle' => 'Véhicules',
            'currentPage' => '/admin/vehicules',
            'vehicules' => $vehicules,
        ], 'admin');
    }

    public function taxes()
    {
        $db = Database::getInstance();

        try {
            $taxes = $db->fetchAll("SELECT * FROM taxes ORDER BY date_creation DESC");
            $paiements = $db->fetchAll(
                "SELECT p.*, t.type_taxe, c.nom as conducteur_nom, c.prenom as conducteur_prenom, v.numero_plaque
                 FROM paiements p
                 LEFT JOIN taxes t ON p.taxe_id = t.id
                 LEFT JOIN conducteurs c ON p.conducteur_id = c.id
                 LEFT JOIN vehicules v ON p.vehicule_id = v.id
                 ORDER BY p.date_paiement DESC
                 LIMIT 100"
            );
        } catch (\Exception $e) {
            $taxes = [];
            $paiements = [];
        }

        $this->render('admin/taxes', [
            'pageTitle' => 'Taxes & Paiements',
            'currentPage' => '/admin/taxes',
            'taxes' => $taxes,
            'paiements' => $paiements,
        ], 'admin');
    }

    public function paiement()
    {
        $db = Database::getInstance();

        $search = $_GET['search'] ?? '';
        $date_debut = $_GET['date_debut'] ?? '';
        $date_fin = $_GET['date_fin'] ?? '';

        try {
            // Rechercher des conducteurs (pour l'enregistrement de paiement)
            $conducteurs = [];
            if (!empty($search)) {
                $conducteurs = $db->fetchAll(
                    "SELECT c.*, pb.montant as paiement_montant, pb.reference_paiement as paiement_reference, pb.date_paiement as paiement_date
                     FROM conducteurs c
                     LEFT JOIN paiements_brevets pb ON c.id = pb.conducteur_id
                     WHERE c.nom LIKE ? OR c.prenom LIKE ? OR c.telephone LIKE ? OR c.numero_permis LIKE ?
                     ORDER BY c.date_creation DESC
                     LIMIT 20",
                    ["%$search%", "%$search%", "%$search%", "%$search%"]
                );
            }

            // Liste des paiements validés (avec filtres)
            $whereClause = "1=1";
            $params = [];
            
            if (!empty($date_debut)) {
                $whereClause .= " AND pb.date_paiement >= ?";
                $params[] = $date_debut;
            }
            if (!empty($date_fin)) {
                $whereClause .= " AND pb.date_paiement <= ?";
                $params[] = $date_fin;
            }

            $paiementsValides = $db->fetchAll(
                "SELECT pb.*, c.nom as conducteur_nom, c.prenom as conducteur_prenom, c.telephone as conducteur_telephone, c.numero_permis
                 FROM paiements_brevets pb
                 INNER JOIN conducteurs c ON pb.conducteur_id = c.id
                 WHERE $whereClause
                 ORDER BY pb.date_paiement DESC
                 LIMIT 100",
                $params
            );
        } catch (\Exception $e) {
            $conducteurs = [];
            $paiementsValides = [];
        }

        $this->render('admin/paiement', [
            'pageTitle' => 'Enregistrement Paiement Brevet',
            'currentPage' => '/admin/paiement',
            'conducteurs' => $conducteurs,
            'paiementsValides' => $paiementsValides,
            'search' => $search,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
        ], 'admin');
    }

    public function validerPaiement()
    {
        $db = Database::getInstance();
        $conducteur_id = $_POST['conducteur_id'] ?? null;
        $montant = $_POST['montant'] ?? 0;
        $reference = $_POST['reference'] ?? '';

        if (!$conducteur_id || !$montant || !$reference) {
            header('Location: ' . BASE_PATH . '/admin/paiement?error=Veuillez remplir tous les champs');
            exit;
        }

        try {
            // Vérifier si un paiement existe déjà
            $existing = $db->fetchOne("SELECT id FROM paiements_brevets WHERE conducteur_id = ?", [$conducteur_id]);
            
            if ($existing) {
                // Mettre à jour
                $db->query(
                    "UPDATE paiements_brevets SET montant = ?, reference_paiement = ?, date_paiement = CURRENT_DATE WHERE id = ?",
                    [$montant, $reference, $existing['id']]
                );
            } else {
                // Créer nouveau
                $db->query(
                    "INSERT INTO paiements_brevets (conducteur_id, montant, reference_paiement, date_paiement) VALUES (?, ?, ?, CURRENT_DATE)",
                    [$conducteur_id, $montant, $reference]
                );
            }
            
            header('Location: ' . BASE_PATH . '/admin/paiement?success=Paiement enregistré avec succès');
            exit;
        } catch (\Exception $e) {
            header('Location: ' . BASE_PATH . '/admin/paiement?error=Erreur: ' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function exportPaiementExcel()
    {
        $db = Database::getInstance();

        $date_debut = $_GET['date_debut'] ?? '';
        $date_fin   = $_GET['date_fin']   ?? '';

        $whereClause = "1=1";
        $params = [];

        if (!empty($date_debut)) {
            $whereClause .= " AND pb.date_paiement >= ?";
            $params[] = $date_debut;
        }
        if (!empty($date_fin)) {
            $whereClause .= " AND pb.date_paiement <= ?";
            $params[] = $date_fin;
        }

        try {
            $paiements = $db->fetchAll(
                "SELECT pb.id, c.prenom as conducteur_prenom, c.nom as conducteur_nom,
                        c.telephone as conducteur_telephone, c.numero_permis,
                        pb.montant, pb.reference_paiement, pb.date_paiement
                 FROM paiements_brevets pb
                 INNER JOIN conducteurs c ON pb.conducteur_id = c.id
                 WHERE $whereClause
                 ORDER BY pb.date_paiement DESC",
                $params
            );
        } catch (\Exception $e) {
            $paiements = [];
        }

        // ── Build spreadsheet ──────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Paiements Brevets');

        // ── Couleurs ───────────────────────────────────────────────────────
        $colorHeader   = '1A2744'; // bleu foncé
        $colorSubtitle = '3B82F6'; // bleu vif
        $colorRowAlt   = 'EFF6FF'; // bleu très clair
        $colorTotal    = 'ECFDF5'; // vert clair
        $colorTotalTxt = '065F46'; // vert foncé

        // ── Titre principal (ligne 1) ──────────────────────────────────────
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'REGISTRE DES PAIEMENTS DE BREVET');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $colorHeader]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // ── Sous-titre / période (ligne 2) ────────────────────────────────
        $periode = 'Exporté le ' . date('d/m/Y à H:i');
        if ($date_debut || $date_fin) {
            $periode .= '  |  Période : '
                . ($date_debut ? date('d/m/Y', strtotime($date_debut)) : '…')
                . ' → '
                . ($date_fin   ? date('d/m/Y', strtotime($date_fin))   : '…');
        }
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', $periode);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $colorSubtitle]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // ── Ligne vide (ligne 3) ───────────────────────────────────────────
        $sheet->getRowDimension(3)->setRowHeight(6);

        // ── En-têtes de colonnes (ligne 4) — 7 colonnes ──────────────────
        // A=ID  B=Conducteur  C=Téléphone  D=N°Permis  E=Montant  F=Référence  G=Date
        $headers = ['#', 'Conducteur', 'Téléphone', 'N° Permis', 'Montant (USD)', 'Référence', 'Date paiement'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $col++;
        }
        $sheet->getStyle('A4:G4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $colorHeader]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF93C5FD']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(28);

        // ── Données ────────────────────────────────────────────────────────
        $row   = 5;
        $total = 0;
        foreach ($paiements as $p) {
            $montant = floatval($p['montant'] ?? 0);
            $total  += $montant;

            $isAlt   = ($row % 2 === 0);
            $bgColor = $isAlt ? $colorRowAlt : 'FFFFFF';

            $conducteur = trim(($p['conducteur_prenom'] ?? '') . ' ' . ($p['conducteur_nom'] ?? ''));
            $telephone  = $p['conducteur_telephone'] ?? '';

            $sheet->setCellValue('A' . $row, $p['id']);
            $sheet->setCellValue('B' . $row, $conducteur);
            // Téléphone en texte pour éviter la conversion numérique
            $sheet->getCell('C' . $row)->setValueExplicit($telephone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $p['numero_permis'] ?? '');
            $sheet->setCellValue('E' . $row, $montant);
            $sheet->setCellValue('F' . $row, $p['reference_paiement'] ?? '');

            // Date comme valeur Excel native
            if (!empty($p['date_paiement'])) {
                $sheet->setCellValue('G' . $row, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($p['date_paiement'])));
                $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            }

            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $bgColor]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['size' => 10],
            ]);

            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // ── Ligne Total ────────────────────────────────────────────────────
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('E' . $row, $total);

        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF' . $colorTotalTxt]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $colorTotal]],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF059669']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($row)->setRowHeight(26);

        // ── Récapitulatif (2 lignes après le total) ────────────────────────
        $recapRow = $row + 2;
        $sheet->mergeCells('A' . $recapRow . ':C' . $recapRow);
        $sheet->setCellValue('A' . $recapRow, 'Nombre de paiements :');
        $sheet->setCellValue('D' . $recapRow, count($paiements));
        $sheet->getStyle('A' . $recapRow . ':D' . $recapRow)->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A' . $recapRow)->getFont()->setBold(true);

        // ── Largeurs de colonnes ───────────────────────────────────────────
        $widths = ['A' => 8, 'B' => 28, 'C' => 16, 'D' => 16, 'E' => 16, 'F' => 24, 'G' => 16];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // ── Figer la ligne d'en-têtes ──────────────────────────────────────
        $sheet->freezePane('A5');

        // ── Filtre automatique ─────────────────────────────────────────────
        $sheet->setAutoFilter('A4:G4');

        // ── Orientation paysage + marges ───────────────────────────────────
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageMargins()->setTop(0.75)->setBottom(0.75)->setLeft(0.7)->setRight(0.7);

        // ── Nom du fichier ─────────────────────────────────────────────────
        $filename = 'paiements_brevets';
        if ($date_debut || $date_fin) {
            $filename .= '_' . ($date_debut ?: 'debut') . '_au_' . ($date_fin ?: 'fin');
        }
        $filename .= '.xlsx';

        // ── Envoi ──────────────────────────────────────────────────────────
        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function parkings()
    {
        $db = Database::getInstance();

        try {
            $parkings = $db->fetchAll(
                "SELECT p.*, u.nom as responsable_nom, u.prenom as responsable_prenom
                 FROM parkings p
                 LEFT JOIN utilisateurs u ON p.responsable_id = u.id
                 ORDER BY p.date_creation DESC"
            );
        } catch (\Exception $e) {
            $parkings = [];
        }

        $this->render('admin/parkings', [
            'pageTitle' => 'Parkings',
            'currentPage' => '/admin/parkings',
            'parkings' => $parkings,
        ], 'admin');
    }

    public function conducteurForm($id = null)
    {
        $db = Database::getInstance();
        $id = $id ?? $_GET['id'] ?? null;
        $conducteur = null;
        
        if ($id) {
            try {
                $conducteur = $db->fetchOne("SELECT * FROM conducteurs WHERE id = ?", [$id]);
            } catch (\Exception $e) {
                $conducteur = null;
            }
        }
        
        $this->render('admin/conducteur-form', [
            'pageTitle' => $id ? 'Modifier Conducteur' : 'Nouveau Conducteur',
            'currentPage' => '/admin/conducteurs',
            'conducteur' => $conducteur,
        ], 'admin');
    }

    public function saveConducteur()
    {
        $db = Database::getInstance();
        $id = $_POST['id'] ?? null;
        
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $date_naissance = $_POST['date_naissance'] ?? null;
        $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $numero_permis = trim($_POST['numero_permis'] ?? '');
        $categorie_permis = $_POST['categorie_permis'] ?? 'B';
        $date_expiration_permis = $_POST['date_expiration_permis'] ?? null;
        $association = trim($_POST['association'] ?? '');
        $syndicat = trim($_POST['syndicat'] ?? '');
        $statut = $_POST['statut'] ?? 'actif';
        
        // Handle file uploads
        $photo_url = null;
        $photo_piece_identite = null;
        
        // Get existing photos if updating
        if ($id) {
            $existing = $db->fetchOne("SELECT photo_url, photo_piece_identite FROM conducteurs WHERE id = ?", [$id]);
            if ($existing) {
                $photo_url = $existing['photo_url'];
                $photo_piece_identite = $existing['photo_piece_identite'];
            }
        }
        
        // Handle new photo upload
        $newPhotoUrl = $this->handleFileUpload('photo_url');
        if ($newPhotoUrl) {
            $photo_url = $newPhotoUrl;
        }
        
        $newPhotoPiece = $this->handleFileUpload('photo_piece_identite');
        if ($newPhotoPiece) {
            $photo_piece_identite = $newPhotoPiece;
        }
        
        // Validation
        $errors = [];
        if (empty($nom)) $errors[] = 'Le nom est obligatoire';
        if (empty($prenom)) $errors[] = 'Le prénom est obligatoire';
        if (empty($date_naissance)) $errors[] = 'La date de naissance est obligatoire';

        
        // Vérifier si le numéro de permis existe déjà (pour un autre conducteur)
        if (!empty($numero_permis)) {
            $existing = $db->fetchOne("SELECT id FROM conducteurs WHERE numero_permis = ? AND id != ?", [$numero_permis, $id ?? 0]);
            if ($existing) {
                $errors[] = 'Ce numéro de permis existe déjà';
            }
        } else {
            $numero_permis = null;
        }
        
        if (!empty($errors)) {
            $this->render('admin/conducteur-form', [
                'pageTitle' => $id ? 'Modifier Conducteur' : 'Nouveau Conducteur',
                'currentPage' => '/admin/conducteurs',
                'conducteur' => $_POST,
                'error' => implode('<br>', $errors),
            ], 'admin');
            return;
        }
        
        try {
            if ($id) {
                // Mise à jour
                $db->query(
                    "UPDATE conducteurs SET nom=?, prenom=?, date_naissance=?, lieu_naissance=?, adresse=?, telephone=?, numero_permis=?, categorie_permis=?, date_expiration_permis=?, photo_url=?, photo_piece_identite=?, association=?, syndicat=?, statut=? WHERE id=?",
                    [$nom, $prenom, $date_naissance, $lieu_naissance, $adresse, $telephone, $numero_permis, $categorie_permis, $date_expiration_permis, $photo_url, $photo_piece_identite, $association, $syndicat, $statut, $id]
                );
                $message = 'Conducteur mis à jour avec succès!';
            } else {
                // Création
                $db->query(
                    "INSERT INTO conducteurs (nom, prenom, date_naissance, lieu_naissance, adresse, telephone, numero_permis, categorie_permis, date_expiration_permis, photo_url, photo_piece_identite, association, syndicat, date_enregistrement, date_expiration, statut)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE, CURRENT_DATE + INTERVAL '1 year', 'actif')",
                    [$nom, $prenom, $date_naissance, $lieu_naissance, $adresse, $telephone, $numero_permis, $categorie_permis, $date_expiration_permis, $photo_url, $photo_piece_identite, $association, $syndicat]
                );
                $message = 'Conducteur créé avec succès!';

                // Envoi SMS de confirmation au conducteur
                if (!empty($telephone)) {
                    $phoneNational = SmsService::normaliserTelephone($telephone);
                    $smsText = "Boyeyi bolamu na recyclage ndeko conducteur  {$nom} {$prenom}. merci na bofuti, brevet nayo okozwa na suka ya formation. Matondo mingi";
                    if (!SmsService::envoyer($phoneNational, $smsText)) {
                        $message .= ' (SMS non envoyé)';
                    }
                }
            }
            
            header('Location: ' . BASE_PATH . '/admin/conducteurs?success=' . urlencode($message));
            exit;
        } catch (\Exception $e) {
            $this->render('admin/conducteur-form', [
                'pageTitle' => $id ? 'Modifier Conducteur' : 'Nouveau Conducteur',
                'currentPage' => '/admin/conducteurs',
                'conducteur' => $_POST,
                'error' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage(),
            ], 'admin');
        }
    }

    public function statistiques()
    {
        $db = Database::getInstance();

        try {
            $totalConducteurs = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs")['total'] ?? 0;
            $totalVehicules = $db->fetchOne("SELECT COUNT(*) as total FROM vehicules")['total'] ?? 0;
            $totalCartes = $db->fetchOne("SELECT COUNT(*) as total FROM cartes_professionnelles")['total'] ?? 0;
            $totalPaiements = $db->fetchOne("SELECT COALESCE(SUM(montant), 0) as total FROM paiements WHERE statut = 'valide'")['total'] ?? 0;
            $totalUtilisateurs = $db->fetchOne("SELECT COUNT(*) as total FROM utilisateurs")['total'] ?? 0;
            $totalParkings = $db->fetchOne("SELECT COUNT(*) as total FROM parkings WHERE est_actif = 1")['total'] ?? 0;

            $brevetsNouveau = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'nouveau'")['total'] ?? 0;
            $brevetsEnCours = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'en_cours_impression'")['total'] ?? 0;
            $brevetsImprimes = $db->fetchOne("SELECT COUNT(*) as total FROM conducteurs WHERE statut_brevet = 'imprime'")['total'] ?? 0;

            $conducteursByStatus = $db->fetchAll(
                "SELECT statut, COUNT(*) as total FROM conducteurs GROUP BY statut"
            );
            $brevetsByStatus = $db->fetchAll(
                "SELECT statut_brevet, COUNT(*) as total FROM conducteurs GROUP BY statut_brevet"
            );
            $vehiculesByType = $db->fetchAll(
                "SELECT type_vehicule, COUNT(*) as total FROM vehicules GROUP BY type_vehicule"
            );
            $paiementsByMonth = $db->fetchAll(
                "SELECT TO_CHAR(date_paiement, 'YYYY-MM') as mois, SUM(montant) as total 
                 FROM paiements WHERE statut = 'valide' 
                 GROUP BY mois ORDER BY mois DESC LIMIT 12"
            );
        } catch (\Exception $e) {
            $totalConducteurs = 0;
            $totalVehicules = 0;
            $totalCartes = 0;
            $totalPaiements = 0;
            $totalUtilisateurs = 0;
            $totalParkings = 0;
            $brevetsNouveau = 0;
            $brevetsEnCours = 0;
            $brevetsImprimes = 0;
            $conducteursByStatus = [];
            $brevetsByStatus = [];
            $vehiculesByType = [];
            $paiementsByMonth = [];
        }

        $this->render('admin/statistiques', [
            'pageTitle' => 'Statistiques',
            'currentPage' => '/admin/statistiques',
            'totalConducteurs' => $totalConducteurs,
            'totalVehicules' => $totalVehicules,
            'totalCartes' => $totalCartes,
            'totalPaiements' => $totalPaiements,
            'totalUtilisateurs' => $totalUtilisateurs,
            'totalParkings' => $totalParkings,
            'brevetsNouveau' => $brevetsNouveau,
            'brevetsEnCours' => $brevetsEnCours,
            'brevetsImprimes' => $brevetsImprimes,
            'conducteursByStatus' => $conducteursByStatus,
            'brevetsByStatus' => $brevetsByStatus,
            'vehiculesByType' => $vehiculesByType,
            'paiementsByMonth' => $paiementsByMonth,
        ], 'admin');
    }

    public function apiConducteurs()
    {
        $db = Database::getInstance();
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $conducteur = $db->fetchOne("SELECT * FROM conducteurs WHERE id = ?", [$id]);
                    $this->json($conducteur ?: ['error' => 'Non trouvé'], $conducteur ? 200 : 404);
                } else {
                    $conducteurs = $db->fetchAll("SELECT * FROM conducteurs ORDER BY date_creation DESC");
                    $this->json($conducteurs);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                
                if (isset($data['_method']) && $data['_method'] === 'DELETE') {
                    $id = $data['id'] ?? $_GET['id'] ?? null;
                    if (!$id) {
                        $this->json(['error' => 'ID requis'], 400);
                        return;
                    }
                    try {
                        $db->query("DELETE FROM conducteurs WHERE id = ?", [$id]);
                        $this->json(['success' => true]);
                    } catch (\Exception $e) {
                        $this->json(['error' => 'Erreur lors de la suppression.'], 500);
                    }
                    break;
                }
                
                try {
                    $numeroPermis = $data['numero_permis'] ?? '';
                    if ($numeroPermis === '') {
                        $numeroPermis = null;
                    }
                    $db->query(
                        "INSERT INTO conducteurs (nom, prenom, date_naissance, lieu_naissance, adresse, telephone, numero_permis, categorie_permis, date_expiration_permis, association, syndicats, date_enregistrement, date_expiration, statut)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE, CURRENT_DATE + INTERVAL '1 year', 'actif')",
                        [
                            $data['nom'] ?? '', $data['prenom'] ?? '', $data['date_naissance'] ?? null,
                            $data['lieu_naissance'] ?? '', $data['adresse'] ?? '', $data['telephone'] ?? '',
                            $numeroPermis, $data['categorie_permis'] ?? 'B',
                            $data['date_expiration_permis'] ?? null, $data['association'] ?? '',
                            $data['syndicats'] ?? '',
                        ]
                    );
                    $this->json(['success' => true, 'id' => $db->lastInsertId()], 201);
                } catch (\Exception $e) {
                    $this->json(['error' => "Erreur lors de la création."], 500);
                }
                break;

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? $_GET['id'] ?? null;
                if (!$id) {
                    $this->json(['error' => 'ID requis'], 400);
                    return;
                }
                try {
                    $db->query(
                        "UPDATE conducteurs SET nom=?, prenom=?, date_naissance=?, lieu_naissance=?, adresse=?, telephone=?, numero_permis=?, categorie_permis=?, date_expiration_permis=?, association=?, syndicat=?, statut=? WHERE id=?",
                        [
                            $data['nom'] ?? '', $data['prenom'] ?? '', $data['date_naissance'] ?? null,
                            $data['lieu_naissance'] ?? '', $data['adresse'] ?? '', $data['telephone'] ?? '',
                            $data['numero_permis'] ?? '', $data['categorie_permis'] ?? 'B',
                            $data['date_expiration_permis'] ?? null, $data['association'] ?? '',
                            $data['syndicat'] ?? '', $data['statut'] ?? 'actif', $id,
                        ]
                    );
                    $this->json(['success' => true]);
                } catch (\Exception $e) {
                    $this->json(['error' => 'Erreur lors de la mise à jour.'], 500);
                }
                break;

            case 'DELETE':
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    $this->json(['error' => 'ID requis'], 400);
                    return;
                }
                try {
                    $db->query("DELETE FROM conducteurs WHERE id = ?", [$id]);
                    $this->json(['success' => true]);
                } catch (\Exception $e) {
                    $this->json(['error' => 'Erreur lors de la suppression.'], 500);
                }
                break;

            default:
                $this->json(['error' => 'Méthode non autorisée'], 405);
        }
    }

    public function apiUtilisateurs()
    {
        $db = Database::getInstance();
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $user = $db->fetchOne("SELECT id, nom, prenom, email, telephone, role, statut, date_creation, derniere_connexion FROM utilisateurs WHERE id = ?", [$id]);
                    $this->json($user ?: ['error' => 'Non trouvé'], $user ? 200 : 404);
                } else {
                    $utilisateurs = $db->fetchAll("SELECT id, nom, prenom, email, telephone, role, statut, date_creation, derniere_connexion FROM utilisateurs ORDER BY date_creation DESC");
                    $this->json($utilisateurs);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $nom = trim($data['nom'] ?? '');
                $prenom = trim($data['prenom'] ?? '');
                $email = trim($data['email'] ?? '');
                $telephone = trim($data['telephone'] ?? '');
                $role = $data['role'] ?? 'citoyen';
                $password = $data['mot_de_passe'] ?? '';

                if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                    $this->json(['error' => 'Nom, prénom, email et mot de passe sont requis'], 400);
                    return;
                }

                $existing = $db->fetchOne("SELECT id FROM utilisateurs WHERE email = ?", [$email]);
                if ($existing) {
                    $this->json(['error' => 'Un utilisateur avec cet email existe déjà'], 400);
                    return;
                }

                try {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $db->query(
                        "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut) VALUES (?, ?, ?, ?, ?, ?, 'actif')",
                        [$nom, $prenom, $email, $telephone, $hashedPassword, $role]
                    );
                    $this->json(['success' => true, 'id' => $db->lastInsertId()], 201);
                } catch (\Exception $e) {
                    $this->json(['error' => 'Erreur lors de la création'], 500);
                }
                break;

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? $_GET['id'] ?? null;
                if (!$id) {
                    $this->json(['error' => 'ID requis'], 400);
                    return;
                }

                $nom = trim($data['nom'] ?? '');
                $prenom = trim($data['prenom'] ?? '');
                $email = trim($data['email'] ?? '');
                $telephone = trim($data['telephone'] ?? '');
                $role = $data['role'] ?? 'citoyen';
                $statut = $data['statut'] ?? 'actif';

                if (empty($nom) || empty($prenom) || empty($email)) {
                    $this->json(['error' => 'Nom, prénom et email sont requis'], 400);
                    return;
                }

                $existing = $db->fetchOne("SELECT id FROM utilisateurs WHERE email = ? AND id != ?", [$email, $id]);
                if ($existing) {
                    $this->json(['error' => 'Un autre utilisateur avec cet email existe déjà'], 400);
                    return;
                }

                try {
                    $params = [$nom, $prenom, $email, $telephone, $role, $statut];
                    $sql = "UPDATE utilisateurs SET nom=?, prenom=?, email=?, telephone=?, role=?, statut=?";

                    if (!empty($data['mot_de_passe'])) {
                        $sql .= ", mot_de_passe=?";
                        $params[] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
                    }

                    $sql .= " WHERE id=?";
                    $params[] = $id;

                    $db->query($sql, $params);
                    $this->json(['success' => true]);
                } catch (\Exception $e) {
                    $this->json(['error' => 'Erreur lors de la mise à jour'], 500);
                }
                break;

            case 'DELETE':
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    $this->json(['error' => 'ID requis'], 400);
                    return;
                }
                $currentUser = Auth::user();
                if ($currentUser && $currentUser['id'] == $id) {
                    $this->json(['error' => 'Vous ne pouvez pas supprimer votre propre compte'], 400);
                    return;
                }
                try {
                    $db->query("DELETE FROM utilisateurs WHERE id = ?", [$id]);
                    $this->json(['success' => true]);
                } catch (\Exception $e) {
                    $this->json(['error' => 'Erreur lors de la suppression'], 500);
                }
                break;

            default:
                $this->json(['error' => 'Méthode non autorisée'], 405);
        }
    }

    public function apiVehicules()
    {
        $db = Database::getInstance();
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $vehicule = $db->fetchOne("SELECT * FROM vehicules WHERE id = ?", [$id]);
                    $this->json($vehicule ?: ['error' => 'Non trouvé'], $vehicule ? 200 : 404);
                } else {
                    $vehicules = $db->fetchAll("SELECT * FROM vehicules ORDER BY date_creation DESC");
                    $this->json($vehicules);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                try {
                    $db->query(
                        "INSERT INTO vehicules (numero_plaque, type_vehicule, marque, modele, annee, capacite, proprietaire_nom, societe_transport, date_immatriculation, date_expiration, statut)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE, CURRENT_DATE + INTERVAL '1 year', 'actif')",
                        [
                            $data['numero_plaque'] ?? '', $data['type_vehicule'] ?? 'taxi',
                            $data['marque'] ?? '', $data['modele'] ?? '',
                            $data['annee'] ?? null, $data['capacite'] ?? null,
                            $data['proprietaire_nom'] ?? '', $data['societe_transport'] ?? '',
                        ]
                    );
                    $this->json(['success' => true, 'id' => $db->lastInsertId()], 201);
                } catch (\Exception $e) {
                    $this->json(['error' => "Erreur lors de la création."], 500);
                }
                break;

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? $_GET['id'] ?? null;
                if (!$id) {
                    $this->json(['error' => 'ID requis'], 400);
                    return;
                }
                try {
                    $db->query(
                        "UPDATE vehicules SET numero_plaque=?, type_vehicule=?, marque=?, modele=?, annee=?, capacite=?, proprietaire_nom=?, societe_transport=?, statut=? WHERE id=?",
                        [
                            $data['numero_plaque'] ?? '', $data['type_vehicule'] ?? 'taxi',
                            $data['marque'] ?? '', $data['modele'] ?? '',
                            $data['annee'] ?? null, $data['capacite'] ?? null,
                            $data['proprietaire_nom'] ?? '', $data['societe_transport'] ?? '',
                            $data['statut'] ?? 'actif', $id,
                        ]
                    );
                    $this->json(['success' => true]);
                } catch (\Exception $e) {
                    $this->json(['error' => 'Erreur lors de la mise à jour.'], 500);
                }
                break;

            case 'DELETE':
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    $this->json(['error' => 'ID requis'], 400);
                    return;
                }
                try {
                    $db->query("DELETE FROM vehicules WHERE id = ?", [$id]);
                    $this->json(['success' => true]);
                } catch (\Exception $e) {
                    $this->json(['error' => 'Erreur lors de la suppression.'], 500);
                }
                break;

            default:
                $this->json(['error' => 'Méthode non autorisée'], 405);
        }
    }
}
