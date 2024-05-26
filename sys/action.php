<?php
    session_start();

    require_once "dbconnect.php";

    if (isset($_GET['action']) && $_GET['action'] === 'set_chosen') {
        set_chosen($_GET['jurusan'], $_GET['kelas'], $_GET['bagian']);
    }

    if (isset($_POST['act']) && $_POST['act'] === 'CHECK') {
        check_student($_POST["nisn"]);
    }

    function set_chosen($jurusan, $kelas, $bagian) {
        $_SESSION['chosen'] = [
            'jurusan' => $jurusan,
            'kelas'   => $kelas,
            'bagian'  => $bagian
        ];
    }
    
?>