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
 
// ユーザーの一覧を取得
$userData = myFunc_get_user($link);

// DB切断
myFunc_close_db($link);

// 特殊文字をHTMLエンティティに変換
$userData = myFunc_entity_array($userData);
 
// ビューファイル読み込み
include_once 'ec_view_user.php';