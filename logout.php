<?php

require('config.php');
require('func.php');

session_start();

//企画中にログアウトするキチガイはいないと思うけど自分用

$pdo = pdo();

if(!empty($_SESSION['id'])){
  echo 'Logoutしました。';
}

$sql = 'UPDATE teams SET flg = 0 WHERE login_id = '.'\''.$_SESSION['id'].'\'';
$pdo->exec($sql);

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header('Location:login.php');
// echo "<a href='./login.php'>ログインはここ</a>";
?>
