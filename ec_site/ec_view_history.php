<?php
// 直アクセスした人はログインページに遷移
if ( isset($_SESSION['user_id']) !== TRUE ) {
   header('Location: ./ec_controller_login.php');
   exit;
}
?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ECサイト 管理画面</title>
    <link rel="stylesheet" href="ec.css">
</head>
<body>
    <h1>管理画面</h1>
<?php if ( isset($myUserData) === true ) { ?>
    <p>ようこそ！<?php print $myUserData[0]['user_name'] ?>さん！</p>
<?php } ?>

        <h2>管理者用メニュー</h2>
        <div class="admin_menu">
            <a class="nemu" href="./ec_controller_user.php">ユーザー管理ページ</a> /
            <a class="nemu" href="./ec_controller_history.php">購入履歴管理ページ</a> /
            <a class="nemu" href="./ec_controller.php">商品管理ページ</a> /
            <a class="nemu" href="./ec_controller_shop.php">ショップページ</a> /
            <a class="nemu" href="./ec_controller_logout.php">ログアウト</a><br>
        </div>

        <h2>履歴一覧</h2>
        <table>
            <tr>
                <th>履歴ID</th>
                <th>顧客ID</th>
                <th>アイテムID</th>
                <th>数量</th>
                <th>購入日時</th>
            </tr>
<?php           if ( isset($historyData) === true ) {
                    foreach ($historyData as $value) {
    ?>
            <tr>
                <td><?php print htmlspecialchars($value['history_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php print htmlspecialchars($value['user_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php print htmlspecialchars($value['item_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php print htmlspecialchars($value['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php print htmlspecialchars($value['buy_date'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
<?php               }
                }
    ?>
        </table>
        </form>
    </div>
</body>
</html>
