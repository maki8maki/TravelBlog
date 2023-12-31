<?php
    session_start();
    include "lib/basic.php";

    $pdo = db_connection();

    $output = "";
    $account_id = "";
    $user_name = "";
    if (isset($_POST["logout"])) {
        // ログアウト処理
        unset($_SESSION["account_id"]);
        unset($_SESSION["user_name"]);
        $output .= '<a href="./login.php">ログインページ</a>に戻る'."\n\t";
    } elseif (isset($_SESSION["user_name"])) {
        // ログイン状態
        $account_id = $_SESSION["account_id"];
        $user_name = $_SESSION["user_name"];
    } else {
        // ログアウト状態
        header("Location: ./login.php");
        exit();
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
    <form action="./edit.php" method="post" style='display: inline'>
        <input type="submit" name="edit_mypage" value="投稿">
    </form>
    <form action="./view.php" method="post" style='display: inline'>
        <input type="submit" name="view" value="閲覧ページ">
    </form>
    <form action="" method="post" style='display: inline'>
        <input type="submit" name="logout" value="ログアウト">
    </form>
    <?php
        }
        $maintable_name = "maincontents_".$account_id;
        $sql = $pdo -> query("show tables like '".$maintable_name."'");
        if ($sql->rowCount() == 0) {
            exit();
        } else {
            $sql = $pdo -> query("select * from ".$maintable_name);
            $results = $sql -> fetchAll();
            foreach ($results as $result) {
                $maincontents_id = $result["maincontents_id"];
                echo "<h1>".$result["title"]."</h1>投稿日：".$result["date"]."<br>";
                echo $result["contents"]."<br>";
                if(!empty($result["img_name"])) {
                    echo "<img src='imgs/".$result["img_name"]."' style='width: 250px;'><br>";
                }
                $subtable_name = "subcontents_".$maincontents_id;
                $sql = $pdo -> query("select * from ".$subtable_name);
                $subresults = $sql -> fetchAll();
                foreach ($subresults as $subresult) {
                    echo "<h2>".$subresult["title"]."</h2><br>";
                    echo $subresult["contents"]."<br>";
                    if(!empty($subresult["img_name"])) {
                        echo "<img src='imgs/".$subresult["img_name"]."' style='width: 250px;'><br>";
                    }
                }
                echo "<hr>";
            }
        }
    ?>
</body>
</html>