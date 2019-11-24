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
      if($result['flg'] !== '1'){
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
        $err = 'あなた方チーム'.$result['team'].'は<br>'.'クイズ'.$result['quiz_id'].'に'.$result['bet'].'bet済みです';
      }
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
 <!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>ベット</title>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<style>
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
  margin-bottom: 5px;

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
  <h1 class="text-center pt-5"><?="チーム".$team['id']?><br>賭けてください<br></h1>
  <form action="" method="post">
    <div class="mt-5 pt-3 box-container">
      <p class="text-center lead">現在の状態</p><?=(isset($err) ? $err : "" )?>
      <div class="bg-info">
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
    <div class="pt-5 text-center">

        <label for="A">A</label>
        <input type="radio" name="answer" id="A" value="A">
        <label for="B">B</label>
        <input type="radio" name="answer" value="B">
        <label for="C">C</label>
        <input type="radio" name="answer" value="C">
        <label for="D">D</label>
        <input type="radio" name="answer" value="D">
        <select name="bet">
          <?= $option ?>
        </select><br>
        <input type="submit" class="btn-primary btn-lg mt-3 pr-3" name="submit" onclick="return confirm('ファイナルアンサー？');"></input>

    </div>
  </div>
  </form>
</div>
</body>
</head>
</html>
