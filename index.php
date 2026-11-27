<?php
require 'config.php';
if (!empty($_SESSION['u'])) {
    go('dashboard.php');
}
require 'includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    chk();
    $role     = $_POST['role'] ?? '';
    $login_id = trim($_POST['login_id'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = null;
    if ($role === 'mahasiswa') {
        $user = q("SELECT * FROM lms_mahasiswa.mahasiswa WHERE npm=? OR email=?", [$login_id, $login_id])->fetch();
    } elseif ($role === 'dosen') {
        $user = q("SELECT * FROM lms_dosen.dosen WHERE npp=? OR email=?", [$login_id, $login_id])->fetch();
    } elseif ($role === 'admin') {
        $user = q("SELECT * FROM lms_admin.admin WHERE username=? OR email=?", [$login_id, $login_id])->fetch();
    }

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
        unset($user['password']);
        $user['role'] = $role;
        $_SESSION['u'] = $user;
        go('dashboard.php');
    }

    flash('Role, ID/Email, atau Password tidak sesuai.');
    go('index.php');
}

auth_top('Masuk LMS');
$pesan = flash();
?>

<div class="auth-logo">
    <img src="<?php echo e(BASE . '/images/UAJY-LOGOGRAM_-01.png'); ?>" alt="Logo UAJY" />
</div>

<form method="post" action="">
    <?php echo csrf(); ?>

    <label for="role">Masuk sebagai
        <select id="role" name="role">
            <option value="mahasiswa" selected="selected">Mahasiswa</option>
            <option value="dosen">Dosen</option>
            <option value="admin">Admin</option>
        </select>
    </label>

    <label for="login_id">NPM / NPP / Username / Email
        <input type="text" id="login_id" name="login_id" value="" />
    </label>

    <label for="password">Password
        <input type="password" id="password" name="password" value="" />
    </label>

    <?php if ($pesan) { ?>
        <div class="flash bad"><?php echo e($pesan); ?></div>
    <?php } ?>

    <button type="submit">Login</button>
</form>

<p><a href="register.php">Registrasi mahasiswa baru</a></p>

<?php
bottom();
?>