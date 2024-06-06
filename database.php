<?php
    session_start();
    require_once "sys/dbconnect.php";

    $databases = ['db_user_tu', 'db_siswa', 'db_spp_siswa', 'db_info_spp_jurusan'];
    $db = "";

    if (isset($_COOKIE["usercode"])) {
        list($_SESSION["username"], $_SESSION["password"]) = explode(":", $_COOKIE["usercode"]);

        if ( !check_user_tu($_SESSION["username"], $_SESSION["password"]) ) {
            setcookie("usercode", "", time() - 3600, "/");
            header("Location: ./login.php");
        }
    } else {
        header("Location: ./login.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        if (!in_array(test_input($_GET["db"]), $databases)) {
            header("Location: ./database.php?db=db_user_tu");
        } else {
            $db = test_input($_GET["db"]);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>easySPP Database Sector</title>

    <style>
        html, body { height: 99%; }
        #page { height: 90%; }
    </style>
</head>
<body>
    <a href="database.php?db=db_user_tu">DB USER TU</a> || 
    <a href="database.php?db=db_siswa">DB SISWA</a> || 
    <a href="database.php?db=db_spp_siswa">DB SPP SISWA</a> || 
    <a href="database.php?db=db_info_spp_jurusan">DB INFO SPP JURUSAN</a> ||
    <a href="dashboard/spp.php">DASHBOARD</a>
    
    <hr>

    <iframe src="database/<?php echo $db; ?>.php" width="100%" id="page" frameborder="0"></iframe>
</body>
</html>