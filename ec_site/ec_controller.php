<?php
 
// 関数ファイル読み込み
require_once 'ec_model.php';

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

// 自分の情報を取得
$myUserData = myFunc_get_my_user($link, $user_id);

// 商品の一覧を取得
$itemData = myFunc_get_item($link);

// 現在時刻を取得
$nowDate   = date('Y-m-d H:i:s');

// カテゴリーとジェンダーの情報を取得
$categoryData = myFunc_get_category($link);
$genderData = myFunc_get_gender($link);

// 何らかのポストがあった場合
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    
    if ( $_POST['sql_kind'] === 'insert' ) {
        $item_name = myFunc_check_item_name();
        $price = myFunc_check_price();
        $stock = myFunc_check_stock();
        $category = myFunc_check_category();
        $gender = myFunc_check_gender();
        $publish_status = myFunc_check_publish_status();
        $filePath = myFunc_check_img();
        
        myFunc_insert_item($link, $item_name, $price, $stock, $category, $gender, $publish_status, $filePath, $nowDate);
    
    } else if ( $_POST['sql_kind'] === 'update_price' ) {
        $price = myFunc_check_price();
        myFunc_update_price($link, $price, $nowDate);
    
    } else if ( $_POST['sql_kind'] === 'update_publish_status' ) {
        $publish_status = myFunc_check_publish_status();
        myFunc_update_publish_status($link, $publish_status, $nowDate);
    
    } else if ( $_POST['sql_kind'] === 'update_stock' ) {
        $stock = myFunc_check_stock();
        myFunc_update_stock($link, $stock, $nowDate);
    
    } else if ( $_POST['sql_kind'] === 'delete' ) {
        $item_id = $_POST['item_id'];
        $img_url = $_POST['img_url'];
        myFunc_remove_item($link, $item_id, $img_url);
    }
}

// DB切断
myFunc_close_db($link);

// 特殊文字をHTMLエンティティに変換
$itemData = myFunc_entity_array($itemData);
$myUserData = myFunc_entity_array($myUserData);
 
// ビューファイル読み込み
include_once 'ec_view.php';