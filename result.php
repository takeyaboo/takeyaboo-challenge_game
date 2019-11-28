<?php

require('config.php');
require('func.php');

$pdo = pdo();

$stmt = $pdo->query ( 'select * from bets where flg = 1');
$bets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query ( 'select * from quiz where flg = 1');
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query ( 'select * from teams where id > 0 order by point desc, id asc');
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<html lang="ja">
<head>
 <!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>結果</title>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<h1 class="text-center bg-primary text-light">結果</h1>
<h3 class="mt-3">[ベット数]</h3>
<button class="btn btn-primary"
    data-toggle="collapse"
    data-target="#bet"
    aria-expand="false"
    aria-controls="bet">OPEN</button>
<div class="collapse" id="bet">
  <div class="card card-body">
    <?php foreach($bets as $bet):?>
      <!-- フラグが立ってる問題に対してベットしたとこのチーム名が出る -->
      <p><?= 'チーム'.$bet['team'].':'.$bet['answer'].'に'.$bet['bet'].'ベット'?></p>
    <?php endforeach;?>
  </div>
</div>
<h3 class="mt-4">[答え]</h3>
<button class="btn btn-primary"
    data-toggle="collapse"
    data-target="#answer"
    aria-expand="false"
    aria-controls="answer">OPEN</button>
<div class="collapse" id="answer">
  <div class="card card-body">
    <?= $quiz['answer'] ?>
  </div>
</div>

<h3 class="mt-4">[各チームのポイント]</h3>
<button class="btn btn-primary"
    data-toggle="collapse"
    data-target="#point"
    aria-expand="false"
    aria-controls="point">OPEN</button>
<div class="collapse" id="point">
  <div class="card card-body">
    <?php foreach($teams as $key => $team):?>
      <!-- フラグが立ってる問題に対してベットしたとこのチーム名が出る -->
      <?php $key += 1 ?>
        <p><?= $key.'位:チーム'.$team['id'].'/'.$team['point'].'ポイント'?></p><hr>
    <?php endforeach;?>
  </div>
</div>
<br>
<a href="admin.php" class="btn btn-secondary mt-5">戻る</a>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
