-- Tiga database terpisah: admin, dosen, mahasiswa (referensi lintas-DB memakai id, tanpa FK)
CREATE DATABASE IF NOT EXISTS lms_admin CHARACTER SET utf8mb4;
CREATE DATABASE IF NOT EXISTS lms_dosen CHARACTER SET utf8mb4;
CREATE DATABASE IF NOT EXISTS lms_mahasiswa CHARACTER SET utf8mb4;

-- ===== DB ADMIN =====
USE lms_admin;
CREATE TABLE admin(id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(40) UNIQUE, nama VARCHAR(100), email VARCHAR(100) UNIQUE, telp VARCHAR(20), password VARCHAR(255));
CREATE TABLE fakultas(id INT AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100));
CREATE TABLE prodi(id INT AUTO_INCREMENT PRIMARY KEY, fakultas_id INT, nama VARCHAR(100), FOREIGN KEY(fakultas_id) REFERENCES fakultas(id));
CREATE TABLE kelas(id INT AUTO_INCREMENT PRIMARY KEY, kode VARCHAR(20), nama VARCHAR(120), dosen_id INT, tahun_ajaran VARCHAR(30), UNIQUE(kode,tahun_ajaran));
CREATE TABLE kelas_mhs(kelas_id INT, mhs_id INT, PRIMARY KEY(kelas_id,mhs_id), FOREIGN KEY(kelas_id) REFERENCES kelas(id) ON DELETE CASCADE);
INSERT INTO fakultas(nama) VALUES('Fakultas Teknologi Industri'),('Fakultas Bisnis dan Ekonomika');
INSERT INTO prodi(fakultas_id,nama) VALUES(1,'Informatika'),(1,'Teknik Industri'),(2,'Manajemen');
-- login awal: admin / password  (segera ganti!)
INSERT INTO admin(username,nama,email,password) VALUES('admin','Admin LMS','admin@lms.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ===== DB DOSEN =====
USE lms_dosen;
CREATE TABLE dosen(id INT AUTO_INCREMENT PRIMARY KEY, npp VARCHAR(30) UNIQUE, nama VARCHAR(100), email VARCHAR(100) UNIQUE, telp VARCHAR(20), fakultas_id INT, prodi_id INT, password VARCHAR(255));
CREATE TABLE materi(id INT AUTO_INCREMENT PRIMARY KEY, kelas_id INT, dosen_id INT, judul VARCHAR(150), isi TEXT, dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE tugas(id INT AUTO_INCREMENT PRIMARY KEY, kelas_id INT, dosen_id INT, judul VARCHAR(150), deskripsi TEXT, deadline DATETIME);
CREATE TABLE perpanjangan(id INT AUTO_INCREMENT PRIMARY KEY, tugas_id INT, mhs_id INT, deadline_baru DATETIME, alasan VARCHAR(255), dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(tugas_id) REFERENCES tugas(id) ON DELETE CASCADE);
CREATE TABLE nilai(id INT AUTO_INCREMENT PRIMARY KEY, tugas_id INT, mhs_id INT, nilai DECIMAL(5,2), catatan VARCHAR(255), UNIQUE(tugas_id,mhs_id), FOREIGN KEY(tugas_id) REFERENCES tugas(id) ON DELETE CASCADE);

-- ===== DB MAHASISWA =====
USE lms_mahasiswa;
CREATE TABLE mahasiswa(id INT AUTO_INCREMENT PRIMARY KEY, npm VARCHAR(20) UNIQUE, nama VARCHAR(100), email VARCHAR(100) UNIQUE, telp VARCHAR(20), fakultas_id INT, prodi_id INT, gender VARCHAR(10), umur TINYINT, password VARCHAR(255));
CREATE TABLE pengumpulan(id INT AUTO_INCREMENT PRIMARY KEY, tugas_id INT, mhs_id INT, file VARCHAR(255), catatan VARCHAR(255), waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE(tugas_id,mhs_id));
-- Produksi: buat user MySQL berbeda per peran (GRANT hanya pada DB masing-masing + SELECT lintas-DB seperlunya).
