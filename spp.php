<?php
session_start();
require "./sys/dbconnect.php";

if (isset($_COOKIE["usercode"])) {
    list($_SESSION["username"], $_SESSION["password"]) = explode(":", $_COOKIE["usercode"]);

    if ( !check_user_tu($_SESSION["username"], $_SESSION["password"]) ) {
        setcookie("usercode", "", time() - 3600, "/");
        header("Location: ./login.php");
    }
} else {
    header("Location: ./login.php");
}

$nisn = test_input($_GET['nisn']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        h2 {
            margin: 0;
            padding: 12px 0;
            font-size: xx-large;
        }
    </style>
</head>
<body>
    <?php
    echo $nisn;
    $data = check_student_spp($nisn);

    foreach(array_keys($data) as $d) {
        $$d = $data[$d];
    } 

    switch ($kelas) {
        case 'XI': $total = $total_kelas2; break;
        case 'XII': $total = $total_kelas3; break;
        case 'XIII': $total = $total_kelas4; break;
        
        default: $total = $total_kelas1; break;
    }

    echo " - ($kelas $jurusan $bagian)";
    echo "<h2>$nama</h2>";

    echo $status;
    if($status == 'BELUM') { echo " LUNAS"; }

    echo " - (Rp$terbayarkan dari Rp$total)";
    

    ?>
</body>
</html>