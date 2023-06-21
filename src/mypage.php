<?php
    session_start();
    include "lib/basic.php";

    $pdo = db_connection();

    $output = "";
    $output = $_SESSION["user_name"];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>マイページ</title>
</head>
<body>
    <?php echo $output ?>
</body>
</html>