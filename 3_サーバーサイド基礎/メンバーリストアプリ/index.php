<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>メンバー登録</title>
</head>
<body>
  <h1>メンバー登録</h1>
  <form action="save.php" method="POST">
    <input type="text" name="name" placeholder="名前" required><br>
    <input type="number" name="age" placeholder="年齢" required><br>
    <button type="submit">登録</button>
  </form>
</body>
</html>