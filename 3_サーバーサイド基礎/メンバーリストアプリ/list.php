<?php
// JSONファイルを読み込む
$members = [];
if (file_exists('members.json')) {
  $json = file_get_contents('members.json');
  $members = json_decode($json, true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>メンバーリスト</title>
  <style>
    body {
      font-family: sans-serif;
      max-width: 600px;
      margin: 50px auto;
      padding: 20px;
    }
    .member-card {
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin: 10px 0;
    }
  </style>
</head>
<body>
  <h1>メンバーリスト</h1>
  
  <?php if (empty($members)): ?>
    <p>まだメンバーが登録されていません。</p>
  <?php else: ?>
    <?php foreach ($members as $member): ?>
      <div class="member-card">
        <p><strong><?php echo htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?></strong>さん</p>
        <p>年齢: <?php echo htmlspecialchars($member['age'], ENT_QUOTES, 'UTF-8'); ?>歳</p>
        <p><small>登録日時: <?php echo htmlspecialchars($member['registered_at'], ENT_QUOTES, 'UTF-8'); ?></small></p>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <p><a href="index.php">新規登録</a></p>
</body>
</html>