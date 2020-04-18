<?php

require_once(__DIR__ . '/functions.php');


// 変数の初期化
$message = null;
$name = null;
$error_messages = [];
$post_date = null;
$mysqli = null;
$sql = null;
$res = null;
$message_array = [];

// 投稿フォームに値が入力されているか
if (isset($_POST['btn'])) {
  if (empty($_POST['text'])) {
    array_push($error_messages, '本文を入力してください。');
  } else {
    $message = $_POST['text'];
  }

  if (($_POST['name'])) {
    $name = $_POST['name'];
  } else {
    $name = "名無し";
  }
}

// 投稿フォームの入力が正常な場合、DBに登録
if (isset($name) && isset($message)) {

  // データベースに接続
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

  // 接続エラーの確認
	if( $mysqli->connect_errno ) {
	  $error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
	} else {
    // 文字コード設定
		$mysqli->set_charset('utf8');

		// 書き込み日時を取得
		$post_date = date("Y-m-d H:i:s");

		// データを登録するSQL作成
		$sql = "INSERT INTO message (name, message, post_date) VALUES ( '$name', '$message', '$post_date')";

		// データを登録
		$res = $mysqli->query($sql);

		// データベースの接続を閉じる
		$mysqli->close();
	}

  // リロードによる投稿を簡易的に防ぐ
  header('Location: ./');
}


// 投稿の名前で検索する
if (isset($_GET['search'])) {

  $res = getSearchData();

  if( $res ) {
    $message_array = $res->fetch_all(MYSQLI_ASSOC);
  }
}

function getSearchData() {
  // データベースに接続
  $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

  // 接続エラーの確認
  if( $mysqli->connect_errno ) {
    $error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
  } else {
    $search = $_GET['search'];
    $sql = "SELECT * FROM message where name LIKE '%$search%' ORDER BY post_date DESC";
    $res = $mysqli->query($sql);
    $mysqli->close();
  }

  return $res;
}

// 投稿日時でソートする
if (isset($_GET['post_date_sort'])) {
  $res = getPostDateSort();

  if( $res ) {
    $message_array = $res->fetch_all(MYSQLI_ASSOC);
  }

}

function getPostDateSort() {
  $typeOfSort = $_GET['post_date_sort'] == 'asc' ? 'ASC':'DESC';

  $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
  // 接続エラーの確認
  if( $mysqli->connect_errno ) {
    $error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
  } else {

    $sql = "SELECT * FROM message ORDER BY post_date $typeOfSort";
    $res = $mysqli->query($sql);
    $mysqli->close();
  }

  return $res;
}

// DBの全データを取得
// データベースに接続
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 接続エラーの確認
if( $mysqli->connect_errno ) {
  $error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {

  $sql = "SELECT * FROM message ORDER BY post_date DESC";
  $res = $mysqli->query($sql);

  if( $res ) {
    $message_array = $res->fetch_all(MYSQLI_ASSOC);
  }

  // データベースの接続を閉じる
  $mysqli->close();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Q2:掲示板</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2 class="">掲示板</h2>
  <?php if (isset($error_messages)) : ?>
    <div class="error_messages">
      <?php foreach ($error_messages as $error_message) : ?>
        <p><?= h($error_message) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <div class="">
    <h3>投稿フォーム</h3>
    <form class="" action="" method="post">
      <input type="text" name="name">
      <textarea name="text" rows="8" cols="80"></textarea>
      <input type="file" nmae="img">
      <input type="submit" name="btn" value="書き込む">
    </form>
  </div>

  <div class="">
    <h3>投稿内容</h3>
    <form action="" method="get">
       <p>投稿を名前で検索</p>
       <input type="text" name="search" value="">
       <input type="submit" name="" value="検索">
     </form>
     <form action="" method="get">
       <p>投稿日時で並び替え</p>
       <select name="post_date_sort">
         <option value="desc">新しい投稿順</option>
         <option value="asc">古い投稿順</option>
       </select>
       <input type="submit" name="" value="並び替え">
     </form>
    <?php if(!empty($message_array)) : ?>
    <?php foreach ($message_array as $message): ?>
    <div>
      <p>
        <a href="http://localhost/php/rotoeggs_ex3/edit.php?message_id=<?php echo $message['id']; ?>">編集</a>
        <a href="http://localhost/php/rotoeggs_ex3/delete.php?message_id=<?php echo $message['id']; ?>">削除</a>
      </p>
      <dl>
        <dt>名前</dt>
        <dd><?= h($message['name']) ?></dd>
      </dl>
      <dl>
        <dt>投稿日時</dt>
        <dd><?php echo h($message['post_date']) ?></dd>
      </dl>
      <dl>
        <dt>変更日時</dt>
        <dd>
          <?php if(!empty($message['edit_date'])) : ?>
            <?= h($message['edit_date']) ?>
          <?php endif; ?>
        </dd>
      </dl>
      <dl>
        <dt>本文</dt>
        <dd><?= nl2br($message['message']) ?></dd>
      </dl>
      <dl>
        <dt>画像</dt>
        <dd></dd>
      </dl>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
