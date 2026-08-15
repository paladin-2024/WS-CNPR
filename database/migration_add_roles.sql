-- SUPERSEDED: schéma MySQL historique. La base tourne désormais sur PostgreSQL ;
-- ce changement est déjà inclus dans database/schema.sql (CHECK constraint sur
-- utilisateurs.role). Conservé pour l'historique uniquement, ne pas exécuter.
--
-- Migration: Ajouter les rôles manquants (imprimeur, receptionnaire) à l'ENUM
ALTER TABLE utilisateurs
MODIFY COLUMN role ENUM('admin', 'minister_admin', 'agent', 'inspecteur', 'gestionnaire_parking', 'transporteur', 'conducteur', 'citoyen', 'operateur_saisie', 'validateur', 'receveur', 'instructeur', 'imprimeur', 'receptionnaire') DEFAULT 'citoyen';
