<?php
    session_start();
    include "lib/basic.php";

    $pdo = db_connection();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>閲覧ページ</title>
</head>
<body>
    <?php if (!isset($_SESSION["user_name"])) { ?>
    <form action="./edit.php" method="post" style='display: inline'>
        <input type="submit" name="login" value="ログイン">
    </form>
    <form action="./registration.php" method="post" style='display: inline'>
        <input type="submit" name="mypage" value="新規登録">
    </form>
    <?php } else { ?>
    <form action="./mypage.php" method="post" style='display: inline'>
        <input type="submit" name="mypage" value="マイページ">
    </form>
    <?php
        }
        [$stmt, $table_name] = create_account_table($pdo);
        $sql = $pdo -> query("select user_name, account_id from ".$table_name);
        $accounts = $sql -> fetchAll();
        foreach ($accounts as $account) {
            $maintable_name = "maincontents_".$account["account_id"];
            $sql = $pdo -> query("select * from ".$maintable_name);
            $results = $sql -> fetchAll();
            foreach ($results as $result) {
                $maincontents_id = $result["maincontents_id"];
                echo "<h1>".$result["title"]."</h1>";
                echo "投稿者：".$account["user_name"]."　投稿日：".$result["date"]."<br>";
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