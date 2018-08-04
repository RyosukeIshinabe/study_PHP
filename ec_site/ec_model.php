<?php
// 管理画面

// グローバル変数定義
define('DB_HOST',   '***'); // データベースのホスト名又はIPアドレス
define('DB_USER',   '***');  // MySQLのユーザ名
define('DB_PASSWD', '***');    // MySQLのパスワード
define('DB_NAME',   '***');    // データベース名
define('HTML_CHARACTER_SET', 'UTF-8');  // HTML文字エンコーディング
define('DB_CHARACTER_SET',   'UTF8');   // DB文字エンコーディング

$sccsMsg   = '';      // 成功時のメッセージ
$errMsg    = array(); // エラーメッセージ
$itemId    = 0; // 操作するアイテムを指定する用

// 新商品追加用
$item_name  = '';
$category_id = 0;
$gender_id = 0;
$price = 0;
$img = '';
$publish_stts = 0;
$created_date = '';
$stock = 0;

// 情報更新用
$new_item_name  = '';
$new_category_id = 0;
$new_gender_id = 0;
$new_price = 0;
$new_img = '';
$new_publish_stts = 0;
$update_date = '';
$new_stock = 0;


// データベース接続
function myFunc_connect_db() {
    if ( !$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME) ) {
        die('データベースの接続に失敗しました。');

    } else {
        mysqli_set_charset($link, DB_CHARACTER_SET);
        return $link;
    }
}

// データベース切断
function myFunc_close_db($link) {
    mysqli_close($link);
}

// 商品一覧を取得
function myFunc_get_item($link) {
    $itemData = array();
    $referItemQuery = '
        SELECT
            ec_item_table.item_id,
            ec_item_table.item_name,
            ec_item_table.category_id,
            ec_category_table.category_name,
            ec_item_table.gender_id,
            ec_gender_table.gender_name,
            ec_item_table.price,
            ec_item_table.img,
            ec_item_table.publish_status,
            ec_item_table.created_date,
            ec_item_table.update_date,
            ec_item_table.stock
        FROM ec_item_table
        JOIN ec_category_table
          ON ec_item_table.category_id = ec_category_table.category_id
        JOIN ec_gender_table
          ON ec_item_table.gender_id = ec_gender_table.gender_id
        ORDER BY ec_item_table.item_id';
    $referItemResult = mysqli_query($link, $referItemQuery);

    while ( $itemRow = mysqli_fetch_array($referItemResult) ) {
        $itemData[] = $itemRow;
    }

    if ( $itemData === 0 ) {
        die('商品データの参照に失敗しました。');
    }

    mysqli_free_result($referItemResult);
    return $itemData;
}

// カテゴリー一覧を取得
function myFunc_get_category($link) {
    $categoryData = array();
    $referCategoryQuery = 'SELECT category_id, category_name FROM ec_category_table';
    $referCategoryResult = mysqli_query($link, $referCategoryQuery);

    while ( $categoryRow = mysqli_fetch_array($referCategoryResult) ) {
        $categoryData[] = $categoryRow;
    }

    if ( $categoryData === 0 ) {
        die('カテゴリーデータの参照に失敗しました。');
    }

    mysqli_free_result($referCategoryResult);
    return $categoryData;
}

// ジェンダー一覧を取得
function myFunc_get_gender($link) {
    $genderData = array();
    $referGenderQuery = 'SELECT gender_id, gender_name FROM ec_gender_table';
    $referGenderResult = mysqli_query($link, $referGenderQuery);

    while ( $genderRow = mysqli_fetch_array($referGenderResult) ) {
        $genderData[] = $genderRow;
    }

    if ( $genderData === 0 ) {
        die('ジェンダーデータの参照に失敗しました。');
    }

    mysqli_free_result($referGenderResult);
    return $genderData;
}

