<?php
    session_start();
    include "lib/basic.php";

    $pdo = db_connection();

    // アカウントテーブルの作成
    [$stmt, $table_name] = create_account_table($pdo);

    $user_name = "";
    $output = "";
    if (isset($_REQUEST["POST_TOKEN"]) && $_REQUEST["POST_TOKEN"] === $_SESSION["POST_TOKEN"]) {
        if (isset($_POST['login'])) {
            if(!empty($_POST["user_name"]) && trim($_POST["user_name"]) != "" && !empty($_POST["password"]) && trim($_POST["password"]) != "") {
                // ユーザー名とパスワードの取得，テーブル内の検索
                $user_name = $_POST["user_name"];
                $password = $_POST["password"];
                $sql = $pdo -> prepare("select * from ".$table_name." where user_name=:user_name and password=:password");
                $sql -> bindParam(':user_name', $user_name, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql -> execute();
                if ($results = $sql -> fetchAll()) {
                    // ログイン成功
                    $_SESSION["user_name"] = $user_name;
                    header("Location: ./mypage.php");
                    exit();
                } else {
                    // ログイン失敗
                    $output .=  "ログインに失敗しました。ユーザー名またはパスワードが異なります。\n\t";
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
    <title>ログインページ</title>
</head>
<body>
    <?php echo $output; ?>

    <form action="" method="post">
        <input type="text" name="user_name" placeholder="ユーザー名" value=<?= $user_name ?>>
        <input type="password" name="password" placeholder="パスワード">
        <input type="hidden" name="POST_TOKEN" value="<?php echo $_SESSION["POST_TOKEN"]; ?>"/>
        <input type="submit" name="login" value="ログイン">
    </form>
    <form action="./registration.php" method="post">
        <input type="hidden" name="POST_TOKEN" value="<?php echo $_SESSION["POST_TOKEN"]; ?>"/>
        <input type="submit" name="registration" value="新規登録">
    </form>
</body>
</html>