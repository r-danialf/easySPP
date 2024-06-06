<?php
session_start();
require "../sys/dbconnect.php";

define("a_month", 86400*30);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION["username"] = test_input($_POST["username"]);
    $_SESSION["password"] = test_input($_POST["password"]);

    if (check_user_tu($_SESSION["username"], $_SESSION["password"])) {
        setcookie("usercode", $_SESSION["username"].":".$_SESSION["password"], time() + a_month, "/");
    } else {
        header("Location: ./login.php");
    }
}

if (isset($_COOKIE["usercode"])) {
    list($_SESSION["username"], $_SESSION["password"]) = explode(":", $_COOKIE["usercode"]);

    if ( !check_user_tu($_SESSION["username"], $_SESSION["password"]) ) {
        setcookie("usercode", "", time() - 3600, "/");
        header("Location: ../login.php");
    }
} else {
    header("Location: ../login.php");
}


$spp_jurusan = ['rpl', 'gp', 'ph', 'tb', 'atr', 'tp'];
$spp_kelas   = ['x', 'xi', 'xii', 'xiii'];
$spp_bagian  = ['1', '2', '3', '4'];

$inavailability = [
    'rpl' => ['xiii'],
    'gp'  => ['2', '3', '4'],
    'ph'  => ['3', '4', 'xiii'],
    'tb'  => ['3', '4', 'xiii'],
    'atr' => ['3', '4', 'xiii'],
    'tp'  => ['3', '4', 'xiii'] 
];

