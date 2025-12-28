DROP TABLE IF EXISTS treatments;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE treatments (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    purpose TEXT NOT NULL,
    legal_basis VARCHAR(100) NOT NULL,
    data_categories TEXT NOT NULL,
    retention_period VARCHAR(100) NOT NULL,
    has_sensitive_data BOOLEAN DEFAULT FALSE,
    is_large_scale BOOLEAN DEFAULT FALSE,
    retention_years INTEGER DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subprocessors (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    service VARCHAR(255) NOT NULL,
    location VARCHAR(100) NOT NULL,
    guarantees TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE treatment_subprocessors (
    treatment_id INTEGER REFERENCES treatments(id) ON DELETE CASCADE,
    subprocessor_id INTEGER REFERENCES subprocessors(id) ON DELETE CASCADE,
    PRIMARY KEY (treatment_id, subprocessor_id)
);

