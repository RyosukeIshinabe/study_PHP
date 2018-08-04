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

        <h2>新規商品追加</h2>
        <div class="add">
        <form name="add_item" method="POST" enctype="multipart/form-data">
            <label for="item_name">商品名：</label><input type="text" maxlength="60" name="item_name" value="">　60文字以下<br>
            <label for="price">価格：</label><input type="number" min="1" max="999999" name="price" value="">　1〜999,999の自然数<br>
            <label for="stock">在庫：</label><input type="number" min="0" max="99999" name="stock" value="">　0〜99,999の自然数<br>
            カテゴリー：<select name="category">
                <option value="0">選択してください</option>
<?php           if ( isset($categoryData) === true ) {
                    foreach ($categoryData as $value) {
    ?>
                <option value="<?php print $value['category_id']; ?>"><?php print $value['category_name']; ?></option>
<?php
                    }
                }
    ?>
            </select><br>
            ジェンダー：<select name="gender">
                <option value="0">選択してください</option>
<?php           if ( isset($genderData) === true ) {
                    foreach ($genderData as $value) {
    ?>
                <option value="<?php print $value['gender_id']; ?>"><?php print $value['gender_name']; ?></option>
<?php
                    }
                }
    ?>
            </select><br>
            公開：<select name="publish_status">
                <option value="0">非公開</option>
                <option value="1">公開</option>
            </select><br>
            <label for="img">画像：</label><input type="file" name="img"><br>
            <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
            <input type="hidden" name="sql_kind" value="insert">
            <input type="submit" name="add" value="追加">
        </form>
        </div><br>

        <h2>商品一覧</h2>
        <div class="list">
        <table>
            <tr>
                <th>ID</th>
                <th>商品名</th>
                <th>カテゴリ</th>
                <th>性別</th>
                <th>価格</th>
                <th>画像</th>
                <th>公開</th>
                <th>作成日時</th>
                <th>更新日時</th>
                <th>在庫</th>
                <th>削除</th>
            </tr>
<?php           if ( isset($itemData) === true ) {
                    foreach ($itemData as $value) {
    ?>
            <tr <?php if ( $value['publish_status'] === '0' ) { print 'class="hidden"'; } ?> >
                <td><?php print $value['item_id']; ?></td>
                <td><?php print $value['item_name']; ?></td>
                <td><?php print $value['category_name']; ?></td>
                <td><?php print $value['gender_name']; ?></td>
                <td><form name="price" method="post">
                    <input type="number" min="1" max="999999" size="6" name="price" value="<?php print $value['price']; ?>"><br>
                    <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                    <input type="hidden" name="sql_kind" value="update_price">
                    <input type="submit" name="price_submit" value="更新">
                    </form></td>
                <td><img src="<?php print $value['img']; ?>" width="60"></td>
                <td><form name="publish_status" method="post">
                    <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                    <input type="hidden" name="publish_status" value="<?php if ( $value['publish_status'] === '0' ) { print '1'; } else { print '0'; } ?>">
                    <input type="hidden" name="sql_kind" value="update_publish_status">
                    <input type="submit" name="status_submit" value="<?php if ( $value['publish_status'] === '0' ) { print '公開する'; } else { print '非公開にする'; } ?>">
                    </form></td>
                <td><?php print $value['created_date']; ?></td>
                <td><?php print $value['update_date']; ?></td>
                <td><form name="stock" method="post">
                    <input type="number" min="0" max="99999" size="5" name="stock" value="<?php print $value['stock']; ?>"><br>
                    <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                    <input type="hidden" name="sql_kind" value="update_stock">
                    <input type="submit" name="stock_submit" value="更新">
                    </form></td>
                <td><form name="delete" method="post">
                    <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                    <input type="hidden" name="sql_kind" value="delete">
                    <input type="hidden" name="img_url" value="<?php print $value['img']; ?>">
                    <input type="submit" name="delete_submit" value="削除">
                    </form></td>
            </tr>
<?php               }
                }
    ?>
        </table>
        </div>
</body>
</html>