// ユーザー一覧を取得
function myFunc_get_user($link) {
    $userData = array();
    $referUserQuery = 'SELECT user_id, user_name, login_id, login_password, email_address, address, created_date, update_date FROM ec_user_table';
    $referUserResult = mysqli_query($link, $referUserQuery);

    while ( $userRow = mysqli_fetch_array($referUserResult) ) {
        $userData[] = $userRow;
    }

    if ( $userData === 0 ) {
        die('ユーザーデータの参照に失敗しました。');
    }

    mysqli_free_result($referUserResult);
    return $userData;
}

// 自分のユーザー情報のみを取得
function myFunc_get_my_user($link, $user_id) {
    $myUserData = array();
    $referMyUserQuery = 'SELECT user_id, user_name, login_id, login_password, email_address, address, created_date, update_date
                         FROM ec_user_table
                         WHERE user_id = ' . $user_id;
    $referMyUserResult = mysqli_query($link, $referMyUserQuery);

    while ( $myUserRow = mysqli_fetch_array($referMyUserResult) ) {
        $myUserData[] = $myUserRow;
    }

    if ( $myUserData === 0 ) {
        die('ユーザーデータの参照に失敗しました。');
    }

    mysqli_free_result($referMyUserResult);
    return $myUserData;
}


// 購入履歴一覧を取得
function myFunc_get_history($link) {
    $historyData = array();
    $referHistoryQuery = 'SELECT history_id, user_id, item_id, quantity, buy_date FROM ec_history_table';
    $referHistoryResult = mysqli_query($link, $referHistoryQuery);

    while ( $historyRow = mysqli_fetch_array($referHistoryResult) ) {
        $historyData[] = $historyRow;
    }

    if ( $historyData === 0 ) {
        die('購入履歴データの参照に失敗しました。');
    }

    mysqli_free_result($referHistoryResult);
    return $historyData;
}

// 商品名の正規チェック
function myFunc_check_item_name() {
    if ( isset($_POST['item_name']) !== true ) {
        die('商品名が入力されていません。');
    } else {
        $item_name = trim($_POST['item_name']);
        if ( $item_name === '' || ctype_space($item_name) === true || mb_ereg_match("^(\s|　)+$", $item_name) === true ) {
            die('商品名が不正です。');
        } else if ( mb_strlen($item_name) > 60 ) {
            die('商品名は60文字以内で指定して下さい。');
        }
    }
    return $item_name;
}

// 価格の正規チェック
function myFunc_check_price() {
    if ( isset($_POST['price']) !== true ) {
        die('価格が入力されていません。');
    } else {
        $price = trim($_POST['price']);
        if ( $price === '' || ctype_space($price) === true || mb_ereg_match("^(\s|　)+$", $price) === true ) {
            die('価格が不正です。');
        } else if ( ctype_digit($price) !== true || mb_strlen($price) > 6 || is_numeric($price) === false || $price < 1 ) {
            die('価格は1〜999,999の自然数を入力して下さい。');
        }
    }
    return $price;
}

// 在庫の正規チェック
function myFunc_check_stock() {
    if ( isset($_POST['stock']) !== true ) {
        die('在庫が入力されていません。');
    } else {
        $stock = trim($_POST['stock']);
        if ( $stock === '' || ctype_space($stock) === true || mb_ereg_match("^(\s|　)+$", $stock) === true ) {
            die('在庫が不正です。');
        } else if ( ctype_digit($stock) !== true || mb_strlen($stock) > 5 || is_numeric($stock) === false || $stock < 0 ) {
            die('在庫は0〜99,999の自然数を入力して下さい。');
        }
    }
    return $stock;
}

// カテゴリーの正規チェック
function myFunc_check_category() {
    if ( isset($_POST['category']) !== true || $_POST['category'] === '0' ) {
        die('カテゴリーが選択されていません。');
    } else {
        $category = trim($_POST['category']);
        if ( $category === '' || ctype_space($category) === true || mb_ereg_match("^(\s|　)+$", $category) === true || is_numeric($category) !== true || $category < 0 ) {
            die('カテゴリーが不正です。');
        }
    }
    return $category;
}

