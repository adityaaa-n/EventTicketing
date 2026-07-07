-- Create ENUM for ticket status
CREATE TYPE ticket_status AS ENUM ('pending', 'paid', 'confirmed', 'rejected', 'cancelled');

-- Table: admins
CREATE TABLE admins (
  admin_id SERIAL PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert data for admins
INSERT INTO admins (admin_id, nama, email, password, created_at) VALUES
(1, 'Super Admin', 'admin@eventtix.com', 'admin123', '2025-12-26 10:40:40');
-- Fix sequence
SELECT setval('admins_admin_id_seq', COALESCE((SELECT MAX(admin_id) FROM admins), 1));

-- Table: events
CREATE TABLE events (
  event_id SERIAL PRIMARY KEY,
  nama_event VARCHAR(150) NOT NULL,
  deskripsi TEXT,
  kategori VARCHAR(50),
  lokasi VARCHAR(150),
  tanggal DATE NOT NULL,
  waktu TIME NOT NULL,
  harga NUMERIC(10,2) NOT NULL,
  kuota INTEGER NOT NULL,
  gambar VARCHAR(255) DEFAULT 'default.jpg',
  created_by INTEGER REFERENCES admins(admin_id) ON DELETE SET NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert data for events
INSERT INTO events (event_id, nama_event, deskripsi, kategori, lokasi, tanggal, waktu, harga, kuota, gambar, created_by, created_at) VALUES
(1, 'Liga Champions Final', 'Saksikan pertandingan final Liga Champions secara langsung di stadion. Atmosfer yang luar biasa dengan ribuan suporter.', NULL, 'National Stadium, Surabaya', '2025-12-25', '20:00:00', 500000.00, 93, 'banner.jpg', NULL, '2025-12-26 10:40:46'),
(2, 'Jazz Festival 2025', 'Nikmati malam penuh alunan musik jazz dari musisi ternama tanah air dan internasional. Pengalaman musik yang tak terlupakan.', NULL, 'Jakarta Convention Center', '2026-01-10', '19:00:00', 750000.00, 245, 'banner.jpg', NULL, '2025-12-26 10:40:46');
-- Fix sequence
SELECT setval('events_event_id_seq', COALESCE((SELECT MAX(event_id) FROM events), 1));

-- Table: users
CREATE TABLE users (
  user_id SERIAL PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert data for users
INSERT INTO users (user_id, nama, email, password, created_at) VALUES
(3, 'maswir', 'wirwir@gmail.com', 'user123', '2026-01-04 23:24:11');
-- Fix sequence
SELECT setval('users_user_id_seq', COALESCE((SELECT MAX(user_id) FROM users), 1));

-- Table: tickets
CREATE TABLE tickets (
  ticket_id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
  event_id INTEGER NOT NULL REFERENCES events(event_id) ON DELETE CASCADE,
  jumlah INTEGER NOT NULL,
  total_harga NUMERIC(10,2) NOT NULL,
  status ticket_status DEFAULT 'pending',
  tanggal_beli TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert data for tickets
INSERT INTO tickets (ticket_id, user_id, event_id, jumlah, total_harga, status, tanggal_beli) VALUES
(17, 3, 2, 1, 755000.00, 'rejected', '2026-01-04 23:25:15'),
(18, 3, 2, 1, 755000.00, 'confirmed', '2026-01-05 05:23:35');
-- Fix sequence
SELECT setval('tickets_ticket_id_seq', COALESCE((SELECT MAX(ticket_id) FROM tickets), 1));

-- Table: payment_logs
CREATE TABLE payment_logs (
  payment_id SERIAL PRIMARY KEY,
  ticket_id INTEGER NOT NULL REFERENCES tickets(ticket_id) ON DELETE CASCADE,
  jumlah_bayar NUMERIC(10,2) NOT NULL,
  bukti_pembayaran VARCHAR(255),
  metode VARCHAR(50) NOT NULL,
  nominal NUMERIC(10,2) NOT NULL,
  waktu_bayar TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  bukti_transfer VARCHAR(255)
);

-- Insert data for payment_logs
INSERT INTO payment_logs (payment_id, ticket_id, jumlah_bayar, bukti_pembayaran, metode, nominal, waktu_bayar, bukti_transfer) VALUES
(14, 17, 755000.00, '695af6e3d5bf3.png', '', 0.00, '2026-01-04 23:25:23', NULL),
(15, 18, 755000.00, '695b4adfaea15.png', '', 0.00, '2026-01-05 05:23:43', NULL);
-- Fix sequence
SELECT setval('payment_logs_payment_id_seq', COALESCE((SELECT MAX(payment_id) FROM payment_logs), 1));
