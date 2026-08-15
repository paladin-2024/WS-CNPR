-- =====================================================
-- Base de données: min_transport
-- Portail Numérique du Ministère des Transports
-- =====================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS min_transport CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE min_transport;

-- =====================================================
-- TABLE: utilisateurs
-- =====================================================
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(20) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'minister_admin', 'agent', 'inspecteur', 'gestionnaire_parking', 'transporteur', 'conducteur', 'citoyen', 'operateur_saisie', 'validateur', 'receveur', 'instructeur', 'imprimeur', 'receptionnaire') DEFAULT 'citoyen',
    statut ENUM('actif', 'inactif', 'suspendu') DEFAULT 'actif',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    derniere_connexion DATETIME NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: conducteurs
-- =====================================================
CREATE TABLE IF NOT EXISTS conducteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    lieu_naissance VARCHAR(255),
    adresse VARCHAR(255),
    telephone VARCHAR(20),
    numero_permis VARCHAR(50) NULL UNIQUE,
    categorie_permis ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
    date_expiration_permis DATE,
    photo_url VARCHAR(255),
    photo_piece_identite VARCHAR(255),
    association VARCHAR(100),
    syndicat VARCHAR(100),
    date_enregistrement DATE NOT NULL,
    date_expiration DATE,
    statut ENUM('actif', 'suspendu', 'expire') DEFAULT 'actif',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_permis (numero_permis),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: vehicules
-- =====================================================
CREATE TABLE IF NOT EXISTS vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_plaque VARCHAR(20) NOT NULL UNIQUE,
    type_vehicule ENUM('taxi', 'bus', 'camion', 'moto', 'voiture') NOT NULL,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100),
    annee INT,
    capacite INT,
    proprietaire_nom VARCHAR(100),
    proprietaire_id INT NULL,
    societe_transport VARCHAR(100),
    date_immatriculation DATE,
    date_expiration DATE,
    statut ENUM('actif', 'suspendu', 'radie') DEFAULT 'actif',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proprietaire_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_plaque (numero_plaque),
    INDEX idx_type (type_vehicule),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: association_conducteur_vehicule
