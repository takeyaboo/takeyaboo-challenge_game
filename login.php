<?php

//ログインなんてどうでもいいけどとリま管理者はadmin.php 参加者はbet.php

require('config.php');
require('func.php');

// session_start();

if(!empty($_SESSION['id'])){
  header('Location: bet.php');
}

$pdo = pdo();

//QR版ログイン
if(!empty($_GET['id']) && !empty($_GET['pass'])){
    try {
    // $pdo = new PDO(DSN, DB_USER, DB_PASS);
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('select * from teams where login_id = ?');
    $stmt->execute(array($_GET['id']));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EQL;
  }
    // print_r($_GET);
  // echo $_POST['id'];
  // print_r($result);
    if($result['login_id'] === $_GET['id'] && $result['pass'] === $_GET['pass']){
      //多重ログイン防止
    //   if($result['flg'] == 0){

      if(empty($_COOKIE['id'])){
        $token = "トークン";
        $token = hash('sha256', $token);
        setcookie('id', $token, time() + 60 * 60 * 24 * 7);

      }

        if(empty($result['token']) || $result['token'] == $_COOKIE['id']){

        $_SESSION['id'] = $result['login_id'];
        $_SESSION['pass'] = $result['pass'];
        // $_SESSION['auth'] = 1;
        $sql = 'UPDATE teams SET token = '.'\''.$token.'\' ,flg = 1 WHERE login_id = '.'\''.$_SESSION['id'].'\'';
        $pdo->exec($sql);

        if($_SESSION['id'] == 'oreore' && $_SESSION['pass'] == 'takeba'){
          header('Location: admin.php');
          exit;
        }
        header('Location: bet.php');
        exit;
      }else{
        $err = '既にログインされています';
      }
    }else{
      $err = 'ログイン失敗';
    }
}

if(isset($_POST['submit'])){
  try {
    // $pdo = new PDO(DSN, DB_USER, DB_PASS);
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('select * from teams where login_id = ?');
    $stmt->execute(array($_POST['id']));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EQL;
  }

  // echo $_POST['id'];
  // print_r($result);
    if($result['login_id'] === $_POST['id'] && $result['pass'] === $_POST['pass']){
      //多重ログイン防止
      // if($result['flg'] == 0){

      if(empty($_COOKIE['id'])){
        $token = "トークン";
        $token = hash('sha256', $token);
        setcookie('id', $token, time() + 60 * 60 * 24 * 7);
      }

        if(empty($result['token']) || $result['token'] == $_COOKIE['id']){

        $_SESSION['id'] = $result['login_id'];
        $_SESSION['pass'] = $result['pass'];
        // $_SESSION['auth'] = 1;

        $sql = 'UPDATE teams SET token = '.'\''.$token.'\' ,flg = 1 WHERE login_id = '.'\''.$_SESSION['id'].'\'';
        $pdo->exec($sql);

        if($_SESSION['id'] == 'oreore' && $_SESSION['pass'] == 'takeba'){
          header('Location: admin.php');
          exit;
        }
        header('Location: bet.php');
        exit;
      // }else{
      //   $err = '既にログインされています';
      // }
    }else{
      $err = '既にログインされています';
    }
  }else{
    $err ='ログイン失敗';
  }
}

