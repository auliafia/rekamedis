<?php

session_start();

if (!isset($_SESSION['ssLoginRM'])) {
    header("location: ../otentikasi/index.php");
    exit();
}


require "../config.php";

if (isset($_POST['simpan'])) {
    $username = trim(htmlspecialchars($_POST['username']));
    $nama = trim(htmlspecialchars($_POST['fullname']));
    $jabatan = $_POST['jabatan'];
    $alamat = trim(htmlspecialchars($_POST['alamat']));
    $gambar = htmlspecialchars($_FILES['gambar']['name']);
    $password = trim(htmlspecialchars($_POST['password']));
    $password2 = trim(htmlspecialchars($_POST['password2']));

    $cekUsername = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$username'");
    if (mysqli_num_rows($cekUsername)) {
        echo "<script>alert('Username sudah ada!, user baru gagal di registrasi'); window.location='tambah-user.php';</script>";
        return;
    }

    if ($password !== $password2) {
        echo "<script>alert('Konfirmasi password tidak sesuai, user baru gagal di registrasi!'); window.location='tambah-user.php';</script>";
        return;
    }

    $pass = password_hash($password, PASSWORD_DEFAULT);

    if ($gambar != null) {
        $url = 'tambah-user.php';
        $gambar = uploadGbr($url);
    } else {
        $gambar = 'default-user.png';
    }

    $query = "INSERT INTO tbl_user (username, fullname, password, jabatan, alamat, gambar) VALUES ('$username', '$nama', '$pass', '$jabatan', '$alamat', '$gambar')";
    mysqli_query($koneksi, $query);

    echo "<script>alert('User baru berhasil di registrasi!'); window.location='tambah-user.php';</script>";
    return;
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus-user') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $gbr = isset($_GET['gambar']) ? $_GET['gambar'] : null;

    if ($id && $gbr) {
        mysqli_query($koneksi, "DELETE FROM tbl_user WHERE userid = $id");
        if ($gbr != 'user.png') {
            unlink('../asset/gambar/' . $gbr);
        }
        echo "<script>alert('User berhasil di hapus!'); window.location='index.php';</script>";
    }
    return;
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $usernameLama = trim(htmlspecialchars($_POST['usernameLama']));
    $username = trim(htmlspecialchars($_POST['username']));
    $nama = trim(htmlspecialchars($_POST['fullname']));
    $jabatan = $_POST['jabatan'];
    $alamat = trim(htmlspecialchars($_POST['alamat']));
    $gambar = htmlspecialchars($_FILES['gambar']['name']);
    $gbrLama = htmlspecialchars($_POST['gbrLama']);

    $cekUsername = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$username'");
    if ($username !== $usernameLama) {
        if (mysqli_num_rows($cekUsername)) {
            echo "<script>
                alert('Username sudah ada!, user gagal memperbarui'); 
                window.location='tambah-user.php';
            </script>";
            return;
        }
    }

    if ($gambar != null) {
        $url = 'tambah-user.php';
        $gambar = uploadGbr($url);
        if ($gbrLama !== 'default-user.png') {
            @unlink('../asset/gambar/' . $gbrLama);
        }
    } else {
        $gambar = $gbrLama;
    }

    mysqli_query($koneksi, "UPDATE tbl_user SET 
        username = '$username',
        fullname = '$nama',
        jabatan = '$jabatan',
        alamat = '$alamat',
        gambar = '$gambar'
        WHERE userid = $id 
    ");

    echo "<script>
            alert(' Data user berhasil di registrasi!'); 
            window.location='index.php';
    </script>";
    return;
}

//ganti password
if (isset($_POST['ganti-password'])) {
    $curPass    = trim(htmlspecialchars($_POST['oldPass']));
    $newPass    = trim(htmlspecialchars($_POST['newPass']));
    $confPass   = trim(htmlspecialchars($_POST['confPass']));

    $userLogin  = $_SESSION['ssUserRM'];
    $queryUser  = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$userLogin' ");
    $dataUser   = mysqli_fetch_assoc($queryUser);

    if ($newPass != $confPass) {
        echo "<script>
            alert('Password gagal diperbarui, Konfirmasi Password tidak sama !'); 
            window.location='../otentikasi/password.php';
        </script>";
        return false;
    }

    if (!password_verify($curPass, $dataUser['password'])) {
        echo "<script>
            alert('Password gagal diperbarui, password lama tidak cocok !'); 
            window.location='../otentikasi/password.php';
        </script>";
        return false;
    } else {
        $pass   = password_hash($newPass, PASSWORD_DEFAULT);
        mysqli_query($koneksi, "UPDATE tbl_user SET password = '$pass' WHERE username = '$userLogin' ");
        echo "<script>
            alert('Password berhasil diperbarui!'); 
            window.location='../otentikasi/password.php';
        </script>";
        return true;
    }
}
