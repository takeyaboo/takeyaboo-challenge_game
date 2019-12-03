<?php

session_start();

require('config.php');
require('func.php');

$pdo = pdo();

if(empty($_SESSION['id'])){
  header('Location:login.php');
}

if(isset($_POST['calc'])){
  $stmt = $pdo->query ( 'select * from bets where flg = 1 and quiz_id = '.'\''.$_POST['quiz_id'].'\'');
  $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //チームがXチーム揃ってなければ　ないしは　答えが定義されてなかったら（答えを登録してリロードしてなかったら）
  if(count($teams) >= 1 && !empty($_POST['answer'])){
    foreach ($teams as $k => $v) {
      $stmt= $pdo->query ( 'select * from teams where id = '.$v['team']);
      $team = $stmt->fetch(PDO::FETCH_ASSOC);
      if($_POST['answer'] == $v['answer']){
        //あってたら
        $add = $v['bet'] * $_POST['rarity'];
        // $point = $team['point'] + $add + $v['bet'];
        $point = $team['point'] + $add;
      }else{
        //間違ってたら
        $point = $team['point'] - $v['bet'];
        // $point = $team['point'];
      }

      //ポイント反映しまーす
      $sql = ' UPDATE teams SET '
              .'  point = :point '
              .'WHERE id = :team '
      ;

      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':point', $point);
      $stmt->bindParam(':team', $v['team']);
      $stmt->execute();
    }

    //クイズのフラグを解除し答えもリセットする
    $sql = ' UPDATE quiz SET flg = 0, '
            .' answer = null'
            .' WHERE id = '.'\''.$_POST['quiz_id'].'\'';
    $pdo->exec($sql);
    // unset($_SESSION['flg']);
    // unset($_SESSION['quiz_id']);

    //ベットのフラグを解除する
    $sql = ' UPDATE bets SET flg = 0 WHERE quiz_id = '.'\''.$_POST['quiz_id'].'\'';
    $pdo->exec($sql);

  }else{
    header('Location:admin.php');
    exit();
  }
}else{
  header('Location:admin.php');
  exit;
}


echo '集計が完了しました';
echo '<a href="admin.php">戻る</a>';

?>
