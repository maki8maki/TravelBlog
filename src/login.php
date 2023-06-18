<?php
    session_start();
    include "basic.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログインページ</title>
</head>
<body>
    <?php
        $pdo = db_connection();

        // テーブルの作成
        [$stmt, $table_name] = create_account_table($pdo);

        $user_id = "";
        if (isset($_REQUEST["POST_TOKEN"]) && $_REQUEST["POST_TOKEN"] === $_SESSION["POST_TOKEN"]) {
            if (isset($_POST['login'])) {
                if(!empty($_POST["user_id"]) && trim($_POST["user_id"]) != "" && !empty($_POST["password"]) && trim($_POST["password"]) != "") {
                    // ユーザーIDとパスワードの取得，テーブル内の検索
                    $user_id = $_POST["user_id"];
                    $password = $_POST["password"];
                    $sql = $pdo -> prepare("select * from ".$table_name." where user_id=:user_id and password=:password");
                    $sql -> bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                    $sql -> execute();
                    if ($results = $sql -> fetchAll()) {
                        // ログイン成功
                    } else {
                        // ログイン失敗
                        echo "ログインに失敗しました。ユーザーIDまたはパスワードが異なります。";
                    }
                }
            }
        }
        $_SESSION["POST_TOKEN"] = uniqid();
    ?>
    <form action="" method="post">
        <input type="text" name="user_id" placeholder="ユーザーID" value=<?= $user_id ?>>
        <input type="password" name="password" placeholder="パスワード">
        <input type="hidden" name="POST_TOKEN" value="<?php echo $_SESSION["POST_TOKEN"]; ?>"/>
        <input type="submit" name="login" value="ログイン">
    </form>
</body>
</html>