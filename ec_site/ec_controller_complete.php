<?php
 
// 関数ファイル読み込み
require_once 'ec_model.php';

$checkRequestQuantityData = array();
$checkRequestStatusData = array();
$errMsg = array();

session_start();
// セッション変数からuser_id取得
if ( isset($_SESSION['user_id']) === TRUE ) {
   $user_id = $_SESSION['user_id'];
} else {
   // 非ログインの場合、ログインページへリダイレクト
   header('Location: ./ec_controller_login.php');
   exit;
}
 
// DB接続
$link = myFunc_connect_db();

// 現在時刻を取得
$nowDate   = date('Y-m-d H:i:s');

// カートのアイテム一覧を取得
$cartData = myFunc_get_cart($link, $user_id, $nowDate);

if ( empty($cartData) === false ) {
    
    // 購入の前に、カート内の全商品に対して在庫チェックとステータスチェック
    foreach ( $cartData as $value ) {
        $checkRequestQuantityData = myFunc_check_request_quantity($link, $value['item_id'], $value['quantity']);
        $checkRequestStatusData = myFunc_check_request_status($link, $value['item_id']);
        
        // 入力されている数量の方が在庫よりも多い場合はエラーを格納
        if ( $checkRequestQuantityData[0]['stock'] < $value['quantity'] ) {
            $errMsg['stock_error'] = '申し訳ございません。在庫が不足しています【' . $checkRequestQuantityData[0]['item_name'] . '】 残り数量：' . $checkRequestQuantityData[0]['stock'];
        }
        
        // 公開ステータスが1以外の場合はエラーを格納
        if ( $checkRequestStatusData[0]['publish_status'] !== '1' ) {
            $errMsg['status_error'] = '申し訳ございません。この商品は現在取り扱っていません。【' . $checkRequestStatusData[0]['item_name'] . '】';
        }
    }
}

if ( empty($errMsg) === true ) {
    
    // トランザクション開始
    mysqli_autocommit($link, false);

    // 購入履歴を記録
    myFunc_record_history($link, $cartData, $nowDate);
    
    // 在庫を減らす
    myFunc_decrement_quantity($link, $cartData, $nowDate);
    
    // 特殊文字をHTMLエンティティに変換
    $cartData = myFunc_entity_array($cartData);
    
}

// ビューファイル読み込み
include_once 'ec_view_complete.php';

if ( empty($errMsg) === true ) {
    
    // カートを消す
    myFunc_delete_cart($link, $user_id);
    
    // ここまででdieしてなければトランザクションをコミット
    mysqli_commit($link);
}

// DB切断
myFunc_close_db($link);