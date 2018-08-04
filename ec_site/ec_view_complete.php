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
    <title>ECサイト 購入結果</title>
    <link rel="stylesheet" href="ec.css">
</head>
<body>
<?php   if ( empty($errMsg) === false ) {
    ?>
        <div class="alart">
<?php       foreach ( $errMsg as $value ) {
                print $value;
            }
    ?>
        </div>
<?php   print nl2br("\n");

        } else {
    ?>
            <h1>購入成功</h1>
            <div class="thanks">ご購入ありがとうございます！</div>

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
        <?php               print '<td>' . number_format($value['quantity']) . '個</td>'
            ?>
                        </tr>
        <?php           $row++;
                        $sum = $sum + $value['price'] * $value['quantity'];
                        }
           ?>
                    </table><br>
                    <p class="sum">合計金額：<?php print number_format($sum); ?> 円</p>
        <?php
                    } else {
            ?>
                    <p>カートに何もはいっていません。</p>
        <?php       }
            ?>
                </div>
<?php
        }
    ?>
        <div class="nav">
            → <a href="ec_controller_shop.php">ショップに戻る</a>
        </div>

</body>
</html>
