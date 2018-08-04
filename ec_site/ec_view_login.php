<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ECサイト ログイン画面</title>
    <link rel="stylesheet" href="ec.css">
</head>
<body>
    <h1>新規登録・ログイン画面</h1>

        <h2>ログインはこちら</h2>
<?php   if ( empty($loginData['error']) === false ) {
    ?>
            <div class="alart">
<?php           print $loginData['error'];
    ?>
            </div>
<?php   }
    ?>
        <div class="login">
            <form name="login" method="POST">
            ログインID：<input type="text" maxlength="20" name="login_id" value="<?php if ( isset($login_id) === true ) { print $login_id; } ?>"><br>
            パスワード：<input type="password" maxlength="20" name="login_password" value=""><br>
            <input type="checkbox" name="cookie_check" value="on" checked="checked">ログインIDを記憶しておく<br>
            <input type="hidden" name="submit_type" value="login">
            <input type="submit" name="submit" value="ログイン">
            </form>
        </div>

        <h2>新規登録はこちら</h2>
        <div class="registration">
            <form name="registration" method="POST">
            ユーザー名：<input type="text" maxlength="20" name="user_name" value=""> 20文字以内<br>
            ログインID：<input type="text" maxlength="20" name="login_id" value=""> 半角英数字 6文字以上20文字以内<br>
            パスワード：<input type="password" maxlength="20" name="login_password" value=""> 半角英数字 6文字以上20文字以内<br>
            パスワード：<input type="password" maxlength="20" name="login_password_confirm" value="">（再確認用）半角英数字 6文字以上20文字以内<br>
            <input type="hidden" name="submit_type" value="registration">
            <input type="submit" name="submit" value="登録">
            </form>
        </div>
</body>
</html>