// ジェンダーの正規チェック
function myFunc_check_gender() {
    if ( isset($_POST['gender']) !== true || $_POST['gender'] === '0' ) {
        die('ジェンダーが選択されていません。');
    } else {
        $gender = trim($_POST['gender']);
        if ( $gender === '' || ctype_space($gender) === true || mb_ereg_match("^(\s|　)+$", $gender) === true || is_numeric($gender) !== true || $gender < 0 ) {
            die('ジェンダーが不正です。');
        }
    }
    return $gender;
}

// 公開ステータスの正規チェック
function myFunc_check_publish_status() {
    if ( isset($_POST['publish_status']) !== true ) {
        die('公開ステータスが入力されていません。');
    } else {
        $publish_status = trim($_POST['publish_status']);
        if ( $publish_status !== '1' && $publish_status !== '0' ) {
            die('公開ステータスが不正です。');
        }
    }
    return $publish_status;
}

// 画像の正規チェック
function myFunc_check_img() {
    if ( isset($_FILES['img']['error']) === false || $_FILES['img']['error'] === 4 ) {
        die('画像が選択されていません。');
    } else if ( $_FILES['img']['error'] !== 2 || $_FILES['img']['size'] > 3000000 ) {
        die('画像が大きすぎます。3MB以下で指定してください。');
    } else if ( $_FILES['img']['error'] !== 0 ) {
        die('画像のエラーが発生しました。コード：' . $_FILES['img']['error']);
    } else {
        $imgPath = $_FILES['img']['tmp_name'];
        $mime = shell_exec('file -bi '.escapeshellcmd($imgPath));
        $mime = trim($mime);
        $mime = preg_replace("/ [^ ]*/", "", $mime);

        if ( $mime === 'image/png;' ) {
            $filePath = './image/' . uniqid() . '.png';
        } else if ( $mime === 'image/jpeg;' || $mime === 'image/jpg;' ) {
            $filePath = './image/' . uniqid() . '.jpg';
        } else {
            die('画像はPNGまたはJPEGでアップロードしてください。');
        }
    }
    return $filePath;
}

// 数量の正規チェック
function myFunc_check_quantity() {
    if ( isset($_POST['quantity']) !== true ) {
        die('数量が入力されていません。');
    } else {
        $quantity = trim($_POST['quantity']);
        if ( $quantity === '' || ctype_space($quantity) === true || mb_ereg_match("^(\s|　)+$", $quantity) === true ) {
            die('数量が不正です。');
        } else if ( ctype_digit($quantity) !== true || $quantity > 99 || is_numeric($quantity) === false || $quantity < 1 ) {
            die('数量は1〜99の自然数を入力して下さい。');
        }
    }
    return $quantity;
}

