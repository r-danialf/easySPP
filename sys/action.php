<?php
    session_start();

    require_once "dbconnect.php";

    if (isset($_GET['action']) && $_GET['action'] === 'set_chosen') {
        set_chosen($_GET['jurusan'], $_GET['kelas'], $_GET['bagian']);
    }

    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        setcookie("usercode", "", time() - a_month, "/");
        header("Location: ../login.php");
    }

    if (isset($_POST['act']) && $_POST['act'] === 'CHECK') {
        check_student($_POST["nisn"]);
    }

    if (isset($_POST['act']) && $_POST['act'] === 'NOMINAL') {
        update_data("spp_siswa", array("terbayarkan" => test_input($_POST['money'])), array("terbayarkan" => $_POST['before']));
    }

    function set_chosen($jurusan, $kelas, $bagian) {
        $_SESSION['chosen'] = [
            'jurusan' => $jurusan,
            'kelas'   => $kelas,
            'bagian'  => $bagian
        ];
    }
    
?>