<?php
    function db_connection() {
        $filename = "/workdir/src/txt/db_info.txt";
        if (file_exists($filename)) {
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            $dbname = $lines[0];
            $user = $lines[1];
            $password = $lines[2];
        } else {
            echo "Not found " . $filename . PHP_EOL;
            exit();
        }
        $dsn = 'mysql:dbname='.$dbname.';host=db';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        return $pdo;
    }

    function create_account_table($pdo) {
        $table_name = "account";
        $sql = "create table if not exists ".$table_name
            ." ("
            . "account_id INT AUTO_INCREMENT PRIMARY KEY,"
            . "user_name char(32),"
            . "password TEXT"
            . ");";
        return array($pdo->query($sql), $table_name);
    }
?>