<?php
    session_start();
    include "lib/basic.php";
    include "lib/class.php";

    $pdo = db_connection();

    $output = "";
    $account_id = "";
    $user_name = "";
    if (isset($_SESSION["user_name"])) {
        // ログイン状態
        $account_id = $_SESSION["account_id"];
        $user_name = $_SESSION["user_name"];
        if (isset($_SESSION["totalpost"])) {
            $totalpost = unserialize($_SESSION["totalpost"]); // 前回の値を取得
        } else {
            $maintable_name = "maincontents_".$account_id;
            $totalpost = new TotalPosting($maintable_name);
        }
        if (isset($_POST["cancel"])) {
            unset($_SESSION["totalpost"]);
            header("Location: ./mypage.php");
            exit();
        } elseif (isset($_POST["post"])) {
            $sql = "create table if not exists ".$totalpost->maintable_name
                ." ("
                . "maincontents_id INT AUTO_INCREMENT PRIMARY KEY,"
                . "title TEXT,"
                . "contents TEXT,"
                . "date datetime"
                . ");";
            $pdo->query($sql);
            $totalpost -> postAll($pdo);
            unset($_SESSION["totalpost"]);
            header("Location: ./mypage.php");
            exit();
        }
        if (isset($_REQUEST["POST_TOKEN"]) && $_REQUEST["POST_TOKEN"] === $_SESSION["POST_TOKEN"]) {
            if (isset($_POST["confirm"])) {
                if (!$totalpost->set_maintitle) {
                    $totalpost -> inputMain($pdo, $_POST["title"], $_POST["contents"]);
                } else {
                    $totalpost -> inputSub($pdo, $_POST["subtitle"], $_POST["contents"]);
                }
            }
        }
        $_SESSION["totalpost"] = serialize($totalpost);
        $_SESSION["POST_TOKEN"] = uniqid();
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
    <title>投稿内容の編集</title>
</head>
<body>
    <?php
        echo $output;
        echo $user_name."\n";
    ?>
    <form action="" method="post">
        <input type="text" name=<?php echo !$totalpost->set_maintitle ? "title" : "subtitle" ?> placeholder=<?php echo !$totalpost->set_maintitle ? "タイトル" : "サブタイトル" ?>>
        <br>
        <textarea name="contents" rows="5" cols="30" wrap="soft"></textarea>
        <input type="hidden" name="POST_TOKEN" value="<?php echo $_SESSION["POST_TOKEN"]; ?>"/>
        <br>
        <input type="submit" name="confirm" value="確認">
    </form>
    <?php
        if ($totalpost->set_maintitle) {
    ?>
    <form action="" method="post">
        <input type="submit" name="post" value="投稿">
    </form>
    <?php
        }
    ?>
    <form action="" method="post">
        <input type="submit" name="cancel" value="キャンセル">
    </form>
    <?php $totalpost -> displayAll(); ?>
</body>
</html>