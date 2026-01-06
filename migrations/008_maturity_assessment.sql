CREATE TABLE IF NOT EXISTS maturity_assessments (
    id SERIAL PRIMARY KEY,
    organization_id INT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    governance_score DECIMAL(3,2) NOT NULL DEFAULT 0,
    registry_score DECIMAL(3,2) NOT NULL DEFAULT 0,
    rights_score DECIMAL(3,2) NOT NULL DEFAULT 0,
    security_score DECIMAL(3,2) NOT NULL DEFAULT 0,
    risk_score DECIMAL(3,2) NOT NULL DEFAULT 0,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_maturity_org ON maturity_assessments(organization_id);
