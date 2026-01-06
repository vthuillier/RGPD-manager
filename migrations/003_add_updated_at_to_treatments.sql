-- Ajout de updated_at sur la table treatments pour le suivi des revues annuelles
ALTER TABLE treatments ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Initialisation de updated_at avec created_at pour les données existantes
UPDATE treatments SET updated_at = created_at WHERE updated_at IS NULL;
