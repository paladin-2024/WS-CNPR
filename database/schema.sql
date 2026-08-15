-- =====================================================
-- Base de données: min_transport (PostgreSQL)
-- Portail Numérique du Ministère des Transports
--
-- Schéma consolidé pour PostgreSQL. Remplace le schéma MySQL historique et
-- incorpore les migrations qui existaient auparavant en fichiers séparés
-- (migration_config.sql, migration_brevets.sql, migration_add_roles.sql —
-- conservés à titre d'historique mais superseded, voir leur en-tête).
--
-- Notes de portage MySQL -> PostgreSQL :
--   - AUTO_INCREMENT       -> SERIAL
--   - ENUM(...)            -> VARCHAR + CHECK (...)
--   - DATETIME             -> TIMESTAMP
--   - ON UPDATE CURRENT_TIMESTAMP -> trigger set_date_modification()
--   - BOOLEAN (tinyint)    -> SMALLINT 0/1 (le code PHP existant compare/insère
--                              des entiers 0/1, pas des booléens Postgres natifs)
--   - INDEX inline         -> CREATE INDEX séparé
--   - ENGINE/CHARSET       -> supprimé (non applicable)
-- =====================================================

-- =====================================================
-- Fonction utilitaire pour les colonnes date_modification
-- =====================================================
CREATE OR REPLACE FUNCTION set_date_modification()
RETURNS TRIGGER AS $$
BEGIN
    NEW.date_modification = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- =====================================================
-- TABLE: utilisateurs
-- =====================================================
CREATE TABLE IF NOT EXISTS utilisateurs (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(20) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(30) DEFAULT 'citoyen' CHECK (role IN (
        'admin', 'minister_admin', 'agent', 'inspecteur', 'gestionnaire_parking',
        'transporteur', 'conducteur', 'citoyen', 'operateur_saisie', 'validateur',
        'receveur', 'instructeur', 'imprimeur', 'receptionnaire'
    )),
    statut VARCHAR(20) DEFAULT 'actif' CHECK (statut IN ('actif', 'inactif', 'suspendu')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL
);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_email ON utilisateurs (email);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_role ON utilisateurs (role);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_statut ON utilisateurs (statut);
DROP TRIGGER IF EXISTS trg_utilisateurs_date_modification ON utilisateurs;
CREATE TRIGGER trg_utilisateurs_date_modification
    BEFORE UPDATE ON utilisateurs
    FOR EACH ROW EXECUTE FUNCTION set_date_modification();

-- =====================================================
-- TABLE: conducteurs
-- =====================================================
CREATE TABLE IF NOT EXISTS conducteurs (
    id SERIAL PRIMARY KEY,
    utilisateur_id INT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    lieu_naissance VARCHAR(255),
    adresse VARCHAR(255),
    telephone VARCHAR(20),
    numero_permis VARCHAR(50) NULL UNIQUE,
    categorie_permis VARCHAR(1) NOT NULL CHECK (categorie_permis IN ('A', 'B', 'C', 'D', 'E')),
    date_expiration_permis DATE,
    photo_url VARCHAR(255),
    photo_piece_identite VARCHAR(255),
    association VARCHAR(100),
    syndicat VARCHAR(100),
    date_enregistrement DATE NOT NULL,
    date_expiration DATE,
    statut VARCHAR(20) DEFAULT 'actif' CHECK (statut IN ('actif', 'suspendu', 'expire')),
    statut_brevet VARCHAR(30) NOT NULL DEFAULT 'nouveau' CHECK (statut_brevet IN ('nouveau', 'en_cours_impression', 'imprime')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS idx_conducteurs_permis ON conducteurs (numero_permis);
CREATE INDEX IF NOT EXISTS idx_conducteurs_statut ON conducteurs (statut);
CREATE INDEX IF NOT EXISTS idx_conducteurs_statut_brevet ON conducteurs (statut_brevet);
CREATE INDEX IF NOT EXISTS idx_conducteurs_date_enregistrement ON conducteurs (date_enregistrement);
DROP TRIGGER IF EXISTS trg_conducteurs_date_modification ON conducteurs;
CREATE TRIGGER trg_conducteurs_date_modification
    BEFORE UPDATE ON conducteurs
    FOR EACH ROW EXECUTE FUNCTION set_date_modification();

-- =====================================================
-- TABLE: vehicules
-- =====================================================
CREATE TABLE IF NOT EXISTS vehicules (
    id SERIAL PRIMARY KEY,
    numero_plaque VARCHAR(20) NOT NULL UNIQUE,
    type_vehicule VARCHAR(20) NOT NULL CHECK (type_vehicule IN ('taxi', 'bus', 'camion', 'moto', 'voiture')),
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100),
    annee INT,
    capacite INT,
    proprietaire_nom VARCHAR(100),
    proprietaire_id INT NULL,
    societe_transport VARCHAR(100),
    date_immatriculation DATE,
    date_expiration DATE,
    statut VARCHAR(20) DEFAULT 'actif' CHECK (statut IN ('actif', 'suspendu', 'radie')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proprietaire_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS idx_vehicules_plaque ON vehicules (numero_plaque);
CREATE INDEX IF NOT EXISTS idx_vehicules_type ON vehicules (type_vehicule);
CREATE INDEX IF NOT EXISTS idx_vehicules_statut ON vehicules (statut);
DROP TRIGGER IF EXISTS trg_vehicules_date_modification ON vehicules;
CREATE TRIGGER trg_vehicules_date_modification
    BEFORE UPDATE ON vehicules
    FOR EACH ROW EXECUTE FUNCTION set_date_modification();

-- =====================================================
-- TABLE: association_conducteur_vehicule
-- =====================================================
CREATE TABLE IF NOT EXISTS association_conducteur_vehicule (
    id SERIAL PRIMARY KEY,
    conducteur_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    est_principal SMALLINT NOT NULL DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE,
    CONSTRAINT unique_association UNIQUE (conducteur_id, vehicule_id, date_debut)
);

-- =====================================================
-- TABLE: cartes_professionnelles
-- =====================================================
CREATE TABLE IF NOT EXISTS cartes_professionnelles (
    id SERIAL PRIMARY KEY,
    conducteur_id INT NOT NULL,
    numero_carte VARCHAR(50) NOT NULL UNIQUE,
    qr_code VARCHAR(255),
    fichier_pdf VARCHAR(255),
    date_emission DATE NOT NULL,
    date_expiration DATE NOT NULL,
    statut VARCHAR(20) DEFAULT 'active' CHECK (statut IN ('active', 'expiree', 'suspendue')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_cartes_numero ON cartes_professionnelles (numero_carte);
CREATE INDEX IF NOT EXISTS idx_cartes_statut ON cartes_professionnelles (statut);

-- =====================================================
-- TABLE: documents
-- =====================================================
CREATE TABLE IF NOT EXISTS documents (
    id SERIAL PRIMARY KEY,
    conducteur_id INT NOT NULL,
    type_document VARCHAR(30) NOT NULL CHECK (type_document IN ('permis', 'certificat_medical', 'attestation', 'carte_pro', 'autre')),
    fichier_url VARCHAR(255) NOT NULL,
    numero_document VARCHAR(100),
    date_obtention DATE,
    date_expiration DATE,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'en_attente' CHECK (statut IN ('valide', 'expire', 'en_attente')),
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_documents_conducteur ON documents (conducteur_id);
CREATE INDEX IF NOT EXISTS idx_documents_type ON documents (type_document);

-- =====================================================
-- TABLE: taxes
-- =====================================================
CREATE TABLE IF NOT EXISTS taxes (
    id SERIAL PRIMARY KEY,
    type_taxe VARCHAR(100) NOT NULL,
    description TEXT,
    montant DECIMAL(12, 2) NOT NULL,
    periodicite VARCHAR(20) DEFAULT 'annuel' CHECK (periodicite IN ('journalier', 'mensuel', 'annuel')),
    categorie_vehicule VARCHAR(50),
    est_actif SMALLINT NOT NULL DEFAULT 1,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_taxes_type ON taxes (type_taxe);
DROP TRIGGER IF EXISTS trg_taxes_date_modification ON taxes;
CREATE TRIGGER trg_taxes_date_modification
    BEFORE UPDATE ON taxes
    FOR EACH ROW EXECUTE FUNCTION set_date_modification();

-- =====================================================
-- TABLE: paiements_brevets (paiement des brevets)
-- =====================================================
CREATE TABLE IF NOT EXISTS paiements_brevets (
    id SERIAL PRIMARY KEY,
    conducteur_id INT NOT NULL,
    montant DECIMAL(12, 2) NOT NULL,
    reference_paiement VARCHAR(100) NOT NULL,
    date_paiement DATE NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_paiements_brevets_conducteur ON paiements_brevets (conducteur_id);
CREATE INDEX IF NOT EXISTS idx_paiements_brevets_date ON paiements_brevets (date_paiement);

-- =====================================================
-- TABLE: paiements
-- =====================================================
CREATE TABLE IF NOT EXISTS paiements (
    id SERIAL PRIMARY KEY,
    conducteur_id INT,
    vehicule_id INT,
    taxe_id INT NOT NULL,
    numero_transaction VARCHAR(50) NOT NULL UNIQUE,
    montant DECIMAL(12, 2) NOT NULL,
    mode_paiement VARCHAR(20) NOT NULL CHECK (mode_paiement IN ('guichet', 'mobile_money', 'banque')),
    reference_paiement VARCHAR(100),
    recu_pdf VARCHAR(255),
    qr_code_verification VARCHAR(255),
    date_paiement TIMESTAMP NOT NULL,
    statut VARCHAR(20) DEFAULT 'en_attente' CHECK (statut IN ('en_attente', 'valide', 'rejete', 'rembourse')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE SET NULL,
    FOREIGN KEY (taxe_id) REFERENCES taxes(id)
);
CREATE INDEX IF NOT EXISTS idx_paiements_transaction ON paiements (numero_transaction);
CREATE INDEX IF NOT EXISTS idx_paiements_statut ON paiements (statut);
CREATE INDEX IF NOT EXISTS idx_paiements_date ON paiements (date_paiement);

-- =====================================================
-- TABLE: inspections
-- =====================================================
CREATE TABLE IF NOT EXISTS inspections (
    id SERIAL PRIMARY KEY,
    inspecteur_id INT NOT NULL,
    conducteur_id INT,
    vehicule_id INT,
    type_inspection VARCHAR(20) DEFAULT 'routine' CHECK (type_inspection IN ('routine', 'controle', 'accident')),
    lieu_inspection VARCHAR(255),
    observations TEXT,
    infractions_constatees TEXT,
    rapport TEXT,
    date_inspection TIMESTAMP NOT NULL,
    statut VARCHAR(20) DEFAULT 'en_cours' CHECK (statut IN ('en_cours', 'termine', 'annule')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inspecteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS idx_inspections_inspecteur ON inspections (inspecteur_id);
CREATE INDEX IF NOT EXISTS idx_inspections_date ON inspections (date_inspection);

-- =====================================================
-- TABLE: sanctions
-- =====================================================
CREATE TABLE IF NOT EXISTS sanctions (
    id SERIAL PRIMARY KEY,
    conducteur_id INT NOT NULL,
    inspecteur_id INT NOT NULL,
    inspection_id INT,
    type_infraction VARCHAR(100) NOT NULL,
    description TEXT,
    amende DECIMAL(12, 2),
    points_suspension INT,
    date_sanction DATE NOT NULL,
    date_expiration DATE,
    statut VARCHAR(20) DEFAULT 'active' CHECK (statut IN ('active', 'payee', 'expiree')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES conducteurs(id),
    FOREIGN KEY (inspecteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (inspection_id) REFERENCES inspections(id)
);
CREATE INDEX IF NOT EXISTS idx_sanctions_conducteur ON sanctions (conducteur_id);
CREATE INDEX IF NOT EXISTS idx_sanctions_statut ON sanctions (statut);

-- =====================================================
-- TABLE: parkings
-- =====================================================
CREATE TABLE IF NOT EXISTS parkings (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    localisation VARCHAR(255) NOT NULL,
    capacite INT NOT NULL,
    type_parking VARCHAR(20) DEFAULT 'exterieur' CHECK (type_parking IN ('couvert', 'exterieur', 'semi_couvert')),
    responsable_id INT,
    telephone VARCHAR(20),
    description TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    est_actif SMALLINT NOT NULL DEFAULT 1,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id)
);
CREATE INDEX IF NOT EXISTS idx_parkings_nom ON parkings (nom);
CREATE INDEX IF NOT EXISTS idx_parkings_localisation ON parkings (localisation);
DROP TRIGGER IF EXISTS trg_parkings_date_modification ON parkings;
CREATE TRIGGER trg_parkings_date_modification
    BEFORE UPDATE ON parkings
    FOR EACH ROW EXECUTE FUNCTION set_date_modification();

-- =====================================================
-- TABLE: stationnement
-- =====================================================
CREATE TABLE IF NOT EXISTS stationnement (
    id SERIAL PRIMARY KEY,
    parking_id INT NOT NULL,
    vehicule_id INT,
    numero_place VARCHAR(20),
    heure_entree TIMESTAMP NOT NULL,
    heure_sortie TIMESTAMP,
    montant DECIMAL(10, 2),
    statut VARCHAR(20) DEFAULT 'en_cours' CHECK (statut IN ('en_cours', 'termine')),
    FOREIGN KEY (parking_id) REFERENCES parkings(id),
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id)
);
CREATE INDEX IF NOT EXISTS idx_stationnement_parking ON stationnement (parking_id);
CREATE INDEX IF NOT EXISTS idx_stationnement_date ON stationnement (heure_entree);

-- =====================================================
-- TABLE: infrastructures
-- =====================================================
CREATE TABLE IF NOT EXISTS infrastructures (
    id SERIAL PRIMARY KEY,
    type_infrastructure VARCHAR(20) NOT NULL CHECK (type_infrastructure IN ('route', 'pont', 'tunnel', 'bau', 'autre')),
    nom VARCHAR(100) NOT NULL,
    localisation VARCHAR(255) NOT NULL,
    description TEXT,
    etat VARCHAR(20) DEFAULT 'bon' CHECK (etat IN ('bon', 'moyen', 'mauvais', 'critique')),
    date_derniere_inspection DATE,
    travaux_en_cours SMALLINT NOT NULL DEFAULT 0,
    description_travaux TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_infrastructures_type ON infrastructures (type_infrastructure);
CREATE INDEX IF NOT EXISTS idx_infrastructures_etat ON infrastructures (etat);
DROP TRIGGER IF EXISTS trg_infrastructures_date_modification ON infrastructures;
CREATE TRIGGER trg_infrastructures_date_modification
    BEFORE UPDATE ON infrastructures
    FOR EACH ROW EXECUTE FUNCTION set_date_modification();

-- =====================================================
-- TABLE: signalements
-- =====================================================
CREATE TABLE IF NOT EXISTS signalements (
    id SERIAL PRIMARY KEY,
    citoyen_id INT NOT NULL,
    type_probleme VARCHAR(30) NOT NULL CHECK (type_probleme IN ('route_endommagee', 'embouteillage', 'pont_dangerux', 'accident', 'autre')),
    description TEXT NOT NULL,
    localisation VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    photo_url VARCHAR(255),
    statut VARCHAR(20) DEFAULT 'nouveau' CHECK (statut IN ('nouveau', 'en_cours', 'traite', 'rejete')),
    date_traitement TIMESTAMP,
    observations_traitement TEXT,
    date_signalement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (citoyen_id) REFERENCES utilisateurs(id)
);
CREATE INDEX IF NOT EXISTS idx_signalements_statut ON signalements (statut);
CREATE INDEX IF NOT EXISTS idx_signalements_type ON signalements (type_probleme);
DROP TRIGGER IF EXISTS trg_signalements_date_modification ON signalements;
CREATE TRIGGER trg_signalements_date_modification
    BEFORE UPDATE ON signalements
    FOR EACH ROW EXECUTE FUNCTION set_date_modification();

-- =====================================================
-- TABLE: articles (CMS)
-- =====================================================
CREATE TABLE IF NOT EXISTS articles (
    id SERIAL PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    resume VARCHAR(500),
    categorie VARCHAR(30) DEFAULT 'actualite' CHECK (categorie IN ('actualite', 'communique', 'appel_offre', 'texte_reglementaire', 'evenement')),
    image_url VARCHAR(255),
    auteur_id INT NOT NULL,
    est_publie SMALLINT NOT NULL DEFAULT 0,
    date_publication TIMESTAMP,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id)
);
CREATE INDEX IF NOT EXISTS idx_articles_categorie ON articles (categorie);
CREATE INDEX IF NOT EXISTS idx_articles_publie ON articles (est_publie);
DROP TRIGGER IF EXISTS trg_articles_date_modification ON articles;
CREATE TRIGGER trg_articles_date_modification
    BEFORE UPDATE ON articles
    FOR EACH ROW EXECUTE FUNCTION set_date_modification();

-- =====================================================
-- TABLE: journal_activites
-- =====================================================
CREATE TABLE IF NOT EXISTS journal_activites (
    id SERIAL PRIMARY KEY,
    utilisateur_id INT,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS idx_journal_utilisateur ON journal_activites (utilisateur_id);
CREATE INDEX IF NOT EXISTS idx_journal_date ON journal_activites (date_action);

-- =====================================================
-- TABLE: contact_messages
-- Utilisée par ContactController::send() — absente du schéma MySQL d'origine.
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sujet VARCHAR(255),
    message TEXT NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_contact_messages_email ON contact_messages (email);

-- =====================================================
-- TABLE: configuration (clé/valeur, ex migration_config.sql)
-- =====================================================
CREATE TABLE IF NOT EXISTS configuration (
    id SERIAL PRIMARY KEY,
    cle VARCHAR(100) NOT NULL UNIQUE,
    valeur TEXT,
    description VARCHAR(255),
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_configuration_cle ON configuration (cle);

INSERT INTO configuration (cle, valeur, description) VALUES ('app_name', 'Ministère des Transports', 'Nom de l''application') ON CONFLICT (cle) DO NOTHING;
INSERT INTO configuration (cle, valeur, description) VALUES ('app_logo', '', 'Logo de l''application (chemin vers l''image)') ON CONFLICT (cle) DO NOTHING;
INSERT INTO configuration (cle, valeur, description) VALUES ('app_slogan', 'Portail Numérique', 'Slogan de l''application') ON CONFLICT (cle) DO NOTHING;
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_logo_gauche', '', 'Logo gauche sur la carte brevet') ON CONFLICT (cle) DO NOTHING;
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_logo_droite', '', 'Logo droite sur la carte brevet') ON CONFLICT (cle) DO NOTHING;
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_signature', '', 'Signature électronique de l''autorité sur la carte') ON CONFLICT (cle) DO NOTHING;
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_titre_gauche', 'République Démocratique du Congo', 'Titre à gauche sur la carte') ON CONFLICT (cle) DO NOTHING;
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_titre_droite', 'Direction Provinciale de la CNPR', 'Titre à droite sur la carte') ON CONFLICT (cle) DO NOTHING;

-- =====================================================
-- DONNÉES DE TEST: Utilisateurs
-- Mots de passe : admin123 (hashés avec bcrypt, portables entre SGBD)
-- =====================================================
INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role, statut) VALUES
('Admin', 'Système', 'admin@mintransport.gov', '+243800000000', '$2y$10$gREmKAPVpa0q2ktCsAho1uVP9pbhn.ZTy9V9d7b6awccnQx/6GL0S', 'admin', 'actif'),
('Kabongo', 'Marie', 'marie@mintransport.gov', '+243800000001', '$2y$10$gREmKAPVpa0q2ktCsAho1uVP9pbhn.ZTy9V9d7b6awccnQx/6GL0S', 'agent', 'actif'),
('Mwamba', 'Pierre', 'pierre@mintransport.gov', '+243800000002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'inspecteur', 'actif'),
('Kasongo', 'Jean', 'jean@email.com', '+243800000003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'citoyen', 'actif')
ON CONFLICT (email) DO NOTHING;

-- =====================================================
-- DONNÉES DE TEST: Taxes
-- =====================================================
INSERT INTO taxes (type_taxe, description, montant, periodicite) VALUES
('Taxe de circulation', 'Taxe annuelle pour tous les véhicules', 50000, 'annuel'),
('Taxe exploitation', 'Licence pour entreprise de transport', 100000, 'annuel'),
('Taxe journalière taxi', 'Taxe journalière pour taxi', 500, 'journalier'),
('Taxe moto', 'Taxe annuelle pour motos', 15000, 'annuel'),
('Taxe stationnement', 'Taxe de parking', 1000, 'journalier')
ON CONFLICT DO NOTHING;

-- =====================================================
-- DONNÉES DE TEST: Parkings
-- =====================================================
INSERT INTO parkings (nom, localisation, capacite, type_parking, telephone) VALUES
('Parking Central', 'Avenue du Plateau', 150, 'couvert', '+243800001000'),
('Parking Marché', 'Quartier Commerce', 80, 'exterieur', '+243800001001'),
('Terminal Bus', 'Zone Industrielle', 50, 'couvert', '+243800001002'),
('Parking Stade', 'Avenue des Sports', 200, 'exterieur', '+243800001003')
ON CONFLICT DO NOTHING;