// 新しい商品をインサート
function myFunc_insert_item($link, $item_name, $price, $stock, $category, $gender, $publish_status, $filePath, $nowDate) {

    // トランザクション開始
    mysqli_autocommit($link, false);

    $insertItemQuery = 'INSERT INTO ec_item_table ( item_name, price, category_id, gender_id, img, created_date, publish_status, stock )
                        VALUES (\'' . $item_name . '\',' . $price . ',' . $category . ',' . $gender . ',\'' . $filePath . '\',\'' . $nowDate . '\',' . $publish_status . ',' . $stock . ')';

    // 画像アップロード
    $imgPath = $_FILES['img']['tmp_name'];
    if ( move_uploaded_file($imgPath, $filePath) === false ) {
        mysqli_rollback($link);
        die('画像のアップロードに失敗しました。');
    }

    // クエリ実行
    if ( mysqli_query($link, $insertItemQuery) === false ) {
        mysqli_rollback($link);
        die('商品の追加に失敗しました。');
    }

    // エラーがなければトランザクションをコミット
    mysqli_commit($link);

    // 成功ページへ遷移
    header('Location:./ec_success.php', true, 303);
}

// 価格をアップデート
function myFunc_update_price($link, $price, $nowDate) {
    $itemId = $_POST['item_id'];
    $newPrice = $_POST['price'];

    $setNewPriceQuery = 'UPDATE ec_item_table
                         SET price = ' . $newPrice . ', update_date = \'' . $nowDate . '\'' .
                        'WHERE item_id = ' . $itemId;

    if ( mysqli_query($link, $setNewPriceQuery) === false ) {
        die('価格の更新に失敗しました。');

    } else {
        header('Location:./ec_success.php', true, 303);
    }
}

// 在庫をアップデート
function myFunc_update_stock($link, $stock, $nowDate) {
    $itemId = $_POST['item_id'];
    $newStock = $_POST['stock'];

    $setNewStockQuery = 'UPDATE ec_item_table
                         SET stock = ' . $newStock . ', update_date = \'' . $nowDate . '\'' .
                        'WHERE item_id = ' . $itemId;

    if ( mysqli_query($link, $setNewStockQuery) === false ) {
        die('在庫の更新に失敗しました。');

    } else {
        header('Location:./ec_success.php', true, 303);
    }
}

// 公開ステータスをアップデート
function myFunc_update_publish_status($link, $publish_status, $nowDate) {
    $itemId = $_POST['item_id'];
    $newPublishStatus = $_POST['publish_status'];

    $setNewPublishStatusQuery = 'UPDATE ec_item_table
                                 SET publish_status = ' . $newPublishStatus . ', update_date = \'' . $nowDate . '\'' .
                                'WHERE item_id = ' . $itemId;

    if ( mysqli_query($link, $setNewPublishStatusQuery) === false ) {
        die('在庫の更新に失敗しました。');

    } else {
        header('Location:./ec_success.php', true, 303);
    }
}

// ユーザー名の正規チェック
function myFunc_check_user_name() {
    if ( isset($_POST['user_name']) !== true ) {
        die('ユーザー名が入力されていません。');
    } else {
        $user_name = trim($_POST['user_name']);
        if ( $user_name === '' || ctype_space($user_name) === true || mb_ereg_match("^(\s|　)+$", $user_name) === true ) {
            die('ユーザー名が不正です。');
        } else if ( mb_strlen($user_name) > 20 ) {
            die('ユーザー名は20文字以内で指定して下さい。');
        }
    }
    return $user_name;
}

// ログインIDの正規チェック
function myFunc_check_login_id() {
    if ( isset($_POST['login_id']) !== true ) {
        die('ログインIDが入力されていません。');
    } else {
        $login_id = trim($_POST['login_id']);
        if ( $login_id === '' || ctype_space($login_id) === true || mb_ereg_match("^(\s|　)+$", $login_id) === true ) {
            die('ログインIDが不正です。');
        } else if ( mb_strlen($login_id) < 6 || mb_strlen($login_id) > 20 || preg_match('/^[a-zA-Z0-9\s]{6,20}$/', $login_id) !== 1 ) {
            die('ログインIDは半角英数字6文字以上20文字以内で指定して下さい。');
        }
    }
    return $login_id;
}

// パスワードの正規チェック
function myFunc_check_login_password() {
    if ( isset($_POST['login_password']) !== true || isset($_POST['login_password_confirm']) !== true ) {
        die('パスワードが入力されていません。');
    } else {
        $login_password = trim($_POST['login_password']);
        $login_password_confirm = trim($_POST['login_password_confirm']);
        if ( $login_password === '' || ctype_space($login_password) === true || mb_ereg_match("^(\s|　)+$", $login_password) === true ) {
            die('パスワードが不正です。');
        } else if ( mb_strlen($login_password) < 6 || mb_strlen($login_password) > 20 || preg_match('/[a-zA-Z0-9]{6,20}/', $login_password) !== 1 ) {
            die('パスワードは半角英数字6文字以上20文字以内で指定して下さい。');
        } else if ( $login_password !== $login_password_confirm ) {
            die('確認用と異なるパスワードが入力されています');
        }
    }
    return $login_password;
}

// ログイン用：ユーザー情報と一致するかチェック
function myFunc_check_login($link) {
    $loginData = array();

    if ( empty($_POST['login_id']) !== false ) {
        $loginData['error'] = 'ログインIDが入力されていません。';

    } else if ( empty($_POST['login_password']) !== false ) {
        $loginData['error'] = 'パスワードが入力されていません。';

    } else {
        $login_id = $_POST['login_id'];
        $login_password = $_POST['login_password'];

        $referLoginQuery = 'SELECT user_id, login_id, login_password, admin_flag
                            FROM ec_user_table
                            WHERE login_id = \'' . $login_id . '\'' .
                            'AND login_password = \'' . $login_password . '\'';

        $referLoginResult = mysqli_query($link, $referLoginQuery);
        while ( $loginRow = mysqli_fetch_assoc($referLoginResult) ) {
            $loginData[] = $loginRow;
        }

        if ( count($loginData) !== 1 ) {
            $loginData['error'] = 'ユーザーIDもしくはパスワードが一致しません。';
        }

        mysqli_free_result($referLoginResult);
    }

    return $loginData;
}

// 新規登録用：ログインIDがすでにあるかどうか確認
function myFunc_check_duplication_login_id($link, $login_id) {

    $referDuplicationLoginIdQuery = 'SELECT user_id FROM ec_user_table
                                     WHERE login_id = \'' . $login_id . '\'';

    $referDuplicationLoginIdResult = mysqli_query($link, $referDuplicationLoginIdQuery);

    while ( $duplicationLoginIdRow = mysqli_fetch_array($referDuplicationLoginIdResult) ) {
        $duplicationLoginIdData[] = $duplicationLoginIdRow;
    }

    if ( empty($duplicationLoginIdData) === false ) {
        die('すでに存在するIDです。別のIDを指定してください。');
    }

    mysqli_free_result($referDuplicationLoginIdResult);
}



// 新規登録用：ユーザー情報をinsert
function myFunc_insert_user($link, $user_name, $login_id, $login_password, $nowDate) {

    $insertUserQuery = 'INSERT INTO ec_user_table ( user_name, login_id, login_password, created_date, admin_flag )
                        VALUES (\'' . $user_name . '\',\'' . $login_id . '\',\'' . $login_password . '\',\'' . $nowDate . '\',' . '0' . ')';

    if ( mysqli_query($link, $insertUserQuery) === false ) {
        die('登録に失敗しました。やり直してください。');
    }

    // セッション用にユーザーIDを取っておく
    $referMyUserIdQuery = 'SELECT user_id FROM ec_user_table
                            WHERE user_name = \'' . $user_name . '\'
                            AND login_id = \'' . $login_id . '\'
                            AND login_password = \'' . $login_password . '\'';
    $referMyUserIdResult = mysqli_query($link, $referMyUserIdQuery);

    while ( $myUserIdRow = mysqli_fetch_array($referMyUserIdResult) ) {
        $myUserIdData[] = $myUserIdRow;
    }

    if ( $myUserIdData === 0 ) {
        die('ユーザーデータの参照に失敗しました。');
    }

    mysqli_free_result($referMyUserIdResult);
    return $myUserIdData;
}

// 在庫を参照する
function myFunc_check_request_quantity($link, $item_id, $requestQuantity) {

    $checkRequestQuantityQuery = 'SELECT stock, item_name
                                  FROM ec_item_table
                                  WHERE item_id = ' . $item_id;

    $checkRequestQuantityResult = mysqli_query($link, $checkRequestQuantityQuery);
    while ( $checkRequestQuantityRow = mysqli_fetch_assoc($checkRequestQuantityResult) ) {
        $checkRequestQuantityData[] = $checkRequestQuantityRow;
    }

    mysqli_free_result($checkRequestQuantityResult);

    $checkRequestQuantityData = myFunc_entity_array($checkRequestQuantityData);

    return $checkRequestQuantityData;
}

// 公開ステータスを参照する
function myFunc_check_request_status($link, $item_id) {

    $checkRequestStatusQuery = 'SELECT publish_status, item_name
                                FROM ec_item_table
                                WHERE item_id = ' . $item_id;

    $checkRequestStatusResult = mysqli_query($link, $checkRequestStatusQuery);
    while ( $checkRequestStatusRow = mysqli_fetch_assoc($checkRequestStatusResult) ) {
        $checkRequestStatusData[] = $checkRequestStatusRow;
    }

    mysqli_free_result($checkRequestStatusResult);

    $checkRequestStatusData = myFunc_entity_array($checkRequestStatusData);

    return $checkRequestStatusData;
}


// カートに商品を追加
function myFunc_add_cart($link, $user_id, $nowDate) {

    if ( isset($_POST['add_cart']) !== true ) {
        die('商品が選択されていません。');
    }

    $add_cart_item_id = $_POST['add_cart'];
    $requestQuantity = 1;
    $searchCartData = array();

    // 在庫チェック
    $checkRequestQuantityData = myFunc_check_request_quantity($link, $add_cart_item_id, $requestQuantity);

    if ( $checkRequestQuantityData[0]['stock'] < $requestQuantity ) {
        $errMsg['stock_error'] = '申し訳ございません。在庫が不足しています。【' . $checkRequestQuantityData[0]['item_name'] . '】 残り数量：' . $checkRequestQuantityData[0]['stock'];
    }

    // 在庫チェックでエラーが返ってなければ
    if ( empty($errMsg) === true ) {

        // すでにカートに追加されている商品かどうか検索
        $searchCartQuery = 'SELECT cart_id, user_id, item_id, quantity
                            FROM ec_cart_table
                            WHERE user_id = ' . $user_id . '
                            AND item_id = ' . $add_cart_item_id;

        $referSearchCartResult = mysqli_query($link, $searchCartQuery);
        while ( $searchCartRow = mysqli_fetch_assoc($referSearchCartResult) ) {
            $searchCartData[] = $searchCartRow;
        }

        mysqli_free_result($referSearchCartResult);

        // すでにカートに追加されている商品なら...
        if ( count($searchCartData) >= 1 ) {

            // すでに追加されている数量に$requestQuantityをプラスしたものを再度在庫チェック
            $newRequestQuantity = $searchCartData[0]['quantity'] + $requestQuantity;

            $checkRequestQuantityData = myFunc_check_request_quantity($link, $add_cart_item_id, $newRequestQuantity);

            if ( $checkRequestQuantityData[0]['stock'] < $newRequestQuantity ) {
                $errMsg['stock_error'] = '申し訳ございません。在庫が不足しています。【' . $checkRequestQuantityData[0]['item_name'] . '】 残り数量：' . $checkRequestQuantityData[0]['stock'];
            }

            // 在庫チェックでエラーが返ってなければ
            if ( empty($errMsg) === true ) {

                // 数量を1プラスする
                $target_cart_id = $searchCartData[0]['cart_id'];

                $incrementQuantityQuery = 'UPDATE ec_cart_table
                                           SET quantity = quantity + 1 ,
                                           update_date = \'' . $nowDate . '\'' .
                                          'WHERE cart_id = ' . $target_cart_id;

                // クエリ実行
                if ( mysqli_query($link, $incrementQuantityQuery) === false ) {
                    die('数量のインクリメントに失敗しました。');
                }

                header('Location:./ec_success.php', true, 303);

            } else {
                return $errMsg;
            }

        // 一致する行がなければ新たにカートIDを発行
        } else if ( count($searchCartData) === 0 ) {

            $addCartQuery = 'INSERT INTO ec_cart_table ( user_id, item_id, quantity, created_date )
                             VALUES (' . $user_id . ',' . $add_cart_item_id . ',' . $requestQuantity . ',\'' . $nowDate . '\')';

            // クエリ実行
            if ( mysqli_query($link, $addCartQuery) === false ) {
                die('カート追加に失敗しました。やり直してください。');
            }

            header('Location:./ec_success.php', true, 303);
        }

    } else {
        return $errMsg;
    }
}

// カートを取得
function myFunc_get_cart($link, $user_id, $nowDate) {

    $getCartQuery = '
        SELECT
            ec_cart_table.cart_id,
            ec_cart_table.user_id,
            ec_cart_table.item_id,
            ec_item_table.item_name,
            ec_item_table.img,
            ec_item_table.price,
            ec_item_table.publish_status,
            ec_item_table.stock,
            ec_cart_table.quantity
        FROM ec_cart_table
        JOIN ec_item_table
        ON ec_cart_table.item_id = ec_item_table.item_id
        WHERE user_id = ' . $user_id . '
        GROUP BY item_id
        ORDER BY cart_id';

    $referCartResult = mysqli_query($link, $getCartQuery);
    while ( $cartRow = mysqli_fetch_assoc($referCartResult) ) {
        $cartData[] = $cartRow;
    }

    mysqli_free_result($referCartResult);

    if ( empty($cartData) === false ) {
        return $cartData;
    }
}

// カート内アイテムを数量変更
function myFunc_change_quantity($link, $user_id, $nowDate, $quantity) {
    $target_cart_id = $_POST['target_cart_id'];

    $setNewQuantityQuery = 'UPDATE ec_cart_table
                            SET quantity = ' . $quantity . ', update_date = \'' . $nowDate . '\'' .
                           'WHERE cart_id = ' . $target_cart_id;

    if ( mysqli_query($link, $setNewQuantityQuery) === false ) {
        die('数量の更新に失敗しました。再度お試しください。');

    } else {
        header('Location:./ec_success.php', true, 303);
    }
}

// カート内アイテムの一部を消去
function myFunc_delete_item($link, $user_id) {
    $target_cart_id = $_POST['target_cart_id'];

    $deleteCartItemQuery = 'DELETE FROM ec_cart_table
                            WHERE cart_id = ' . $target_cart_id;

    if ( mysqli_query($link, $deleteCartItemQuery) === false ) {
        die('アイテムの削除に失敗しました。再度お試しください。');

    } else {
        header('Location:./ec_success.php', true, 303);
    }
}

// 購入履歴を記録
function myFunc_record_history($link, $cartData, $nowDate) {

    foreach ( $cartData as $value ) {

        $recordHistoryQuery = 'INSERT INTO ec_history_table ( user_id, item_id, quantity, buy_date)
                                VALUES ( ' . $value['user_id'] . ',' . $value['item_id'] . ',' . $value['quantity'] . ',\'' . $nowDate . '\')';

        if ( mysqli_query($link, $recordHistoryQuery) === false ) {
            die('購入履歴の記録に失敗しました。再度お試しください。');
        }
    }
}

// 在庫を減らす
function myFunc_decrement_quantity($link, $cartData, $nowDate) {

    foreach ( $cartData as $value ) {

        $setDecrementQuantityQuery = 'UPDATE ec_item_table
                                      SET stock = stock - ' . $value['quantity'] . '
                                      , update_date = \'' . $nowDate . '\'' .
                                     'WHERE item_id = ' . $value['item_id'];

        if ( mysqli_query($link, $setDecrementQuantityQuery) === false ) {
            die('在庫の減算に失敗しました。再度お試しください。');
        }
    }
}

// カート自体を消去
function myFunc_delete_cart($link, $user_id) {

    $deleteCartQuery = 'DELETE FROM ec_cart_table
                        WHERE user_id = ' . $user_id;

    if ( mysqli_query($link, $deleteCartQuery) === false ) {
        die('データの削除に失敗しました。');
    }
}

// 商品自体を削除
function myFunc_remove_item($link, $item_id, $img_url) {

    $removeItemQuery = 'DELETE FROM ec_item_table
                        WHERE item_id = ' . $item_id;

    if ( mysqli_query($link, $removeItemQuery) === false ) {
        die('商品の削除に失敗しました。');

    } else {
        unlink($img_url);
        header('Location:./ec_success.php', true, 303);
    }
}

// HTMLエンティティに変換
function myFunc_entity_str($str) {
    return htmlspecialchars($str, ENT_QUOTES, HTML_CHARACTER_SET);
}

// HTMLエンティティに変換（配列）
function myFunc_entity_array($assoc_array) {
    foreach ($assoc_array as $key => $value) {
        foreach ($value as $keys => $values) {
            $assoc_array[$key][$keys] = myFunc_entity_str($values);
        }
    }
    return $assoc_array;
}