-- =====================================================
CREATE TABLE IF NOT EXISTS association_conducteur_vehicule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    est_principal BOOLEAN DEFAULT FALSE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE,
    UNIQUE KEY unique_association (conducteur_id, vehicule_id, date_debut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: cartes_professionnelles
-- =====================================================
CREATE TABLE IF NOT EXISTS cartes_professionnelles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    numero_carte VARCHAR(50) NOT NULL UNIQUE,
    qr_code VARCHAR(255),
    fichier_pdf VARCHAR(255),
    date_emission DATE NOT NULL,
    date_expiration DATE NOT NULL,
    statut ENUM('active', 'expiree', 'suspendue') DEFAULT 'active',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE CASCADE,
    INDEX idx_numero (numero_carte),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: documents
-- =====================================================
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    type_document ENUM('permis', 'certificat_medical', 'attestation', 'carte_pro', 'autre') NOT NULL,
    fichier_url VARCHAR(255) NOT NULL,
    numero_document VARCHAR(100),
    date_obtention DATE,
    date_expiration DATE,
    date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('valide', 'expire', 'en_attente') DEFAULT 'en_attente',
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE CASCADE,
    INDEX idx_conducteur (conducteur_id),
    INDEX idx_type (type_document)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: taxes
-- =====================================================
CREATE TABLE IF NOT EXISTS taxes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_taxe VARCHAR(100) NOT NULL,
    description TEXT,
    montant DECIMAL(12, 2) NOT NULL,
    periodicite ENUM('journalier', 'mensuel', 'annuel') DEFAULT 'annuel',
    categorie_vehicule VARCHAR(50),
    est_actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type_taxe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: paiements_brevets ( Paiement des brevets )
-- =====================================================
CREATE TABLE IF NOT EXISTS paiements_brevets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    montant DECIMAL(12, 2) NOT NULL,
    reference_paiement VARCHAR(100) NOT NULL,
    date_paiement DATE NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE CASCADE,
    INDEX idx_conducteur (conducteur_id),
    INDEX idx_date (date_paiement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: paiements
-- =====================================================
CREATE TABLE IF NOT EXISTS paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT,
    vehicule_id INT,
    taxe_id INT NOT NULL,
    numero_transaction VARCHAR(50) NOT NULL UNIQUE,
    montant DECIMAL(12, 2) NOT NULL,
    mode_paiement ENUM('guichet', 'mobile_money', 'banque') NOT NULL,
    reference_paiement VARCHAR(100),
    recu_pdf VARCHAR(255),
    qr_code_verification VARCHAR(255),
    date_paiement DATETIME NOT NULL,
    statut ENUM('en_attente', 'valide', 'rejete', 'rembourse') DEFAULT 'en_attente',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE SET NULL,
    FOREIGN KEY (taxe_id) REFERENCES taxes(id),
    INDEX idx_transaction (numero_transaction),
    INDEX idx_statut (statut),
    INDEX idx_date (date_paiement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: inspections
-- =====================================================
CREATE TABLE IF NOT EXISTS inspections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inspecteur_id INT NOT NULL,
    conducteur_id INT,
    vehicule_id INT,
    type_inspection ENUM('routine', 'controle', 'accident') DEFAULT 'routine',
    lieu_inspection VARCHAR(255),
    observations TEXT,
    infractions_constatees TEXT,
    rapport TEXT,
    date_inspection DATETIME NOT NULL,
    statut ENUM('en_cours', 'termine', 'annule') DEFAULT 'en_cours',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inspecteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE SET NULL,
    INDEX idx_inspecteur (inspecteur_id),
    INDEX idx_date (date_inspection)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: sanctions
-- =====================================================
CREATE TABLE IF NOT EXISTS sanctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    inspecteur_id INT NOT NULL,
    inspection_id INT,
    type_infraction VARCHAR(100) NOT NULL,
    description TEXT,
    amende DECIMAL(12, 2),
    points_suspension INT,
    date_sanction DATE NOT NULL,
    date_expiration DATE,
    statut ENUM('active', 'payee', 'expiree') DEFAULT 'active',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id),
    FOREIGN KEY (inspecteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (inspection_id) REFERENCES inspections(id),
    INDEX idx_conducteur (conducteur_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: parkings
-- =====================================================
CREATE TABLE IF NOT EXISTS parkings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    localisation VARCHAR(255) NOT NULL,
    capacite INT NOT NULL,
    type_parking ENUM('couvert', 'exterieur', 'semi_couvert') DEFAULT 'exterieur',
    responsable_id INT,
    telephone VARCHAR(20),
    description TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    est_actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id),
    INDEX idx_nom (nom),
    INDEX idx_localisation (localisation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: stationnement
-- =====================================================
CREATE TABLE IF NOT EXISTS stationnement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parking_id INT NOT NULL,
    vehicule_id INT,
    numero_place VARCHAR(20),
    heure_entree DATETIME NOT NULL,
    heure_sortie DATETIME,
    montant DECIMAL(10, 2),
    statut ENUM('en_cours', 'termine') DEFAULT 'en_cours',
    FOREIGN KEY (parking_id) REFERENCES parkings(id),
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id),
    INDEX idx_parking (parking_id),
    INDEX idx_date (heure_entree)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: infrastructures
-- =====================================================
CREATE TABLE IF NOT EXISTS infrastructures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_infrastructure ENUM('route', 'pont', 'tunnel', 'bau', 'autre') NOT NULL,
    nom VARCHAR(100) NOT NULL,
    localisation VARCHAR(255) NOT NULL,
    description TEXT,
    etat ENUM('bon', 'moyen', 'mauvais', 'critique') DEFAULT 'bon',
    date_derniere_inspection DATE,
    travaux_en_cours BOOLEAN DEFAULT FALSE,
    description_travaux TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type_infrastructure),
    INDEX idx_etat (etat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: signalements
-- =====================================================
CREATE TABLE IF NOT EXISTS signalements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citoyen_id INT NOT NULL,
    type_probleme ENUM('route_endommagee', 'embouteillage', 'pont_dangerux', 'accident', 'autre') NOT NULL,
    description TEXT NOT NULL,
    localisation VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    photo_url VARCHAR(255),
    statut ENUM('nouveau', 'en_cours', 'traite', 'rejete') DEFAULT 'nouveau',
    date_traitement DATETIME,
    observations_traitement TEXT,
    date_signalement DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citoyen_id) REFERENCES utilisateurs(id),
    INDEX idx_statut (statut),
    INDEX idx_type (type_probleme)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: articles (CMS)
-- =====================================================
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    resume VARCHAR(500),
    categorie ENUM('actualite', 'communique', 'appel_offre', 'texte_reglementaire', 'evenement') DEFAULT 'actualite',
    image_url VARCHAR(255),
    auteur_id INT NOT NULL,
    est_publie BOOLEAN DEFAULT FALSE,
    date_publication DATETIME,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id),
    INDEX idx_categorie (categorie),
    INDEX idx_publie (est_publie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABLE: journal_activites
-- =====================================================
CREATE TABLE IF NOT EXISTS journal_activites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_date (date_action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DONNÉES DE TEST: Utilisateurs
-- =====================================================
-- Mots de passe : admin123 (hashés avec bcrypt)
INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut) VALUES
('Admin', 'Système', 'admin@mintransport.gov', '+243800000000', '$2y$10$gREmKAPVpa0q2ktCsAho1uVP9pbhn.ZTy9V9d7b6awccnQx/6GL0S', 'admin', 'actif'),
('Kabongo', 'Marie', 'marie@mintransport.gov', '+243800000001', '$2y$10$gREmKAPVpa0q2ktCsAho1uVP9pbhn.ZTy9V9d7b6awccnQx/6GL0S', 'agent', 'actif'),
('Mwamba', 'Pierre', 'pierre@mintransport.gov', '+243800000002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'inspecteur', 'actif'),
('Kasongo', 'Jean', 'jean@email.com', '+243800000003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'citoyen', 'actif');

-- =====================================================
-- DONNÉES DE TEST: Taxes
-- =====================================================
INSERT INTO taxes (type_taxe, description, montant, periodicite) VALUES
('Taxe de circulation', 'Taxe annuelle pour tous les véhicules', 50000, 'annuel'),
('Taxe exploitation', 'Licence pour entreprise de transport', 100000, 'annuel'),
('Taxe journalière taxi', 'Taxe journalière pour taxi', 500, 'journalier'),
('Taxe moto', 'Taxe annuelle pour motos', 15000, 'annuel'),
('Taxe stationnement', 'Taxe de parking', 1000, 'journalier');

-- =====================================================
-- DONNÉES DE TEST: Parkings
-- =====================================================
INSERT INTO parkings (nom, localisation, capacite, type_parking, telephone) VALUES
('Parking Central', 'Avenue du Plateau', 150, 'couvert', '+243800001000'),
('Parking Marché', 'Quartier Commerce', 80, 'exterieur', '+243800001001'),
('Terminal Bus', 'Zone Industrielle', 50, 'couvert', '+243800001002'),
('Parking Stade', 'Avenue des Sports', 200, 'exterieur', '+243800001003');
