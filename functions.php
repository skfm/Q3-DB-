<?php

// データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'bulletin_board');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// エスケープ処理
function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

?>
