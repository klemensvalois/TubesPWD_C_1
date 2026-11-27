<?php
/**
 * config.php
 * Konfigurasi aplikasi, koneksi database, dan fungsi bantu yang dipakai semua halaman.
 */

session_start();
date_default_timezone_set('Asia/Jakarta');

/* ---------- Alamat dasar aplikasi ---------- */
// Dideteksi otomatis dari lokasi folder terhadap htdocs, jadi nama folder bebas (mis. /lms).
$docRoot = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$appDir  = str_replace('\\', '/', __DIR__);
define('BASE', ($docRoot !== '' && stripos($appDir, $docRoot) === 0) ? substr($appDir, strlen($docRoot)) : '/lms');

/* ---------- Database ---------- */
// Satu koneksi ke server MySQL; tiap peran memakai database sendiri (lms_admin, lms_dosen, lms_mahasiswa)
// dan tabelnya dipanggil dengan nama lengkap, contoh: lms_dosen.tugas
function db()
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// Menjalankan query dengan prepared statement dan mengembalikan statement-nya.
function q($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/* ---------- Fungsi bantu umum ---------- */
// Escape output HTML (mencegah XSS).
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Alamat file di folder assets, ditambah versi file agar cache browser tidak menahan CSS/JS lama.
function asset($file)
{
    $path = __DIR__ . '/assets/' . $file;
    return BASE . '/assets/' . $file . '?v=' . (is_file($path) ? filemtime($path) : 0);
}

// Pindah halaman (relatif terhadap folder aplikasi) lalu hentikan script.
function go($url)
{
    header('Location: ' . BASE . '/' . $url);
    exit;
}

// Pesan sekali tampil: flash('teks') untuk menyimpan, flash() untuk mengambil.
function flash($message = null)
{
    if ($message !== null) {
        $_SESSION['flash'] = $message;
        return;
    }
    $text = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    return $text;
}

/* ---------- Hak akses ---------- */
// Pastikan pengguna sudah login dengan peran tertentu ('admin', 'dosen', 'mahasiswa').
function need($role)
{
    if (empty($_SESSION['u']) || $_SESSION['u']['role'] !== $role) {
        go('index.php');
    }
}

/* ---------- CSRF ---------- */
// Input tersembunyi berisi token; tempelkan di setiap form POST.
function csrf()
{
    if (empty($_SESSION['t'])) {
        $_SESSION['t'] = bin2hex(random_bytes(16));
    }
    return '<input type="hidden" name="t" value="' . $_SESSION['t'] . '" />';
}

// Periksa token pada setiap request POST.
function chk()
{
    if (!hash_equals($_SESSION['t'] ?? '', $_POST['t'] ?? '')) {
        http_response_code(400);
        exit('Token CSRF tidak valid. Muat ulang halaman.');
    }
}

/* ---------- Upload file ---------- */
// Menyimpan file ke folder uploads/ (maks. 5 MB, ekstensi diperiksa). Mengembalikan nama file atau null.
function upload($file)
{
    if (!isset($file) || $file['error'] !== 0 || $file['size'] > 5 * 1024 * 1024) {
        return null;
    }
    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'txt', 'png', 'jpg'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $name);
    return $name;
}

/* ---------- Deadline efektif ---------- */
// Potongan SQL: deadline milik mahasiswa = perpanjangan dari dosen (jika ada), kalau tidak deadline umum.
// Butuh satu parameter "?" berisi id mahasiswa dan alias tabel tugas "t".
const DL = "COALESCE((SELECT MAX(p.deadline_baru) FROM lms_dosen.perpanjangan p
                      WHERE p.tugas_id = t.id AND p.mhs_id = ?), t.deadline)";
?>