-- SUPERSEDED: schéma MySQL historique. La base tourne désormais sur PostgreSQL ;
-- la table configuration et ses valeurs par défaut sont déjà incluses dans
-- database/schema.sql. Conservé pour l'historique uniquement, ne pas exécuter.

-- =====================================================
-- TABLE: configuration
-- =====================================================
CREATE TABLE IF NOT EXISTS configuration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(100) NOT NULL UNIQUE,
    valeur TEXT,
    description VARCHAR(255),
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cle (cle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Configuration par défaut
INSERT INTO configuration (cle, valeur, description) VALUES ('app_name', 'Ministère des Transports', 'Nom de l''application');
INSERT INTO configuration (cle, valeur, description) VALUES ('app_logo', '', 'Logo de l''application (chemin vers l''image)');
INSERT INTO configuration (cle, valeur, description) VALUES ('app_slogan', 'Portail Numérique', 'Slogan de l''application');
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_logo_gauche', '', 'Logo gauche sur la carte brevet');
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_logo_droite', '', 'Logo droite sur la carte brevet');
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_signature', '', 'Signature électronique de l''autorité sur la carte');
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_titre_gauche', 'République Démocratique du Congo', 'Titre à gauche sur la carte');
INSERT INTO configuration (cle, valeur, description) VALUES ('carte_titre_droite', 'Direction Provinciale de la CNPR', 'Titre à droite sur la carte');
