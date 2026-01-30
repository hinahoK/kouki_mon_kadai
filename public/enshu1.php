<?php
date_default_timezone_set('Asia/Tokyo');

//セッションIDのクッキー名
$session_cookie_name = 'session_id';

//セッションIDを取得または新規生成
$session_id = $_COOKIE[$session_cookie_name] ?? base64_encode(random_bytes(64));
if (!isset($_COOKIE[$session_cookie_name])) {
  setcookie($session_cookie_name, $session_id);
}

// 接続 (redisコンテナの6379番ポートに接続)
$redis = new Redis();
$redis->connect('redis', 6379);

//セッション用のkeyを作成
$redis_session_key = "session-" . $session_id;

//セッションデータを取得
$session_values = $redis->exists($redis_session_key)
  ? json_decode($redis->get($redis_session_key), true)
  : [];

//前回のアクセス日時を取得(初回なら”なし”)
$last_access = $session_values["last_access"] ?? "なし";

//アクセスカウントを取得・更新
$session_access_count = isset($session_values["access_count"]) ? intval($session_values["access_count"]) : 0;
$session_access_count++;

//現在の日時を取得（日本時間）
$current_time = date("Y年m月d日 H時i分s秒");

//セッションデータにカウントを保存
$session_values["access_count"] = $session_access_count;
$session_values["last_access"] = $current_time;
$redis->set($redis_session_key, json_encode($session_values));

//表示
echo "このセッションで{$session_access_count}回目のアクセスです！<br>";
echo "前回の訪問は{$last_access}でした。";
?>
