CREATE TABLE my_table (
    id      SERIAL PRIMARY KEY,
    name    VARCHAR(255),
    created_at      TIMESTAMP DEFAULT now()
);