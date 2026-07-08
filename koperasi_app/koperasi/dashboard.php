<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
?>

<h2>Dashboard Admin</h2>
<h4>Selamat Datang, <?php echo $_SESSION['username']; ?></h4>


Untuk *ketua, **bendahara, **teller, dan **anggota*, cukup ubah pengecekan rolenya.

Contoh:

php
if($_SESSION['role'] != 'bendahara'){
    header("Location: ../login.php");
    exit;
}

<?php
session_start();

if(!isset($_SESSION['username'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
?>

<h2>Dashboard Admin</h2>
<h4>Selamat Datang, <?php echo $_SESSION['username']; ?></h4>


Untuk *ketua, **bendahara, **teller, dan **anggota*, cukup ubah pengecekan rolenya.

Contoh:

php
if($_SESSION['role'] != 'bendahara'){
    header("Location: ../login.php");
    exit;
}

