CREATE TABLE IF NOT EXISTS dtias (
    id SERIAL PRIMARY KEY,
    organization_id INT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    treatment_id INT REFERENCES treatments(id) ON DELETE SET NULL,
    subprocessor_id INT REFERENCES subprocessors(id) ON DELETE SET NULL,
    country_name VARCHAR(100) NOT NULL,
    transfer_mechanism VARCHAR(50) NOT NULL, -- 'adequacy', 'scc', 'bcr', 'derogation'
    data_exporter VARCHAR(255) NOT NULL,
    data_importer VARCHAR(255) NOT NULL,
    data_categories TEXT NOT NULL,
    risk_level VARCHAR(20) NOT NULL, -- 'low', 'medium', 'high', 'critical'
    supplementary_measures TEXT,
    assessment_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'draft', -- 'draft', 'completed', 'reviewed'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_dtia_org ON dtias(organization_id);
CREATE INDEX idx_dtia_treatment ON dtias(treatment_id);
