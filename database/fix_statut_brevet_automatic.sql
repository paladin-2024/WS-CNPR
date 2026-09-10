-- Per client direction: the manual nouveau -> en_cours_impression ->
-- imprime staff workflow (see BrevetController/admin/imprimeur.php) wasn't
-- actually being run in practice, so every conducteur stayed stuck at
-- 'nouveau' and every public verification (verification/show.php's
-- $isAuthentique check requires statut_brevet = 'imprime') came back
-- "not authentic" regardless of whether the driver's record was otherwise
-- valid. AdminController.php's conducteur-creation INSERTs now set
-- statut_brevet = 'imprime' directly instead of relying on this column's
-- default, and the schema.sql default (fresh installs only) was updated
-- to match - this migration does the same for the already-existing
-- production database and its already-existing rows.

ALTER TABLE conducteurs ALTER COLUMN statut_brevet SET DEFAULT 'imprime';

UPDATE conducteurs
SET statut_brevet = 'imprime'
WHERE statut_brevet IN ('nouveau', 'en_cours_impression');
