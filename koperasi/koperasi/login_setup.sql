-- ============================================================
-- login_setup.sql
-- Tabel pengguna untuk fitur login/logout CoreCoop
-- Jalankan sekali di database PostgreSQL (Neon/Railway) sebelum
-- login.php bisa dipakai.
-- ============================================================

CREATE TABLE IF NOT EXISTS pengguna (
    id_pengguna     SERIAL PRIMARY KEY,
    username        VARCHAR(50) UNIQUE NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    nama_lengkap    VARCHAR(100) NOT NULL,
    role            VARCHAR(20) NOT NULL DEFAULT 'admin', -- admin / petugas
    dibuat_pada     TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Akun default: username = admin, password = admin123
-- (password_hash di bawah adalah hasil bcrypt dari "admin123",
--  cocok dengan password_verify() di PHP)
INSERT INTO pengguna (username, password_hash, nama_lengkap, role)
VALUES (
    'admin',
    '$2b$10$vX4tLkdRPhPvY55TtJck2OFUg.krV3GrirpFvOszQsPFtjC.zxpES',
    'Administrator',
    'admin'
)
ON CONFLICT (username) DO NOTHING;
