ALTER TABLE notification_settings 
ADD COLUMN IF NOT EXISTS smtp_host VARCHAR(255),
ADD COLUMN IF NOT EXISTS smtp_port INTEGER DEFAULT 587,
ADD COLUMN IF NOT EXISTS smtp_user VARCHAR(255),
ADD COLUMN IF NOT EXISTS smtp_pass VARCHAR(255),
ADD COLUMN IF NOT EXISTS smtp_encryption VARCHAR(10) DEFAULT 'tls',
ADD COLUMN IF NOT EXISTS from_email VARCHAR(255),
ADD COLUMN IF NOT EXISTS from_name VARCHAR(255);

-- Ajout d'une colonne pour marquer la configuration système (id -1 ou flag spécifique)
-- On va utiliser l'organization_id -1 pour le "Système" comme pour l'organisation par défaut si besoin,
-- ou s'assurer que l'organisation -1 a sa propre config.

INSERT INTO notification_settings (organization_id, from_email, from_name)
VALUES (-1, 'system@rgpd-manager.local', 'RGPD Manager System')
ON CONFLICT (organization_id) DO NOTHING;
