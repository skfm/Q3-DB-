<?php

require_once(__DIR__ . '/functions.php');

// 変数の初期化
$message_id = null;
$edit_date = null;
$error_messages = [];
$mysqli = null;
$sql = null;
$res = null;
$message_data = [];


// GET値からidが渡された場合は削除確認画面を表示、POST値からidが渡された場合は内容を削除する
if( !empty($_GET['message_id']) && empty($_POST['message_id']) ) {

	// 投稿を取得するコード
  $message_id = $_GET['message_id'];

  // データベースに接続
  $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

  // 接続エラーの確認
  if( $mysqli->connect_errno ) {
    $error_message[] = 'データの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
  } else {

    $sql = "SELECT id, name, message, post_date FROM message WHERE id = $message_id";
    $res = $mysqli->query($sql);

    if( $res ) {
      $message_data = $res->fetch_assoc();
    } else {
      header("Location: http://localhost/php/rotoeggs_ex3/index.php");
    }

    // データベースの接続を閉じる
    $mysqli->close();
  }

} elseif ( !empty($_POST['message_id']) ) {

  $message_id = $_POST['message_id'];

  // データベースに接続
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

  // 接続エラーの確認
	if( $mysqli->connect_errno ) {
	  $error_message[] = 'データベースの接続に失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
	} else {
		$sql = "DELETE FROM message WHERE id = $message_id";
		$res = $mysqli->query($sql);
  }

	$mysqli->close();

  if( $res ) {
	  header("Location: http://localhost/php/rotoeggs_ex3/index.php");
  }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Q2:掲示板 削除</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2 class="">掲示板 削除</h2>
  <?php if (isset($error_messages)) : ?>
    <div class="error_messages">
      <?php foreach ($error_messages as $error_message) : ?>
        <p><?= h($error_message) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <p>
    以下の投稿を削除します。<br>よろしければ「削除」ボタンを押してください。
  </p>
  <div class="">
    <h3>投稿フォーム</h3>
    <form method="post">
      <input type="text" name="name" value="<?php if( isset($message_data['name']) ){ echo h($message_data['name']); } ?>" disabled>
      <textarea name="text" rows="8" cols="80" disabled><?php if( isset($message_data['message']) ){ echo h(($message_data['message'])); } ?></textarea>
      <input type="file" nmae="img">
      <a class="btn_cancel" href="http://localhost/php/rotoeggs_ex3/index.php">キャンセル</a>
      <input type="submit" name="btn" value="削除">
      <input type="hidden" name="message_id" value="<?php echo h($message_data['id']); ?>">
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
