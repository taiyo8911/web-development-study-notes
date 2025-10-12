<?php
// --- 処理部分 --- //
$fortunes = ["大吉", "中吉", "小吉", "末吉", "凶"];
$result = $fortunes[array_rand($fortunes)]; // ランダムで選ぶ
?>

<body>
  <h1>おみくじ結果</h1>
  <p class="result">あなたの運勢は <strong><?= htmlspecialchars($result, ENT_QUOTES, 'UTF-8') ?></strong> です！</p>

  <form action="index.html" method="get">
    <button type="submit">もう一度引く</button>
  </form>
</body>