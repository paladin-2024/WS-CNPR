-- SUPERSEDED: schéma MySQL historique. La base tourne désormais sur PostgreSQL ;
-- statut_brevet et les rôles imprimeur/receptionnaire sont déjà inclus dans
-- database/schema.sql. Conservé pour l'historique uniquement, ne pas exécuter.
--
-- =====================================================
-- Migration: Gestion des brevets de conducteurs
-- Date: 2026-03-23
-- Description: Ajout du statut de brevet pour les conducteurs
--              et des rôles imprimeur/réceptionnaire
-- =====================================================

USE min_transport;

-- =====================================================
-- Étape 1: Ajout de la colonne statut_brevet à la table conducteurs
-- Permet de suivre l'état d'impression du brevet de chaque conducteur
-- =====================================================
ALTER TABLE conducteurs
    ADD COLUMN statut_brevet ENUM('nouveau', 'en_cours_impression', 'imprime') NOT NULL DEFAULT 'nouveau'
    AFTER statut;

-- =====================================================
-- Étape 2: Modification du rôle dans la table utilisateurs
-- Ajout des rôles 'imprimeur' et 'receptionnaire' pour la gestion des brevets
-- =====================================================
ALTER TABLE utilisateurs
    MODIFY COLUMN role ENUM(
        'admin',
        'minister_admin',
        'agent',
        'inspecteur',
        'gestionnaire_parking',
        'transporteur',
        'conducteur',
        'citoyen',
        'imprimeur',
        'receptionnaire'
    ) DEFAULT 'citoyen';

-- =====================================================
-- Étape 3: Ajout d'un index sur la colonne statut_brevet
-- Optimise les requêtes de filtrage par statut de brevet
-- =====================================================
ALTER TABLE conducteurs
    ADD INDEX idx_statut_brevet (statut_brevet);

-- =====================================================
-- Étape 4: Ajout d'un index sur la colonne date_enregistrement
-- Optimise les requêtes de tri et filtrage par date d'enregistrement
-- =====================================================
ALTER TABLE conducteurs
    ADD INDEX idx_date_enregistrement (date_enregistrement);
