<?php
require 'config.php';
if (empty($_SESSION['u'])) {
    go('index.php');
}
require 'includes/layout.php';

$u = $_SESSION['u'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    chk();
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telp  = trim($_POST['hp'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($nama === '' || $email === '') {
        flash('Nama dan Email wajib diisi.');
        go('profile.php');
    }

    if ($u['role'] === 'mahasiswa') {
        $tabel = 'lms_mahasiswa.mahasiswa';
    } elseif ($u['role'] === 'dosen') {
        $tabel = 'lms_dosen.dosen';
    } else {
        $tabel = 'lms_admin.admin';
    }

    if ($pass !== '') {
        q("UPDATE {$tabel} SET nama=?, email=?, telp=?, password=? WHERE id=?",
          [$nama, $email, $telp, password_hash($pass, PASSWORD_BCRYPT), $u['id']]);
    } else {
        q("UPDATE {$tabel} SET nama=?, email=?, telp=? WHERE id=?",
          [$nama, $email, $telp, $u['id']]);
    }

    $_SESSION['u']['nama']  = $nama;
    $_SESSION['u']['email'] = $email;
    $_SESSION['u']['telp']  = $telp;

    flash('Perubahan profil berhasil disimpan.');
    go('profile.php');
}

top('Profile', 'profile.php', true, ['profile.css']);
?>

<div class="box">
    <form method="post" action="profile.php">
        <?php echo csrf(); ?>

        <div class="pr-head">
            <span class="av"><?php echo e(ini($u['nama'])); ?></span>
            <div>
                <h2><?php echo e($u['nama']); ?></h2>
                <p class="mut"><?php echo e(ucfirst($u['role'])); ?></p>
            </div>
        </div>

        <div class="prow">
            <label for="f_nama">Nama Anda
                <input type="text" id="f_nama" name="nama" value="<?php echo e($u['nama']); ?>" readonly="readonly" />
            </label>
            <span class="pill" onclick="ed('f_nama')">Edit</span>
        </div>

        <div class="prow">
            <label for="f_email">Email
                <input type="text" id="f_email" name="email" value="<?php echo e($u['email']); ?>" readonly="readonly" />
            </label>
            <span class="pill" onclick="ed('f_email')">Edit</span>
        </div>

        <div class="prow">
            <label for="f_hp">Phone Number
                <input type="text" id="f_hp" name="hp" value="<?php echo e($u['telp'] ?? ''); ?>" readonly="readonly" />
            </label>
            <span class="pill" onclick="ed('f_hp')">Edit</span>
        </div>

        <div class="prow">
            <label for="f_pass">Password baru
                <input type="password" id="f_pass" name="password" value="" placeholder="**********" readonly="readonly" />
            </label>
            <span class="pill" onclick="ed('f_pass')">Edit</span>
        </div>

        <div style="margin-top: 14px;">
            <button type="submit" id="sv" class="blue" style="display: none;">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php
bottom();
?>