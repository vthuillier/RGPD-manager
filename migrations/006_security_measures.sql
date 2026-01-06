-- Create a library of Technical and Organizational Measures (TOMs)
CREATE TABLE IF NOT EXISTS security_measures (
    id SERIAL PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    weight INTEGER DEFAULT 1 -- Used for the security score calculation
);

-- Associate treatments with security measures
CREATE TABLE IF NOT EXISTS treatment_security_measures (
    treatment_id INTEGER REFERENCES treatments(id) ON DELETE CASCADE,
    measure_id INTEGER REFERENCES security_measures(id) ON DELETE CASCADE,
    PRIMARY KEY (treatment_id, measure_id)
);

-- Pre-populate with standard TOMs
INSERT INTO security_measures (category, name, weight, description) VALUES
('Contrôle d''accès', 'Authentification forte (MFA)', 3, 'Utilisation du second facteur pour l''accès au système.'),
('Contrôle d''accès', 'Gestion stricte des habilitations', 2, 'Principe du moindre privilège appliqué.'),
('Confidentialité', 'Chiffrement des données au repos', 3, 'AES-256 ou équivalent sur les bases de données et serveurs.'),
('Confidentialité', 'Chiffrement TLS pour les flux', 3, 'HTTPS/TLS pour tous les échanges de données.'),
('Disponibilité', 'Sauvegardes régulières', 2, 'Backups quotidiens avec test de restauration.'),
('Disponibilité', 'Redondance des infrastructures', 2, 'Architecture haute disponibilité.'),
('Traçabilité', 'Journalisation des accès', 1, 'Logs de connexion et d''actions critiques.'),
('Organisationnel', 'Politique de Mot de Passe', 1, 'Complexité minimale et renouvellement.'),
('Organisationnel', 'Formation des utilisateurs', 2, 'Sensibilisation régulière à la cybersécurité.'),
('Organisationnel', 'Contrats de sous-traitance RGPD', 2, 'DPA signés avec tous les prestataires.'),
('Technique', 'Antivirus et Firewall', 2, 'Protection périmétrique et des terminaux.'),
('Technique', 'Mises à jour de sécurité (Patching)', 3, 'Processus de déploiement rapide des correctifs.');
