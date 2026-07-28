-- ---------------------------------------------------------------
-- schema.sql — the one table this project needs.
--
-- Run this once in phpMyAdmin (InfinityFree gives you phpMyAdmin
-- from the Client Area) using the SQL tab.
-- ---------------------------------------------------------------

CREATE TABLE IF NOT EXISTS people (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(100)     NOT NULL,
    age    INT              NOT NULL,
    status TINYINT(1)       NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Optional: a few rows to look at while testing.
-- INSERT INTO people (name, age, status) VALUES
--     ('John',    25, 0),
--     ('Sarah',   30, 1),
--     ('Michael', 22, 0);
