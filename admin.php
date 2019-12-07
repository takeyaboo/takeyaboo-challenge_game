<?php

// session_start();

require('config.php');
require('func.php');


if(empty($_SESSION['id'])){
  header('Location:login.php');
}else{
  if($_SESSION['id'] !== 'oreore'){
    header('Location:login.php');

  }
}

$pdo = pdo();

//全クイズの取得　表示用
$stmt = $pdo->query ( 'select * from quiz');
$quizs = $stmt->fetchAll(PDO::FETCH_ASSOC);

//全チーム取得　表示用
//追加ポイント処理のため
$stmt = $pdo->query ( 'select * from teams');
$all_team = $stmt->fetchAll(PDO::FETCH_ASSOC);

//リアルタイムの問題と答えの状態を表示するための処理
$stmt = $pdo->query ( 'select * from quiz where flg = 1' );
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);
// print_r($quiz);


// 全チームがベットしたのを確認するためにどこがベットしたか表示させる処理
// まあどうせsyuukei.phpで全チームがベットしないと集計できないようにしてるけど

$stmt = $pdo->query ( 'select * from bets where flg = 1 order by team asc' );
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($teams);


// for($i = 1; $i <= 16; $i++){
//   foreach ($pdo->query ( 'select * from bets' ) as $val){
//     if($val['team'] == $i){
//       $teams[] = $val['team'];
//       break;
//     }
//   }
// }

// print_r($teams);



echo "<a href='logout.php'>ログアウトはこちら。</a>";

//久々にORANGE RANGE聴くと良さある

if(isset($_POST)){
  //登録ボタン押したら
  if(isset($_POST['regist'])){
      //全クイズのフラグを取得
      $stmt = $pdo->query( 'select flg from quiz' );
      $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

      // 全クイズのフラグをチェックして登録されているクイズがないか確認
      $flg_chk = [];
      for($i = 0; $i < count($result) -1; $i++){
        if($result[$i] != 0){
          array_push($flg_chk, $result[$i]);
        }
      }

      // なかったら登録問題にフラグを立てる
      if(count($flg_chk) == 0){
        $sql = ' UPDATE quiz SET '
                .' flg = 1 '
                .'WHERE id = :id '
        ;

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();


        // $_SESSION['flg'] = 1;
        // $_SESSION['quiz_id'] = $_GET['id'];

      }else{
        echo 'どっかのフラグが解除されてない';
      }
    }

    //正解が登録されたら
    if(isset($_POST['correct'])){
      //問題がちゃんと登録されてることを確認して
      // $stmt = $pdo->query ( 'select * from quiz where flg = 1' );
      // $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

      // if(!empty($_SESSION['flg']) && !empty($_SESSION['quiz_id'])){
      if(!empty($quiz['flg']) && !empty($quiz['id'])){

        //念の為その問題と元々登録されてた問題が一致してるか確認して
        if($quiz['id'] == $_GET['id']){

          //大丈夫だったらその問題に対して答えを定義ウェーイ

          // $answer[$_GET['id']] = $_POST['answer'];

          $sql = ' UPDATE quiz SET '
                  .'  answer = :answer '
                  .'WHERE id = :id '
          ;

          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':answer', $_POST['answer']);
          $stmt->bindParam(':id', $_GET['id']);
          $stmt->execute();
          echo '問題'.$_GET['id'].'の答えを'.$_POST['answer'].'に登録完了';
        }else{
          echo 'フラグのついている問題にしか答えは登録できマシェん';
        }
      }else{
        echo '登録すらできてない';
      }
    }

    //次の問題表示するときにフラグ消さないといけないのでこの処理要る
    //これ押せば全チームの画面から問題の表示消える
    if(isset($_POST['delete'])){
      //　答え削除 フラグoff
      $sql = ' UPDATE quiz SET '
              .' answer = null, '
              .' flg = 0 '
              .'WHERE id = :id '
      ;

      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':id', $_GET['id']);
      $stmt->execute();

      $sql = ' UPDATE bets SET '
              .' flg = 0 '
              .'WHERE quiz_id = :id '
      ;
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':id', $_GET['id']);
      $stmt->execute();

      // unset($_SESSION['flg']);
      // unset($_SESSION['quiz_id']);
      echo '問題'.$_GET['id'].'の解除完了';
    }
}



