<?php

session_start();

require "../config.php";


if (isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $cekUser = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$username'");


    if (mysqli_num_rows($cekUser) === 1) {
        $row = mysqli_fetch_assoc($cekUser);
        if (password_verify($password, $row['password'])) {
            // session start
            $_SESSION['ssLoginRM'] = true;
            $_SESSION['ssUserRM'] = $username;
            
            header("location: ../index.php"); //index halaman dashboard luar
            exit();
        } else {
            echo "<script>alert('Login gagal ! Password Salah'); document.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Login gagal ! Password Salah'); document.location.href='login.php';</script>";
        exit();
    }
}