if (!isset($_SESSION['chosen'])) {
    $_SESSION['chosen'] = [
        'jurusan' => 'rpl',
        'kelas'   => 'x',
        'bagian'  => '1' ]; 
    } 
    
    function test_kelas() {
        global $inavailability;
        if (in_array($_SESSION['chosen']['kelas'], $inavailability[$_SESSION['chosen']['jurusan']])) {
            $_SESSION['chosen']['kelas'] = "x";
        }
    }
    
    function test_bagian() {
        global $inavailability;
        if (in_array($_SESSION['chosen']['bagian'], $inavailability[$_SESSION['chosen']['jurusan']])) {
            $_SESSION['chosen']['bagian'] = "1";
        }
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>easySPP Dashboard</title>
    <style>
        body {
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }
        .griddy { display: grid; padding: 0;}

        .top-navbar { 
            grid-template-columns: 1fr 1fr; 
            background-color: #50b050;
            padding: 0 2em;
            color: white;
            font-weight: bold;
        }

        .no-list { list-style: none; }

        .navbar-links { 
            text-align: right; 
            list-style: none; 
            align-self: center;
        } .navbar-links * { 
            display: inline;
            text-decoration: none;
            color: white;
        } .navbar-links li * {
            padding: 0.8em;
        } .navbar-links li:hover * {
            color: #50b050;
            background-color: white;
            cursor: pointer;
        }

        .selectednavbar {
            color: white;
            background-color: #208020;
        }

        .main-page { grid-template-columns: 1fr 3fr; }

        .spp-search { background-color: #ddf; }

        .sidenav-links {
            list-style: none; 
            align-self: center;
            text-align: center;
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin: 0;
            padding: 0;
        } .sidenav-links * {
            text-decoration: none;
            padding: 1em 0;
            display: inline;
            color: black;
            background-color: #ccf;
        } .sidenav-links li:hover, li:hover * {
            background-color: #aaf;
            cursor: pointer;
        }

        .selectedsidenav { border-bottom: 0.25rem solid #50b050; }
        .sidenav-category { padding: 1em; }

        .sidenav-options-kelas {
            list-style: none; 
            align-self: center;
            padding: 0.5em;
            margin: 0.5em;
            display: grid;
        } .sidenav-options-kelas * {
            text-decoration: none;
            text-align: center;
            display: inline;
            padding: 0.5em;
        } .sidenav-options-kelas li:hover {
            background-color: #aaf;
            cursor: pointer;
        }

        .sidenav-jurusan-options { grid-template-columns: repeat(<?php echo count($spp_jurusan); ?>, 1fr); }
        .sidenav-kelas-options { grid-template-columns: repeat(<?php echo count($spp_kelas); ?>, 1fr); }
        .sidenav-bagian-options { grid-template-columns: repeat(<?php echo count($spp_bagian); ?>, 1fr); }
        
        .sidenav-chosen-option { border-bottom: 0.25rem solid #50b050; background-color: #ccf; }

        .spp-lists table {
            border-collapse: collapse; 
            width: 100%; 
        } .spp-lists table * {
            text-align: left;
        } .spp-lists table th {
            background-color: #bbb;
        } .spp-lists table tr,td,th {
            padding: 1em; 
        }.spp-lists table tr:hover {
            background-color: #ddd;
            cursor: pointer;
        }

        .fakelink {
            color: #55f;
        } .fakelink:hover {
            text-decoration: underline;
            cursor: pointer;
        }

        .fulfilled-spp {
            padding: 0.4em 1em;
            background-color: #50b050;
            color: white;
        } .unfulfilled-spp {
            padding: 0.4em 1em;
            background-color: #c03030;
            color: white;
        }

        .text-input {
            display: block;
            margin: 1em;
            width: 85%;
            font-size:large;
            height: 2em;
            padding: 3px;
        }

        .search-by-nisn {
            display: none;
        } .search-by-class {
            display: block;
        }

        .spp-list-hidden {
            display: none;
        }

    </style>
</head>
<body>
    <div class="top-navbar griddy">
        <h1>easySPP Dashboard</h1>
        <ul class="navbar-links">
            <li><a href="spp.php" class="selectednavbar">Data SPP</a></li>
            <li><a href="siswa.php">Data Siswa</a></li>
            <li><a onclick="dbAccess()">Database</a></li>
            <li><a href="../sys/action.php?action=logout">Log Out</a></li>
        </ul>
    </div>

    <div class="main-page griddy">
        <div class="spp-search">
            <ul class="sidenav-links">
                <li><a onclick="findBy('class')" class="selectedsidenav class-button">Cari dengan Kelas</a></li>
                <li><a onclick="findBy('nisn')" class="nisn-button">Cari dengan NISN</a></li>
            </ul>
            <br><br>
            <div class="search-by-class">
                <b class="sidenav-category">JURUSAN: </b>
                <ul class="sidenav-options-kelas sidenav-jurusan-options">
                    <?php
                    foreach ($spp_jurusan as $jurusan) {
                        test_kelas(); test_bagian();
                        $chosen = ($jurusan === $_SESSION['chosen']['jurusan']);
                        $status = $chosen ? 'sidenav-chosen-option' : '';
                        echo "<li onclick=\"set_chosen('$jurusan','".$_SESSION['chosen']['kelas']."','".$_SESSION['chosen']['bagian']."','$jurusan');\" class=\"$status\">".strtoupper($jurusan)."</li>";
                    } ?>
                </ul>
                <b class="sidenav-category">KELAS: </b>
                <ul class="sidenav-options-kelas sidenav-kelas-options">
                    <?php
                    foreach ($spp_kelas as $kelas) {
                        $unavailable = in_array($kelas, $inavailability[$_SESSION['chosen']['jurusan']]);
                        $chosen = ($kelas === $_SESSION['chosen']['kelas']);
                        $status = $chosen ? 'sidenav-chosen-option' : '';
                        if (!$unavailable) {
                            echo "<li onclick=\"set_chosen('".$_SESSION['chosen']['jurusan']."','$kelas','".$_SESSION['chosen']['bagian']."')\" class=\"$status\">".strtoupper($kelas)."</li>";
                        }
                    } ?>
                </ul>
                <b class="sidenav-category">BAGIAN: </b>
                <ul class="sidenav-options-kelas sidenav-bagian-options">
                    <?php
                        foreach ($spp_bagian as $bagian) {
                            $unavailable = in_array($bagian, $inavailability[$_SESSION['chosen']['jurusan']]);
                            $chosen = ($bagian === $_SESSION['chosen']['bagian']);
                            $status = $chosen ? 'sidenav-chosen-option' : '';
                            if (!$unavailable) {
                                echo "<li onclick=\"set_chosen('".$_SESSION['chosen']['jurusan']."','".$_SESSION['chosen']['kelas']."','$bagian')\" class=\"$status\">$bagian</li>";
                            }
                        }
                    ?>
                </ul>
            </div>
            <div class="search-by-nisn">
                <b class="sidenav-category">NISN SISWA: </b>
                <input type="text" name="nisn" placeholder="Masukkan no. NISN" class="text-input" oninput="get_student_by_nisn();">
            </div>
        </div>
        <div class="spp-lists">
            <table class="spp-list-byclass-table">
                <?php
                if (!isset($_SESSION['chosen'])) { set_chosen('RPL', 'X', '1'); }
                list($jurusan, $kelas, $bagian) = array_values($_SESSION['chosen']);
                show_spp_table2($jurusan, $kelas, $bagian);
                ?>
            </table>
            <table class="spp-list-bynisn-table spp-list-hidden">
                <?php
                show_spp_table3('');
                ?>
            </table>
        </div>
    </div>

    <script>
        const findClassSection = document.getElementsByClassName("search-by-class")[0];
        const findNisnSection = document.getElementsByClassName("search-by-nisn")[0];
        
        const listSppClass = document.getElementsByClassName("spp-list-byclass-table")[0];
        const listSppNisn = document.getElementsByClassName("spp-list-bynisn-table")[0];

        function dbAccess() {
            const pass = window.prompt("Enter Database Password:");

            if (pass !== null && pass === "<?php echo $server_password; ?>") {
                window.location.href = '../database.php';
            }
        }

        function findBy(category) {
            findClassSection.style.display = "none";
            findNisnSection.style.display = "none";
            listSppClass.classList.add("spp-list-hidden");
            listSppNisn.classList.add("spp-list-hidden");

            const classButton = document.getElementsByClassName("class-button")[0];
            const nisnButton = document.getElementsByClassName("nisn-button")[0];
            classButton.classList.remove("selectedsidenav");
            nisnButton.classList.remove("selectedsidenav");

            switch (category) {
                case 'class':
                    findClassSection.style.display = "block";
                    listSppClass.classList.remove("spp-list-hidden");
                    classButton.classList.add("selectedsidenav");
                break;
                case 'nisn':
                    findNisnSection.style.display = "block";
                    listSppNisn.classList.remove("spp-list-hidden");
                    nisnButton.classList.add("selectedsidenav");
                break;
                default: break;
            }
        }

        function set_chosen(jurusan, kelas, bagian) {
            const xhr = new XMLHttpRequest();
            const url = '../sys/action.php?action=set_chosen&jurusan='+jurusan+'&kelas='+kelas+'&bagian='+bagian;
            xhr.open('GET', url, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send();
        }

        function get_student_by_nisn() {
            const nisnBox = document.getElementsByName("nisn")[0];
            const nisn = nisnBox.value;

            const xhr = new XMLHttpRequest();
            const url = '../sys/action.php?action=get_student&nisn='+nisn;
            xhr.open('GET', url, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    listSppNisn.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</body>
</body>
</html>