CREATE TABLE IF NOT EXISTS notification_settings (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER UNIQUE NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    enable_rights_reminders BOOLEAN DEFAULT TRUE,
    rights_reminder_days INTEGER DEFAULT 7, -- Notification envoyée X jours avant l'échéance des 30 jours
    enable_treatment_review_reminders BOOLEAN DEFAULT TRUE,
    treatment_review_interval_years INTEGER DEFAULT 1,
    enable_aipd_draft_reminders BOOLEAN DEFAULT TRUE,
    aipd_draft_reminder_days INTEGER DEFAULT 15, -- Rappel si en brouillon depuis X jours
    notification_emails TEXT, -- Emails séparés par des virgules, si vide on utilise l'admin de l'organisation
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notifications (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    type VARCHAR(50) NOT NULL, -- 'rights_exercise', 'treatment_review', 'aipd_draft'
    entity_type VARCHAR(50) NOT NULL,
    entity_id INTEGER NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recipient_email VARCHAR(255) NOT NULL
);

-- Index pour éviter les doublons de notifications
CREATE INDEX idx_notifications_lookup ON notifications(entity_type, entity_id, type);

-- Initialisation des paramètres par défaut pour les organisations existantes
INSERT INTO notification_settings (organization_id)
SELECT id FROM organizations
ON CONFLICT (organization_id) DO NOTHING;
