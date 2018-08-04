<?php
 
// 関数ファイル読み込み
require_once 'ec_model.php';

// セッション変数からログイン済みか確認
session_start();

// Cookieからlogin_idを取得
if ( isset($_COOKIE['login_id']) === TRUE ) {
   $login_id = $_COOKIE['login_id'];
} else {
   $login_id = '';
}

// 特殊文字をHTMLエンティティに変換
$login_id = myFunc_entity_str($login_id);

// DB接続
$link = myFunc_connect_db();
 
// ユーザーの一覧を取得
$userData = myFunc_get_user($link);

// 現在時刻を取得
$nowDate   = date('Y-m-d H:i:s');

// 何らかのポストがあった場合
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    
    // ログイン
    if ( $_POST['submit_type'] === 'login' ) {
        $loginData = myFunc_check_login($link);
        
        // errorが格納されてなければセッションにuser_idを保存
        if ( isset($loginData['error']) === false ) {
            $_SESSION['user_id'] = $loginData[0]['user_id'];
            
            // cookie保存のチェックが入ってたらcookieに保存
            if ( $_POST['cookie_check'] === 'on' ) {
                $login_id = $_POST['login_id'];
                setcookie('login_id', $login_id, time() + 60 * 60 * 24 * 7);
                
            // 入ってなかったらクッキーを破棄
            } else {
                setcookie('login_id', '', time() - 3600);
            }
            
            // 管理者権限がついてるアカウントは管理ページへ
            if ( $loginData[0]['admin_flag'] === '1' ) {
                header('Location:./ec_controller.php', true, 303);
                exit;
                
            // 一般ユーザーは商品ページへ
            } else {
                header('Location:./ec_controller_shop.php', true, 303);
                exit;
            }
        }
    
    // 新規登録
    } else if ( $_POST['submit_type'] === 'registration' ) {
        $user_name = myFunc_check_user_name();
        $login_id = myFunc_check_login_id();
        $login_password = myFunc_check_login_password();
        
        myFunc_check_duplication_login_id($link, $login_id);
        
        $myUserIdData = myFunc_insert_user($link, $user_name, $login_id, $login_password, $nowDate);
        
        $_SESSION['user_id'] = $myUserIdData[0]['user_id'];
        
        // 管理画面に遷移
        header('Location:./ec_controller_shop.php', true, 303);
        exit;
    }
}

// DB切断
myFunc_close_db($link);

// 特殊文字をHTMLエンティティに変換
$userData = myFunc_entity_array($userData);
 
// ビューファイル読み込み
include_once 'ec_view_login.php';