?>
<html lang="ja">
 <head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <!-- Required meta tags -->
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

   <title>ログイン</title>
   <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
   <style>
   html{
       background-color: #56baed;
      }

      body {
       font-family: "Poppins", sans-serif;
       height: 100vh;
      }



      h2 {
       text-align: center;
       font-size: 16px;
       font-weight: 600;
       text-transform: uppercase;
       display:inline-block;
       margin: 40px 8px 10px 8px;
       color: #cccccc;
      }



      /* STRUCTURE */

      .wrapper {
       display: flex;
       align-items: center;
       flex-direction: column;
       justify-content: center;
       width: 100%;
       min-height: 100%;
       padding: 20px;
      }

      #formContent {
       -webkit-border-radius: 10px 10px 10px 10px;
       border-radius: 10px 10px 10px 10px;
       background: #fff;
       padding: 30px;
       width: 90%;
       max-width: 450px;
       position: relative;
       padding: 0px;
       -webkit-box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
       box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
       text-align: center;
      }


      /* TABS */

      /* h2.inactive {
       color: #cccccc;
      }

      h2.active {
       color: #0d0d0d;
       border-bottom: 2px solid #5fbae9;
      } */



      /* FORM TYPOGRAPHY*/

      input[type=button], input[type=submit], input[type=reset]  {
       background-color: #56baed;
       border: none;
       color: white;
       padding: 15px 80px;
       text-align: center;
       text-decoration: none;
       display: inline-block;
       text-transform: uppercase;
       font-size: 13px;
       -webkit-box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
       box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
       -webkit-border-radius: 5px 5px 5px 5px;
       border-radius: 5px 5px 5px 5px;
       margin: 5px 20px 40px 20px;
       -webkit-transition: all 0.3s ease-in-out;
       -moz-transition: all 0.3s ease-in-out;
       -ms-transition: all 0.3s ease-in-out;
       -o-transition: all 0.3s ease-in-out;
       transition: all 0.3s ease-in-out;
      }

      input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover  {
       background-color: #39ace7;
      }

      input[type=button]:active, input[type=submit]:active, input[type=reset]:active  {
       -moz-transform: scale(0.95);
       -webkit-transform: scale(0.95);
       -o-transform: scale(0.95);
       -ms-transform: scale(0.95);
       transform: scale(0.95);
      }

      input[type=text], input[type=password] {
       background-color: #f6f6f6;
       border: none;
       color: #0d0d0d;
       padding: 15px 32px;
       text-align: center;
       text-decoration: none;
       display: inline-block;
       font-size: 16px;
       margin: 5px;
       width: 85%;
       border: 2px solid #f6f6f6;
       -webkit-transition: all 0.5s ease-in-out;
       -moz-transition: all 0.5s ease-in-out;
       -ms-transition: all 0.5s ease-in-out;
       -o-transition: all 0.5s ease-in-out;
       transition: all 0.5s ease-in-out;
       -webkit-border-radius: 5px 5px 5px 5px;
       border-radius: 5px 5px 5px 5px;
      }

      input[type=text]:focus, input[type=password]:focus {
       background-color: #fff;
       border-bottom: 2px solid #5fbae9;
      }

      input[type=text]:placeholder, input[type=password]:placeholder {
       color: #cccccc;
      }



      /* ANIMATIONS */

      /* Simple CSS3 Fade-in-down Animation */
      .fadeInDown {
       -webkit-animation-name: fadeInDown;
       animation-name: fadeInDown;
       -webkit-animation-duration: 1s;
       animation-duration: 1s;
       -webkit-animation-fill-mode: both;
       animation-fill-mode: both;
      }

      @-webkit-keyframes fadeInDown {
       0% {
         opacity: 0;
         -webkit-transform: translate3d(0, -100%, 0);
         transform: translate3d(0, -100%, 0);
       }
       100% {
         opacity: 1;
         -webkit-transform: none;
         transform: none;
       }
      }

      @keyframes fadeInDown {
       0% {
         opacity: 0;
         -webkit-transform: translate3d(0, -100%, 0);
         transform: translate3d(0, -100%, 0);
       }
       100% {
         opacity: 1;
         -webkit-transform: none;
         transform: none;
       }
      }

      /* Simple CSS3 Fade-in Animation */
      @-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
      @-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
      @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

      .fadeIn {
       opacity:0;
       -webkit-animation:fadeIn ease-in 1;
       -moz-animation:fadeIn ease-in 1;
       animation:fadeIn ease-in 1;

       -webkit-animation-fill-mode:forwards;
       -moz-animation-fill-mode:forwards;
       animation-fill-mode:forwards;

       -webkit-animation-duration:1s;
       -moz-animation-duration:1s;
       animation-duration:1s;
      }

      .fadeIn.first {
       -webkit-animation-delay: 0.4s;
       -moz-animation-delay: 0.4s;
       animation-delay: 0.4s;
      }

      .fadeIn.second {
       -webkit-animation-delay: 0.6s;
       -moz-animation-delay: 0.6s;
       animation-delay: 0.6s;
      }

      .fadeIn.third {
       -webkit-animation-delay: 0.8s;
       -moz-animation-delay: 0.8s;
       animation-delay: 0.8s;
      }

      .fadeIn.fourth {
       -webkit-animation-delay: 1s;
       -moz-animation-delay: 1s;
       animation-delay: 1s;
      }

      /* Simple CSS3 Fade-in Animation */
      .underlineHover:after {
       display: block;
       left: 0;
       bottom: -10px;
       width: 0;
       height: 2px;
       background-color: #56baed;
       content: "";
       transition: width 0.2s;
      }

      .underlineHover:hover {
       color: #0d0d0d;
      }

      .underlineHover:hover:after{
       width: 100%;
      }

      /* OTHERS */

      *:focus {
         outline: none;
      }
   </style>
   <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
   <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 </head>
<body>
  <div class="wrapper fadeInDown">
    <div id="formContent">
      <p class="lead <?=(isset($err) ? "alert-danger" : "")?>"><?=(isset($err) ? $err : "ログインしてください")?></p>
        <form action="" method="post">
          <!-- <label for="#id">ID</label><br> -->
          <input type="text" id="id" class="fadeIn second" name="id" value="" placeholder="IDを入力してください">
          <!-- <label for="#pass">PASS</label><br> -->
          <input type="password" id="pass" class="fadeIn third" name="pass" value=""placeholder="PASSを入力してください">
          <input type="submit" class="fadeIn fourth" name="submit" value="企画に参加!!">
        </form>
    </div>
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
