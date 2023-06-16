<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>環境構築</title>
</head>
<body>
    <?php
        $dsn = 'mysql:dbname=test;host=db';
        $user = 'root';
        $password = 'pass';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        phpinfo();
    ?>
</body>
</html>