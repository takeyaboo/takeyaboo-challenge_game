<?php

session_start();

require('config.php');
require('func.php');

// echo "<a href='./logout.php'>ログアウトはする意味ないです</a><br />";

if(empty($_SESSION['id'])){
  header('Location:login.php');
}
// print_r($_SESSION);

$pdo = pdo();

if(isset($_POST['submit'])){
  if($_POST["quiz_id"] != ""){
    if(isset($_POST['answer']) && isset($_POST['bet'])){
      //クイズとチームに対応する情報(bet)を取得
      $stmt = $pdo->query( 'select * from bets where quiz_id = '. $_POST["quiz_id"] .' and team ='. $_POST["team_id"]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);


      //フラグが立っていたら挿入できないようにする
      //立ってたらどの問題に対してどのチームがどの答えにいくら賭けたか挿入
      if($result['flg'] != '1'){
        $sql = 'INSERT INTO bets('
            . '  team '
            . ', bet '
            . ', quiz_id '
            . ', answer '
            . ', flg '
          . ' )VALUES( '
            . '  :team '
            . ', :bet '
            . ', :quiz_id '
            . ', :answer '
            . ', 1 '
          . ' ) '
        ;

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':team', $_POST['team_id']);
        $stmt->bindParam(':bet', $_POST['bet']);
        $stmt->bindParam(':quiz_id', $_POST['quiz_id']);
        $stmt->bindParam(':answer', $_POST['answer']);
        $stmt->execute();
        // print_r($result);
        // print_r($_POST);
      }else{
        $err = 'あなた方チーム'.$result['team'].'は'.'クイズ'.$result['quiz_id'].'に'.$result['bet'].'bet済みです!!';
      }
    }else{
      $err = '答えが未選択です!!';
    }
  }
}

// echo $_POST['bet'];

//クイズの状態表示用
$stmt = $pdo->query( 'select * from quiz where flg = 1');
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

//自分のチームの状態表示用
$id = $_SESSION['id'];
$stmt = $pdo->query( 'select * from teams where login_id = \''.$id.'\'');
$team = $stmt->fetch(PDO::FETCH_ASSOC);

// ベット後に一回ログアウトして(しないと思うけど)ログインし直しても「あれ？あれ？ってならないように」ベットした数が表示されるようにわざわざ書いてる
if(isset($quiz) && isset($team)){
  $stmt = $pdo->query( 'select * from bets where team = \''.$team['id'].'\' and quiz_id = \''.$quiz['id'].'\'');
  $bet = $stmt->fetch(PDO::FETCH_ASSOC);
}



//チームの保有ポイントによってベット数を制限する
$option = "";
  for($i = 0; $i <= $team['point']; $i++){
    if($team['point'] <= 10){
      $option .= "<option value=\"".$i."\">".$i."</option>";
    }else{
      if($i <= 10){
        $option .= "<option value=\"".$i."\">".$i."</option>";
      }
    }
  }

 ?>

<html lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <!-- Required meta tags -->
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>ベット</title>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<style>

body{
  background-color: #66CCCC;
}

.container {
  padding-right: 15px;
  padding-left: 15px;
  margin-right: auto;
  margin-left: auto;
}
@media (min-width: 768px) {
  .container {
    width: 750px;
  }
}
@media (min-width: 992px) {
  .container {
    width: 970px;
  }
}
@media (min-width: 1200px) {
  .container {
    width: 1170px;
  }
}

.box-container {
  background: #ddd;
  /* margin-bottom: 5px; */
  margin-bottom: 60px;

}

h1{
  margin-top:10px;
  color: #6cb4e4;
  border: solid 2px #6cb4e4;
  border-radius: 30%;
  background: -webkit-repeating-linear-gradient(-45deg, #f0f8ff, #f0f8ff 3px,#e9f4ff 3px, #e9f4ff 7px);
  background: repeating-linear-gradient(-45deg, #f0f8ff, #f0f8ff 3px,#e9f4ff 3px, #e9f4ff 7px);
}

</style>


</head>
<body>
  <nav class="navbar navbar-expand navbar-dark bg-primary">
    <!-- <a href="" class="navbar-brand"></a> -->
    <ul class="navbar-nav mr-auto">
      <li class="nav-item"><a href="" class="nav-link"><a href="./logout.php" class="text-light">ログアウト</a><li/>
    </ul>
  </nav>
  <div class="container my-0 mx-auto">
    <h1 class="text-center" style="display:none"><?="チーム".$team['id']?><br>賭けてください<br></h1>
    <form action="" method="post">
      <div id="container" class="mt-5 pt-3 box-container" style="display:none">
        <p class="text-center lead">現在の状態</p><div class="text-danger"><?=(isset($err) ? "ERROR:".$err : "" )?></div>
        <div class="bg-info pb-3">
          <?php if(isset($quiz)): ?>
            <p class="text-light mt-2 bg-info"><?= 'クイズ名:'.$quiz['title'] ?></p><hr>
            <p class="text-light mt-4 bg-info"><?= 'ベットした数:'.$bet['bet'] ?></p><hr>
            <p class="text-light mt-4 bg-info"><?= 'Your answer:'.$bet['answer'] ?></p><hr>
            <p class="text-light mt-4 bg-info"><?= '倍率:'.$quiz['rarity'] ?></p><hr>
            <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
          <?php endif ; ?>
          <?php if(isset($team)): ?>
            <p class="text-light mt-4 bg-info"><?= 'あなたのチームのポイント:'.$team['point'] ?></p>
            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
          <?php endif ; ?>
          </div>
      </div>
      <div class="pt-3 text-center">
        <h4 class="alert alert-secondary">答えとベット数を選んで<br>送信してください</h4><br>
        <!-- <div class="text-left w-50 mx-auto"> -->
          <label for="A"><span class="badge badge-secondary" style="font-weight:bold;">答え</span>A</label>
          <input type="radio" name="answer" id="A" value="A">
          <label for="B">B</label>
          <input type="radio" name="answer" value="B">
          <label for="C">C</label>
          <input type="radio" name="answer" value="C">
          <label for="D">D</label>
          <input type="radio" name="answer" value="D">
          <p><span class= "badge badge-secondary" style="font-weight:bold;">ベット数</span>
          <select name="bet">
            <?= $option ?>
          </select><br>
          </p>
        <!-- </div> -->
          <input type="submit" class="btn-primary btn-lg mt-3 pr-5 pl-5" name="submit" onclick="return confirm('※答えとbet数は変更できません!\nファイナルアンサー?');"></input>

      </div>
    </div>
    </form>
  </div>
  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script>
  $(function() {
  //   $('h1').fadeIn(2000, function(){
  //     $(this).addClass('title');
  //     });
  // });
  $('h1').fadeIn(2100);
  $('#container').show(1000)
});
  </script>
</body>
</head>
</html>