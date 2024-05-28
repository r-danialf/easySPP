<?php
    $server_hostname = "localhost";
    $server_username = "root";
    $server_password = "";
    $server_database = "spp";

    function check_user_tu( $username, $password ) {
        global $server_hostname, $server_username, $server_password, $server_database;

        $username = test_input( $username );
        $password = test_input( $password );

        if( preg_match("/^[a-zA-Z-' ]*$/", $username) &&
        preg_match("/^[0-9A-Za-z@#\-_$%^&+=ยง!\?]*$/", $password) ) {
            $conn = new mysqli($server_hostname, $server_username, $server_password, $server_database);
            
            if ($conn->connect_error) {
                die("CONNECT_FAIL: $conn->connect_error");
            }
            
            $sql = "SELECT * FROM USER_TU";
            $res = $conn->query($sql);
            
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    if ($row["user"] == $username && $row["pass"] == $password) {
                        $conn->close();
                        return true;
                    }
                }
            }
            $conn->close();
            return false;
        }
    }

    function check_student( $nisn ) {
        global $server_hostname, $server_username, $server_password, $server_database;
        $nisn = test_input( $nisn );

        $conn = new mysqli($server_hostname, $server_username, $server_password, $server_database);
            
        if ($conn->connect_error) {
            die("CONNECT_FAIL: $conn->connect_error");
        }
            
        $sql = "SELECT * FROM SISWA";
        $res = $conn->query($sql);
            
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                if ($row["nisn"] == $nisn) {
                    echo '{"nama":"'.$row["nama"].'", "kelas":"'.$row["kelas"].'",'.
                        ' "jurusan":"'.$row["jurusan"].'", "bagian":"'.$row["bagian"].'"}';
                }
            }
            $conn->close();
            return true;
        }
        $conn->close();
        return false;
    }

    function check_student_spp( $nisn ) {
        global $server_hostname, $server_username, $server_password, $server_database;
        $conn = new mysqli($server_hostname, $server_username, $server_password, $server_database);

        

        $keys = implode(",", array('nama','kelas','siswa.jurusan','bagian','lingkup_bulan', 'total_kelas1', 'total_kelas2', 'total_kelas3', 'total_kelas4'));
        $data = array();

        if ($conn->connect_error) {
            die("CONNECT_FAIL: $conn->connect_error");
        }

        $sql = "SELECT $keys FROM siswa INNER JOIN info_spp_jurusan WHERE siswa.jurusan = info_spp_jurusan.jurusan AND siswa.nisn='$nisn'";
        $res = $conn->query($sql);

        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $key = array_keys($row);
                $val = array_values($row);

                $i = 0;
                foreach($key as $k) {
                    $data[$k] = $val[$i];
                    $i+=1;
                }
            }
        }

        $keys = implode(",", array('status','terbayarkan','hiraubayar'));
        $sql = "SELECT $keys FROM spp_siswa WHERE nisn='$nisn'";
        $res = $conn->query($sql);

        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $key = array_keys($row);
                $val = array_values($row);

                $i = 0;
                foreach($key as $k) {
                    $data[$k] = $val[$i];
                    $i+=1;
                }
            }
        }

        return $data;
    }

    function show_table( $table, $clickable="" ) {
        global $server_hostname, $server_username, $server_password, $server_database;
        $conn = new mysqli($server_hostname, $server_username, $server_password, $server_database);
            
        if ($conn->connect_error) {
            die("CONNECT_FAIL: $conn->connect_error");
        }

        $sql = "SELECT * FROM $table";
        $res = $conn->query($sql);

        $headprinted = false;
        $i = 0;
        
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $key = array_keys($row);
                $val = array_values($row);

                if (!$headprinted) {
                    foreach($key as $k) {
                        echo "<th>$k</th>";
                    } $headprinted = true;
                }

                if ($clickable !== "") {
                    $onclick = "onclick=\"$clickable($i)\" id=\"$i\"";
                } else { $onclick = ""; }

                echo "<tr $onclick>";
                foreach ($val as $v) {
                    echo "<td>$v</td>";
                }
                echo "</tr>";

                $i+=1;
            }
        }
        $conn->close();
    }

    function show_spp_table( $jurusan, $kelas, $bagian ) {
        global $server_hostname, $server_username, $server_password, $server_database;
        $conn = new mysqli($server_hostname, $server_username, $server_password, $server_database);
            
        if ($conn->connect_error) {
            die("CONNECT_FAIL: $conn->connect_error");
        }

        $sql = "SELECT siswa.nisn, nama, terbayarkan, status FROM spp_siswa INNER JOIN siswa WHERE jurusan=\"$jurusan\" AND kelas=\"$kelas\" AND bagian=\"$bagian\" AND spp_siswa.nisn = siswa.nisn ";
        $res = $conn->query($sql);

        $headprinted = false;
        $i = 0;
        
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $key = array_keys($row);
                $val = array_values($row);

                if (!$headprinted) {
                    foreach($key as $k) {
                        echo "<th>$k</th>";
                    } $headprinted = true;
                }

                $onclick = "onclick=\"window.location.href = 'spp.php?nisn=".$val[0]."'\" id=\"$i\"";

                echo "<tr $onclick>";
                foreach ($val as $v) {
                    echo "<td>$v</td>";
                }
                echo "</tr>";

                $i+=1;
            }
        }
        $conn->close();
    }

    function insert_data( $table, $data ) {
        global $server_hostname, $server_username, $server_password, $server_database;
        $conn = new mysqli($server_hostname, $server_username, $server_password, $server_database);
            
        if ($conn->connect_error) {
            die("CONNECT_FAIL: $conn->connect_error");
        }

        echo var_dump($data);

        $values = array();

        foreach($data as $val) {
            $values[] = "'$val'";
        }

        $values = implode(",", $values);

        $sql = "INSERT INTO $table VALUE ($values)";

        if ($conn->query($sql) === FALSE) {
            echo "ERROR: " . $sql . $conn->error;
        }

        $conn->close();
    }

    function update_data( $table, $data, $where ) {
        global $server_hostname, $server_username, $server_password, $server_database;
        $conn = new mysqli($server_hostname, $server_username, $server_password, $server_database);
            
        if ($conn->connect_error) {
            die("CONNECT_FAIL: $conn->connect_error");
        }

        $dataval = array();

        foreach($data as $key => $val) {
            $dataval[] = "$key='$val'";
        } $dataval = implode(",", $dataval);

        foreach($where as $key => $val) { $where = "$key='$val'"; }

        $sql = "UPDATE $table SET $dataval WHERE $where";
        echo $sql;

        if ($conn->query($sql) === FALSE) {
            echo "ERROR: " . $sql . $conn->error;
        } else {
            echo $sql;
        }

        $conn->close();
    }

    function delete_data( $table, $where ) {
        global $server_hostname, $server_username, $server_password, $server_database;
        $conn = new mysqli($server_hostname, $server_username, $server_password, $server_database);
            
        if ($conn->connect_error) {
            die("CONNECT_FAIL: $conn->connect_error");
        }

        foreach ($where as $key => $val) { $where = "$key='$val'"; }

        $sql = "DELETE FROM $table WHERE $where";

        if ($conn->query($sql) === FALSE) {
            echo "ERROR: " . $sql . $conn->error;
        } else {
            echo $sql;
        }

        $conn->close();
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }