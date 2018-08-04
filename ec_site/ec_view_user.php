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
    <title>ECサイト ユーザー管理画面</title>
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

        <h2>ユーザー一覧</h2>
        <div class="list">
        <table>
            <tr>
                <th>ID</th>
                <th>ユーザー名</th>
                <th>ログインID</th>
                <th>メールアドレス</th>
                <th>住所</th>
                <th>作成日時</th>
                <th>更新日時</th>
            </tr>
<?php           if ( isset($userData) === true ) {
                    foreach ($userData as $value) {
    ?>
            <tr>
                <td><?php print $value['user_id']; ?></td>
                <td><?php print $value['user_name']; ?></td>
                <td><?php print $value['login_id']; ?></td>
                <td><?php print $value['email_address']; ?></td>
                <td><?php print $value['address']; ?></td>
                <td><?php print $value['created_date']; ?></td>
                <td><?php print $value['update_date']; ?></td>
            </tr>
<?php               }
                }
    ?>
        </table>
        </div>
</body>
</html>
