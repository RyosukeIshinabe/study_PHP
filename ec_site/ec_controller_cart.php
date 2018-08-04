<?php

$cartData = array();

session_start();
// セッション変数からuser_id取得
if ( isset($_SESSION['user_id']) === TRUE ) {
   $user_id = $_SESSION['user_id'];
} else {
   // 非ログインの場合、ログインページへリダイレクト
   header('Location: ./ec_controller_login.php');
   exit;
}
 
// 関数ファイル読み込み
require_once 'ec_model.php';
 
// DB接続
$link = myFunc_connect_db();

// 現在時刻を取得
$nowDate   = date('Y-m-d H:i:s');

// カートデータを取得
$cartData = myFunc_get_cart($link, $user_id, $nowDate);

// 何らかのポストがあった場合
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    
    // カート内のアイテムを数量変更
    if ( $_POST['change_cart_kind'] === 'change_quantity' ) {
        $quantity = myFunc_check_quantity();
        $target_item_id = $_POST['target_item_id'];
        
        // トランザクション開始
        mysqli_autocommit($link, false);
        
        $checkRequestQuantityData = myFunc_check_request_quantity($link, $target_item_id, $quantity);
        
        // 要求した$quantityが在庫数より多かったらエラーを格納
        if ( $checkRequestQuantityData[0]['stock'] < $quantity ) {
            $errMsg['stock_error'] = '申し訳ございません。在庫が不足しています。【' . $checkRequestQuantityData[0]['item_name'] . '】 残り数量：' . $checkRequestQuantityData[0]['stock'];
        }
        
        // エラーメッセージが空なら実行
        if ( empty($errMsg) === true ) {
            myFunc_change_quantity($link, $user_id, $nowDate, $quantity);
        
        // エラーメッセージに何か入っていればロールバック
        } else {
            mysqli_rollback($link);
        }
        
        // ここまででdieしてなければトランザクションをコミット
        mysqli_commit($link);
    
    // カート内のアイテムを消去
    } else if ( $_POST['change_cart_kind'] === 'delete' ) {
        myFunc_delete_item($link, $user_id);
    }
}

// DB切断
myFunc_close_db($link);

// 特殊文字をHTMLエンティティに変換
if ( empty($cartData) === false ) {
    $cartData = myFunc_entity_array($cartData);
}
 
// ビューファイル読み込み
include_once 'ec_view_cart.php';