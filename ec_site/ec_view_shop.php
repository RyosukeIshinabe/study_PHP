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
    <title>ECサイト ショップ画面</title>
    <link rel="stylesheet" href="ec.css">
</head>
<body>
    <h1>ショップ画面</h1>
<?php if ( isset($myUserData) === true ) { ?>
    <p>ようこそ！<?php print $myUserData[0]['user_name'] ?>さん！</p>
<?php } ?>

        <h2>メニュー</h2>
        <div class="nav">
            <a class="nemu" href="ec_controller_cart.php">カートに移動する</a> /
            <a class="nemu" href="./ec_controller_logout.php">ログアウト</a><br>
        </div>

        <h2>商品一覧</h2>
<?php       if ( empty($errMsg) === false ) {
    ?>
        <div class="alart">
<?php           foreach ( $errMsg as $value ) {
                    print $value;
                }
    ?>
        </div>
<?php       print nl2br("\n");
            }
    ?>
        <div class="shop">
<?php       if ( isset($itemData) === true ) {
    ?>
            <table>
<?php       }
            foreach ($itemData as $value) {
                if ( $value['publish_status'] !== '1' ) { continue; }
    ?>
            <tr>
                <td>
                    <img src="<?php print $value['img']; ?>" width="100"><br>
<?php               print $value['item_name'] . '<br>' .
                          $value['price'] . '円<br>';

                    if ( $value['stock'] <= 0 ) {
    ?>
                        <div class="alart">在庫切れ</div>
<?php               } else {
    ?>
                        <form name="add_cart" method="POST">
                        <input type="hidden" name="add_cart" value="<?php print $value['item_id'] ?>">
                        <input type="submit" value="カートに入れる">
                        </form>
<?php               }
    ?>
                </td>
            </tr>
<?php       }
    ?>
            </table>
        </div><br>

        <div class="nav">
            → <a href="ec_controller_cart.php">カートに移動する</a>
        </div>
</body>
</html>
