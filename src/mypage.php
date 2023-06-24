<?php
    session_start();
    include "lib/basic.php";

    $pdo = db_connection();

    $output = "";
    $user_name = "";
    if (isset($_POST["logout"])) {
        // ログアウト処理
        unset($_SESSION["user_name"]);
        $output .= '<a href="./login.php">ログインページ</a>に戻る'."\n\t";
    } elseif (isset($_SESSION["user_name"])) {
        // ログイン状態
        $user_name = $_SESSION["user_name"];
    } else {
        // ログアウト状態
        header("Location: ./login.php");
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>マイページ</title>
</head>
<body>
    <?php
        echo $output;
        echo $user_name."\n\t";
        if (isset($_SESSION["user_name"])) {
    ?>
    <form action="" method="post">
        <input type="submit" name="logout" value="ログアウト">
    </form>
    <?php
        }
    ?>
</body>
</html>