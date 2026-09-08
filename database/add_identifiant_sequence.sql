-- Makes the existing `numero_permis` column (already relabeled
-- "Identifiant" in the UI, per feature/identifiant-and-qrcode-by-identifiant)
-- system-generated in ROC-A001 format instead of manually typed by staff.
-- No column rename - just how the value gets populated going forward.
--
-- Generated via a real Postgres sequence (nextval() is atomic - no
-- SELECT-MAX-then-format race window), rolling from A001..A999 to B001
-- rather than a 4th digit. Same scheme as the sibling PST-A001 identifiant
-- e-taxe-kisangani (DGPSPT) uses for its own transport identifiant.
--
-- This is a fresh sequence starting at 1, independent of however many
-- conducteurs already exist or what values they already have in
-- numero_permis - only rows that are currently NULL get backfilled, so
-- any already-entered real value is left untouched.

CREATE SEQUENCE IF NOT EXISTS identifiant_conducteur_seq START 1;

DO $$
DECLARE
    r RECORD;
    n INTEGER;
    letter_index INTEGER;
    num INTEGER;
    code TEXT;
BEGIN
    FOR r IN SELECT id FROM conducteurs WHERE numero_permis IS NULL ORDER BY id LOOP
        n := nextval('identifiant_conducteur_seq');
        letter_index := (n - 1) / 999;
        num := ((n - 1) % 999) + 1;
        code := 'ROC-' || chr(65 + letter_index) || lpad(num::text, 3, '0');
        UPDATE conducteurs SET numero_permis = code WHERE id = r.id;
    END LOOP;
END
$$;
