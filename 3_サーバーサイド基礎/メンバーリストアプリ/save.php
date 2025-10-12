<?php
$name = $_POST['name'] ?? '';
$age = $_POST['age'] ?? '';

// バリデーション
$errors = [];
if (empty($name)) {
  $errors[] = "名前を入力してください";
}
if (empty($age)) {
  $errors[] = "年齢を入力してください";
}

// エラーがなければ保存
if (count($errors) === 0) {
  // 新しいメンバーデータ
  $new_member = [
    'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    'age' => htmlspecialchars($age, ENT_QUOTES, 'UTF-8'),
    'registered_at' => date('Y-m-d H:i:s')
  ];

  // 既存のデータを読み込む
  $members = [];
  if (file_exists('members.json')) {
    $json = file_get_contents('members.json');
    $members = json_decode($json, true) ?? [];
  }

  // 新しいメンバーを追加
  $members[] = $new_member;

  // JSONファイルに保存
  $json = json_encode($members, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  file_put_contents('members.json', $json);

  // 完了ページにリダイレクト
  header('Location: list.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>登録エラー</title>
</head>
<body>
  <h1>エラーが発生しました</h1>
  <?php foreach ($errors as $error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
  <?php endforeach; ?>
  <a href="index.php">戻る</a>
</body>
</html>