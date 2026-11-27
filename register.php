<?php
require 'config.php';
require 'includes/layout.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    chk();
    $nama  = trim($_POST['nama'] ?? '');
    $npm   = trim($_POST['npm'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telp  = trim($_POST['telp'] ?? '');
    $fak   = (int) ($_POST['fak'] ?? 0);
    $prodi = (int) ($_POST['prodi'] ?? 0);
    $pw    = $_POST['pw'] ?? '';

    if ($nama === '' || !preg_match('/^\d{6,12}$/', $npm) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pw) < 8) {
        $error = 'Data tidak valid (NPM berupa angka, email benar, password minimal 8 karakter).';
    } else {
        try {
            q("INSERT INTO lms_mahasiswa.mahasiswa (npm, nama, email, telp, fakultas_id, prodi_id, password)
               VALUES (?, ?, ?, ?, ?, ?, ?)",
              [$npm, $nama, $email, $telp, $fak, $prodi, password_hash($pw, PASSWORD_BCRYPT)]);
            flash('Registrasi berhasil, silakan login.');
            go('index.php');
        } catch (PDOException $ex) {
            $error = 'NPM atau email sudah terdaftar.';
        }
    }
}

$fakultas = q("SELECT * FROM lms_admin.fakultas")->fetchAll();
$prodi    = q("SELECT * FROM lms_admin.prodi")->fetchAll();

auth_top('Registrasi');
?>
<form method="post" action="register.php">
    <h1>Registrasi Mahasiswa</h1>
    <?php
    if ($error !== '') {
        echo '<div class="flash bad">' . e($error) . '</div>';
    }
    echo csrf();
    ?>

    <label>Nama <input type="text" name="nama" required="required" /></label>
    <label>NPM <input type="text" name="npm" required="required" /></label>
    <label>Email <input type="text" name="email" required="required" /></label>
    <label>No. telepon <input type="text" name="telp" /></label>
    <label>Fakultas
        <select name="fak">
            <?php foreach ($fakultas as $f) { ?>
                <option value="<?php echo $f['id']; ?>"><?php echo e($f['nama']); ?></option>
            <?php } ?>
        </select>
    </label>
    <label>Program studi
        <select name="prodi">
            <?php foreach ($prodi as $p) { ?>
                <option value="<?php echo $p['id']; ?>"><?php echo e($p['nama']); ?></option>
            <?php } ?>
        </select>
    </label>
    <label>Password <input type="password" name="pw" required="required" /></label>

    <button type="submit">Daftar</button>
    <p><a href="index.php">Kembali ke login</a></p>
</form>
<?php
bottom();
?>