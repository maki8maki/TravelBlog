<?php
    session_start();
    include "lib/basic.php";

    $pdo = db_connection();

    // アカウントテーブルの作成
    [$stmt, $table_name] = create_account_table($pdo);

    $user_name = "";
    $output = "";
    $has_created  = FALSE;
    if (isset($_REQUEST["POST_TOKEN"]) && $_REQUEST["POST_TOKEN"] === $_SESSION["POST_TOKEN"]) {
        if (isset($_POST['registration'])) {
            if(!empty($_POST["user_name"]) && trim($_POST["user_name"]) != "" && !empty($_POST["password"]) && trim($_POST["password"]) != "") {
                // ユーザー名とパスワードの取得，テーブル内の検索
                $user_name = $_POST["user_name"];
                $password = $_POST["password"];
                $sql = $pdo -> prepare("select * from ".$table_name." where user_name=:user_name");
                $sql -> bindParam(':user_name', $user_name, PDO::PARAM_STR);
                $sql -> execute();
                if ($results = $sql -> fetchAll()) {
                    // 同じユーザー名が既に存在する
                    $output .=  "既に存在するユーザー名です。ユーザー名を変更して下さい。<br>\n\t";
                } else {
                    // 同じユーザー名が存在しないならアカウントを登録
                    $sql = $pdo -> prepare("insert into ".$table_name."(user_name, password) values (:user_name, :password)");
                    $sql -> bindParam(':user_name', $user_name, PDO::PARAM_STR);
                    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                    $sql -> execute();
                    // 投稿を保存するテーブルを作成
                    $sql = $pdo -> prepare("select account_id from ".$table_name." where user_name=:user_name limit 1");
                    $sql -> bindParam(':user_name', $user_name, PDO::PARAM_STR);
                    $sql -> execute();
                    $account_id = ($sql -> fetchAll())[0]["account_id"];
                    $maintable_name = "maincontents_".$account_id;
                    $sql = "create table if not exists ".$maintable_name
                        ." ("
                        . "maincontents_id INT AUTO_INCREMENT PRIMARY KEY,"
                        . "title TEXT,"
                        . "contents TEXT,"
                        . "date datetime"
                        . ");";
                    $pdo->query($sql);
                    $has_created  = TRUE;
                    $output .=  "アカウントの登録を完了しました。<br>\n\t";
                    $_SESSION["account_id"] = $account_id;
                    $_SESSION["user_name"] = $user_name;
                }
            }
        }
    }
    $_SESSION["POST_TOKEN"] = uniqid();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アカウント新規登録</title>
</head>
<body>
    <?php
        echo $output;
        if (!$has_created) {
    ?>
    <form action="" method="post">
        <input type="text" name="user_name" placeholder="ユーザー名" value=<?= $user_name ?>>
        <input type="password" name="password" placeholder="パスワード">
        <input type="hidden" name="POST_TOKEN" value="<?php echo $_SESSION["POST_TOKEN"]; ?>"/>
        <input type="submit" name="registration" value="登録">
    </form>
    <?php
        } else {
    ?>
    <form action="./mypage.php" method="post">
        <input type="submit" name="transition" value="マイページへ移動">
    </form>
    <?php
        }
    ?>
</body>
</html>