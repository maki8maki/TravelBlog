<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>環境構築</title>
</head>
<body>
    <?php
        include "basic.php";
        $pdo = db_connection();

        phpinfo();
    ?>
</body>
</html>