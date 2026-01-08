CREATE DATABASE IF NOT EXISTS db_kartini;
USE db_kartini;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(100),
    level ENUM('admin','siswa')
);

CREATE TABLE siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    kelas VARCHAR(50)
);

CREATE TABLE spp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tahun VARCHAR(20),
    nominal INT
);

CREATE TABLE pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT,
    bulan VARCHAR(20),
    bukti VARCHAR(255),
    status ENUM('pending','lunas') DEFAULT 'pending',
    FOREIGN KEY (id_siswa) REFERENCES siswa(id)
);

INSERT INTO users (username, password, level) VALUES
('admin', 'admin123', 'admin'),
('siswa1', 'siswa123', 'siswa');
