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
    <title>ECサイト カート画面</title>
    <link rel="stylesheet" href="ec.css">
</head>
<body>
    <h1>マイカート画面</h1>

        <h2>メニュー</h2>
        <div class="nav">
            <a class="nemu" href="ec_controller_shop.php">ショッピングを続ける</a> /
            <a class="nemu" href="./ec_controller_logout.php">ログアウト</a><br>
        </div>

        <h2>カート内アイテム一覧</h2>
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

        <div class="cart">
<?php       if ( empty($cartData) === false ) {
                $row = 1;
                $sum = 0;
    ?>
                <table>
<?php           foreach ($cartData as $value) {
    ?>
                <tr>
<?php               print '<td>' . $row . '</td>'
    ?>
<?php               print '<td>' . $value['item_name'] . '</td>'
    ?>
                    <td><img src="<?php print $value['img']; ?>" width="60"></td>
<?php               print '<td>' . number_format($value['price']) . '円</td>'
    ?>
                    <td><form name="change_quantity" method="POST">
                        <input type="number" name="quantity" max="99" min="1" value="<?php print $value['quantity']; ?>">
                        <input type="hidden" name="change_cart_kind" value="change_quantity">
                        <input type="hidden" name="target_cart_id" value="<?php print $value['cart_id'] ?>">
                        <input type="hidden" name="target_item_id" value="<?php print $value['item_id'] ?>">
                        <input type="submit" value="変更">
                    </form></td>
                    <td><form name="delete" method="POST">
                        <input type="hidden" name="change_cart_kind" value="delete">
                        <input type="hidden" name="target_cart_id" value="<?php print $value['cart_id'] ?>">
                        <input type="submit" value="削除">
                    </form></td>
                </tr>
<?php           $row++;
                $sum = $sum + $value['price'] * $value['quantity'];
                }
   ?>
            </table><br>
            <p class="sum">合計金額：<?php print number_format($sum); ?> 円</p>
            <form name="buy" action="ec_controller_complete.php" method="POST">
                <input type="hidden" name="change_cart_kind" value="buy">
                <input type="hidden" name="target_user_id" value="<?php print $user_id; ?>">
                <input type="submit" value="購入">
            </form><br>
<?php
            } else {
    ?>
            <p>カートに何もはいっていません。</p>
<?php       }
    ?>
        </div>

</body>
</html>
