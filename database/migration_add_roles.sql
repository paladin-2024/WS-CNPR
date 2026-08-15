-- Migration: Ajouter les rôles manquants (imprimeur, receptionnaire) à l'ENUM
ALTER TABLE utilisateurs 
MODIFY COLUMN role ENUM('admin', 'minister_admin', 'agent', 'inspecteur', 'gestionnaire_parking', 'transporteur', 'conducteur', 'citoyen', 'operateur_saisie', 'validateur', 'receveur', 'instructeur', 'imprimeur', 'receptionnaire') DEFAULT 'citoyen';
