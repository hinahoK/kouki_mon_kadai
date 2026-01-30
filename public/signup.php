<?php
// DBに接続
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
$error_message = '';

if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
  // POSTで name と email と password が送られてきた場合はDBへの登録処理をする
  $check_sth = $dbh->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
  $check_sth->execute([':email' => $_POST['email']]);
  $email_exists = $check_sth->fetchColumn();

  if ($email_exists > 0) {
  // 既に登録されている場合
    $error_message = '既にこのメールアドレスは登録されています';
  } else {
  // insertする
  function stretchHash($password, $salt, $iterations = 100000){
    $hash = $password.$salt;
    for ($i = 0; $i < $iterations; $i++){
      $hash = hash('sha256',$hash);
    }
    return $hash;
  }

  $insert_sth = $dbh->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
  $insert_sth->execute([
    ':name' => $_POST['name'],
    ':email' => $_POST['email'],
    ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
  ]);

  // 処理が終わったら完了画面にリダイレクト
  header("HTTP/1.1 303 See Other");
  header("Location: ./signup_finish.php");
  return;
  }
}
?>
<h1>会員登録</h1>

会員登録済の人は<a href="/login.php">ログイン</a>しましょう。
<hr>

<!-- 登録フォーム -->
<form method="POST">
  <!-- input要素のtype属性は全部textでも動くが、適切なものに設定すると利用者は使いやすい -->
  <label>
    名前:
    <input type="text" name="name">
  </label>
  <br>
  <label>
    メールアドレス:
    <input type="email" name="email">
  </label>
  <br>
  <label>
    パスワード:
    <input type="password" name="password" minlength="6" autocomplete="new-password">
  </label>
  <br>
  <button type="submit">決定</button>
</form>
