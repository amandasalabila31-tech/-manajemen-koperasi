<?php
session_start();
include "koneksi.php";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn,"SELECT * FROM users
        WHERE username='$username' AND password='$password'");

    if(mysqli_num_rows($query) > 0){

        $data = mysqli_fetch_assoc($query);

        $_SESSION['id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        switch($data['role']){

            case 'admin':
                header("Location: admin/dashboard.php");
                break;

            case 'ketua':
                header("Location: ketua/dashboard.php");
                break;

            case 'bendahara':
                header("Location: bendahara/dashboard.php");
                break;

            case 'teller':
                header("Location: teller/dashboard.php");
                break;

            case 'anggota':
                header("Location: anggota/dashboard.php");
                break;

            default:
                header("Location: login.php");
        }

    }else{

        echo "<script>
        alert('Username atau Password Salah!');
        window.location='login.php';
        </script>";

    }
}
?>