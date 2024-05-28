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

if (!isset($_GET['kelas'])) {
    header("Location: ./spp.php?nisn=$nisn&kelas=1");
}

$kelasnum = test_input($_GET['kelas']);
$kelasnum -= 1;

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
            padding: 6px 0;
            font-size: xx-large;
        }

        table { 
            border-collapse: collapse; 
            width: 100%; 
        }

        th, td {
            border: 1px solid #224; 
            padding: 0.5em; 
            text-align: left;
        }

        .main {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    </style>
</head>
<body>
    <a href="dashboard.php">DASHBOARD</a>
    <br><hr><br>

    <?php
    echo $nisn;
    $data = check_student_spp($nisn);

    foreach(array_keys($data) as $d) {
        $$d = $data[$d];
    } 

    $total = array($total_kelas1, $total_kelas2, $total_kelas3, $total_kelas4);


    echo " - ($kelas $jurusan $bagian)";
    echo "<h2>$nama</h2>";

    echo $status;
    if($status == 'BELUM') { echo " LUNAS"; }

    $terbayar_hold = $terbayarkan;

    echo " - (Rp$terbayar_hold dari Rp$total[$kelasnum])";
    ?>

    <br><br><hr><br>

    <div class="main">    
        <div id="spp-list">
            <?php
                $month = [
                    'Juli', 
                    'Agustus', 
                    'September', 
                    'Oktober', 
                    'November',
                    'Desember',
                    'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                ];

                $unpaid = str_split($hiraubayar);

                foreach (array(1,2,3,4) as $n) {
                    if(!in_array($n, $unpaid)) {
                        $displaymode = ($n-1 !== $kelasnum) ? "style=\"display: none;\"" : "";
                        echo "<div id=\"kelas$n\" $displaymode>";
                        echo "<table><tr><th>Bulan</th><th>Terbayarkan</th></tr>";

                        $terhitung = round($total[$n-1]/$lingkup_bulan);

                        for ($i=0; $i < $lingkup_bulan; $i++) { 
                            if ($terbayarkan >= $terhitung) {
                                echo "<tr><td>".$month[$i]."</td><td>$terhitung</td></tr>";
                                $terbayarkan -= $terhitung;
                            } else {
                                echo "<tr><td>".$month[$i]."</td><td>$terbayarkan</td></tr>";
                                $terbayarkan = 0;
                            }
                        } echo "</table></div>";
                    }
                }

                
            ?>

        </div>
        <div id="sidenav">
            <ul>
                <?php
                    $classroman = array('X', 'XI', 'XII', 'XIII');
                    foreach (array(1,2,3,4) as $n) {
                        if(!in_array($n, $unpaid)) {
                            echo "<li><a href=\"spp.php?nisn=$nisn&kelas=$n\">Kelas ".$classroman[$n-1]."</a></li>";
                        }
                    }
                ?>

                
                <h3>Tambahkan Nomina</h3>
                <input type="text" name="bayartambah">
                <button onclick="setNominal('bayartambah')">s</button>
                <br><br><hr>
                <h3>Kurangi Nomina</h3>
                <input type="text" name="bayarkurang">
                <button onclick="setNominal('bayarkurang', -1)">ss</button>
            </ul>

        </div>
    </div>

    <script>
        const terbayar = <?php echo $terbayar_hold ?>;
        const url = "sys/action.php";

        function setNominal(name, multiplier = 1) {
            const nominaltxt = document.getElementsByName(name)[0];
            let nominal = Number(nominaltxt.value);

            const xhr = new XMLHttpRequest();
            const par = `act=NOMINAL&money=${terbayar+(nominal*multiplier)}&before=${terbayar}`;
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    console.log(this.responseText);
                    location.reload();
                }
            };
            xhr.send(par);
        }

    </script>


</body>
</html>