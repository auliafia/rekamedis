<?php

date_default_timezone_set('Asia/Jakarta');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'db_rekamedis';

$koneksi = mysqli_connect($host, $user, $pass, $dbname);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$main_url = "http://localhost/rekamedis-app/";

function uploadGbr($url)
{
    $namafile = $_FILES['gambar']['name'];
    $ukuran = $_FILES['gambar']['size'];
    $tmp = $_FILES['gambar']['tmp_name'];

    $ekstensiValid = ['jpg', 'jpeg', 'png', 'gif'];
    $ekstensiFile = explode('.', $namafile);
    $ekstensiFile = strtolower(end($ekstensiFile));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>
                alert('Input User Gagal, file yang anda upload bukan gambar');
                window.location='$url';
         </script>";
        die();
    }

    if ($ukuran > 1000000) {
        echo "<script>
                alert('Input User Gagal, maksimal ukuran gambar 1 MB');
                window.location='$url';
         </script>";
        die();
    }

    $namafileBaru = time() . '-' . $namafile;
    $path = '../asset/gambar/' . $namafileBaru;

    if (move_uploaded_file($tmp, $path)) {
        return $namafileBaru;
    } else {
        echo "<script>
            alert('Input User Gagal, gagal mengupload gambar'); 
            window.location='$url';
        </script>";
        die();
    }
}

if (!function_exists('in_date')) {
    function in_date($tgl)
    {
        $dd = substr($tgl, 8, 2);
        $mm = substr($tgl, 5, 2);
        $yy = substr($tgl, 0, 4);

        return $dd . "-" . $mm . "-" . $yy;
    }
}

if (!function_exists('htgUmur')) {
    function htgUmur($tgl_lahir)
    {
        $tglLahir = new DateTime($tgl_lahir);
        $hariini = new DateTime("today");

        $umur = $hariini->diff($tglLahir)->y;

        return $umur . " tahun";
    }
}
