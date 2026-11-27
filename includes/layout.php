<?php

function ini($name)
{
    $parts = preg_split('/\s+/', trim($name));
    $awal  = substr($parts[0], 0, 1);
    $kedua = isset($parts[1]) ? substr($parts[1], 0, 1) : '';
    return strtoupper($awal . $kedua);
}

function hari($datetime)
{
    $days   = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $months = [
        '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $t     = strtotime($datetime);
    $hari  = $days[(int) date('w', $t)];
    $tgl   = (int) date('j', $t);
    $bulan = $months[(int) date('n', $t)];
    $tahun = date('Y', $t);
    return $hari . ', ' . $tgl . ' ' . $bulan . ' ' . $tahun;
}

function menu($role)
{
    if ($role === 'admin') {
        return [
            'dashboard.php'              => ['Dashboard', '&#9635;'],
            'admin/akun.php?t=dosen'     => ['Teachers', '&#9776;'],
            'admin/akun.php?t=mahasiswa' => ['Students', '&#9776;'],
            'admin/kelas.php'            => ['Settings', '&#9881;'],
        ];
    }

    $hal_dashboard = ($role === 'dosen') ? 'dosen/kelas.php' : 'mahasiswa/dashboard.php';
    $menu = [
        'dashboard.php' => ['Home', '&#8962;'],
        $hal_dashboard  => ['Dashboard', '&#9635;'],
        'profile.php'   => ['Profile', '&#9786;'],
    ];

    if ($role === 'dosen') {
        $menu['dosen/nilai.php'] = ['Grades', '&#9780;'];
    } else {
        $menu['mahasiswa/grades.php'] = ['Grades', '&#9780;'];
    }

    $menu['blog.php']      = ['Blog', '&#9998;'];
    $menu['social.php']    = ['Social', '&#9741;'];
    $menu['panduan.xhtml'] = ['Panduan', '&#9432;'];
    return $menu;
}

function top($title, $active, $showTitle = true, $extraCss = [])
{
    $user  = $_SESSION['u'];
    $menu  = menu($user['role']);
    $label = isset($menu[$active][0]) ? $menu[$active][0] : $title;
    $sub   = ($user['role'] === 'admin') ? 'Staff IT Universitas Atma Jaya Yogyakarta' : ucfirst($user['role']);

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
    echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id"><head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>' . e($title) . ' - LMS</title>';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/variables.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/reset.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/sidebar.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/topbar.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/dashboard.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/forms-buttons.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/table-badges.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/modal.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/stats.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/profile.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/blog.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/auth.css" />';
    $n_css = count($extraCss);
    for ($i = 0; $i < $n_css; $i++) {
        echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/' . e($extraCss[$i]) . '" />';
    }
    echo '</head><body><div class="app">';

    echo '<aside class="side">';
    echo '<div class="logo">';
    echo '<img src="' . BASE . '/images/UAJY-LOGOGRAM_-01.png" alt="Logo UAJY" width="36" height="36" />';
    echo '<div><b>UNIVERSITAS<br />ATMA JAYA YOGYAKARTA</b><small>servians in lumine veritatis</small></div>';
    echo '</div>';
    echo '<ul>';
    $urls = array_keys($menu);
    $n_m  = count($urls);
    for ($i = 0; $i < $n_m; $i++) {
        $url   = $urls[$i];
        $item  = $menu[$url];
        $class = ($url === $active) ? ' class="on"' : '';
        echo '<li><a href="' . BASE . '/' . $url . '"' . $class . '><i>' . $item[1] . '</i>' . $item[0] . '</a></li>';
    }
    echo '</ul>';
    echo '<div class="me" style="padding:10px 14px;gap:10px;align-items:center;">';
    echo '<span class="av" style="width:34px;height:34px;line-height:34px;font-size:13px;flex-shrink:0;">' . e(ini($user['nama'])) . '</span>';
    echo '<div style="font-size:13px;line-height:1.3;">' . e($user['nama']) . '<small style="display:block;font-size:11px;margin-top:2px;">' . $sub . ' | <a href="' . BASE . '/logout.php">Log out</a></small></div>';
    echo '</div>';
    echo '</aside>';

    echo '<main class="main">';
    echo '<header class="topbar">';
    echo '<span class="bc">&#9647; &nbsp;&#9734; &nbsp;&nbsp;<span class="mut">' . e($label) . '</span> &nbsp;/&nbsp; Overview</span>';
    echo '<span class="tools"><span class="srch">Search &nbsp;&nbsp;&#8984;K</span> &#9788; &#8634; &#9750;</span>';
    echo '</header>';
    echo '<div class="content">';

    if ($showTitle) {
        echo '<h1>' . e($title) . '</h1>';
    }
    $pesan = flash();
    if ($pesan) {
        echo '<div class="flash">' . e($pesan) . '</div>';
    }
}

function bottom()
{
    echo '</div></main></div>';
    echo '<script type="text/javascript" src="' . BASE . '/js/app.js"></script>';
    echo '</body></html>';
}

function auth_top($title)
{
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
    echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id"><head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>' . e($title) . '</title>';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/variables.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/reset.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/forms-buttons.css" />';
    echo '<link rel="stylesheet" type="text/css" href="' . BASE . '/css/auth.css" /></head>';
    echo '<body class="auth"><div class="card">';
}

function auth_bottom()
{
    echo '</div></body></html>';
}

function panel($id, $role)
{
    if ($role === 'mahasiswa') {
        $tugas = q("SELECT t.judul, k.nama AS kelas, " . DL . " AS dl
                    FROM lms_dosen.tugas t
                    JOIN lms_admin.kelas k ON k.id = t.kelas_id
                    JOIN lms_admin.kelas_mhs km ON km.kelas_id = k.id AND km.mhs_id = ?
                    ORDER BY t.id DESC LIMIT 6", [$id, $id])->fetchAll();
        $materi = q("SELECT m.judul, k.nama AS kelas, m.dibuat
                     FROM lms_dosen.materi m
                     JOIN lms_admin.kelas k ON k.id = m.kelas_id
                     JOIN lms_admin.kelas_mhs km ON km.kelas_id = k.id AND km.mhs_id = ?
                     ORDER BY m.id DESC LIMIT 4", [$id])->fetchAll();
    } else {
        $tugas = q("SELECT t.judul, k.nama AS kelas, t.deadline AS dl
                    FROM lms_dosen.tugas t
                    JOIN lms_admin.kelas k ON k.id = t.kelas_id
                    WHERE t.dosen_id = ? ORDER BY t.id DESC LIMIT 6", [$id])->fetchAll();
        $materi = q("SELECT m.judul, k.nama AS kelas, m.dibuat
                     FROM lms_dosen.materi m
                     JOIN lms_admin.kelas k ON k.id = m.kelas_id
                     WHERE m.dosen_id = ? ORDER BY m.id DESC LIMIT 4", [$id])->fetchAll();
    }

    echo '<aside class="panel"><h2>Baru baru ini di akses</h2>';
    $n_t = count($tugas);
    for ($i = 0; $i < $n_t; $i++) {
        $x = $tugas[$i];
        echo '<div class="it"><span class="ic red">&#9635;</span><div>' . e($x['judul'])
           . '<small>' . e(date('D, j M, H:i', strtotime($x['dl']))) . '</small></div></div>';
    }
    $n_m = count($materi);
    for ($i = 0; $i < $n_m; $i++) {
        $x = $materi[$i];
        echo '<div class="it"><span class="ic blue">&#9780;</span><div>' . e($x['judul'])
           . '<small>' . e($x['kelas']) . ' &middot; ' . e(date('j M', strtotime($x['dibuat']))) . '</small></div></div>';
    }
    if (!$tugas && !$materi) {
        echo '<div class="it">Belum ada aktivitas.</div>';
    }
    echo '</aside>';
}

function cal($marks)
{
    $months = [
        '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $offset = (int) date('w', strtotime(date('Y-m-01')));
    $days   = (int) date('t');
    $today  = (int) date('j');

    echo '<div class="box"><b>' . $months[(int) date('n')] . ' ' . date('Y') . ' &rsaquo;</b>';
    echo '<table class="cal"><tr><th>SUN</th><th>MON</th><th>TUE</th><th>WED</th><th>THU</th><th>FRI</th><th>SAT</th></tr><tr>';
    for ($i = 0; $i < $offset; $i++) {
        echo '<td></td>';
    }
    for ($d = 1; $d <= $days; $d++) {
        if ($d > 1 && ($d + $offset - 1) % 7 === 0) {
            echo '</tr><tr>';
        }
        $class = '';
        if ($d === $today) {
            $class = 'today';
        } elseif (isset($marks[$d])) {
            $class = 'mark';
        }
        echo '<td><span class="' . $class . '">' . $d . '</span></td>';
    }
    echo '</tr></table></div>';
}
?>