//集計データ

// if(count($teams) != 30){
//   $data = "";
//   foreach ($teams as $k => $v) {
//     $answer = "<input type=\"hidden\" name=\"answer".$v["id"]."\" value=\"". $v["answer"]. "\">";
//     $bet    = "<input type=\"hidden\" name=\"bet".$v["id"]."\" value=\"". $v["bet"]. "\">";
//     $data  .= $answer.$bet;
//   }
// }


if(isset($_POST["add"])){
  $stmt= $pdo->query ( 'select * from teams where id = '.'\''.$_POST['win'].'\'');
  $team = $stmt->fetch(PDO::FETCH_ASSOC);
    $point = $team['point'] + 5;

  //ポイント反映しまーす
  $sql = ' UPDATE teams SET '
          .'  point = :point '
          .'WHERE id = :team '
  ;

  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':point', $point);
  $stmt->bindParam(':team', $_POST['win']);
  $stmt->execute();
}


//リセット処理
if(isset($_POST["reset"])){
  $sql = ' delete from bets ' ;
  $pdo->query($sql);

  $sql = ' update teams set point = 10';
  $pdo->query($sql);

  $sql = ' update quiz set answer = null,'
          .' flg = 0 '
        ;
  $pdo->query($sql);
}

 ?>
<h1>現在の集計状況</h1>
<p><?=(isset($quiz['id']) ? '現在の問題:'.$quiz['id'] : '') ?></p>
<p><?=(isset($quiz['answer']) ? '答え:'.$quiz['answer'] : '') ?></p>
<?php if(isset($teams) && $quiz['flg'] == 1):?>
  <p>ベット済みのチーム</p>
 <?php foreach($teams as $team):?>
   <!-- フラグが立ってる問題に対してベットしたとこのチーム名が出る -->
   <p><?= 'チーム'.$team['team'].':'.$team['answer'].'に'.$team['bet'].'ベット'?></p>
 <?php endforeach;?>
<?php endif;?>

<h2>問題の答えを登録してください</h2>

<?php foreach($quizs as $v):?>
<h3>問題<?= $v['id'] ?></h3>

 <form action="admin.php?id=<?= $v['id'] ?>" method="post">
   <select name="answer">
     <option value="A">A</option>
     <option value="B">B</option>
     <option value="C">C</option>
     <option value="D">D</option>
   </select>
   <input type="submit" name="regist" value="問題登録">
   <input type="submit" name="correct" value="解答登録">
   <input type="submit" name="delete" value="解除">
</form>
<?php endforeach ; ?>

<h4>集計ボタン</h4>
<form action="syuukei.php" method="post">
  <input type="hidden" name="quiz_id" value="<?=$quiz['id']?>">
  <input type="hidden" name="answer" value="<?=(isset($quiz['answer']) ? $quiz['answer'] : '') ?>">
  <input type="hidden" name="rarity" value="<?=(isset($quiz['rarity']) ? $quiz['rarity'] : '') ?>">
  <input type="submit" name="calc" value="集計">
</form>

<h4>ポイント追加ボタン</h4>
<form action="" method="post">
  <select name="win">
    <?php foreach($all_team as $v):?>
      <option value="<?=$v['id'] ?>"><?=$v['id'] ?></option>
  <?php endforeach; ?>
  </select>
  <input type="submit" name="add" value="勝利チームにポイント追加">
</form>

<h4>リセットボタン</h4>
<form action="" method="post" onclick="return confirm('リセットしてもいいよね？');">
  <input type="submit" name="reset" value="ぜーんぶリセット">
</form>

<a href="result.php">結果へ(表示用)</a>
<a href="status.php">ステータスへ(自分用)</a>
