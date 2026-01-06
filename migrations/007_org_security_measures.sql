-- Allow security measures to be organization-specific
ALTER TABLE security_measures ADD COLUMN IF NOT EXISTS organization_id INTEGER REFERENCES organizations(id) ON DELETE CASCADE;

CREATE INDEX IF NOT EXISTS idx_security_measures_org ON security_measures(organization_id);

-- Update existing measures to be "global" (organization_id stays NULL)
-- If we want them to be specific to an existing organization we could do that here, 
-- but NULL = Global template for all is a good model.
