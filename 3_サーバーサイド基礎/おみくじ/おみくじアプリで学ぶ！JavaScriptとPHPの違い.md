## 🎯 学習目的
- Webアプリがどのように動くか理解する  
- 「フロントエンド（JavaScript）」と「バックエンド（PHP）」の役割を区別できるようになる  
- 同じ「おみくじ」でも、処理の場所・仕組み・通信がどう違うのかを体験する  
---

## 🧩 1. JavaScript版おみくじ：ブラウザの中だけで完結

### 📄 ファイル名：`fortune_js.html`

```html
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>おみくじ（JavaScript版）</title>
</head>
<body>
  <h1>今日の運勢は？</h1>
  <button onclick="showFortune()">おみくじを引く</button>
  <p id="result"></p>

  <script>
    function showFortune() {
      const fortunes = ["大吉", "中吉", "小吉", "末吉", "凶"];
      const result = fortunes[Math.floor(Math.random() * fortunes.length)];
      document.getElementById("result").textContent = result;
    }
  </script>
</body>
</html>
```

### 💬 解説
- JavaScriptはブラウザの中で動くプログラム。
- ボタンを押すと、JavaScriptが即座にページの内容を書き換える。
- サーバー通信は発生しない（ネットが切れてても動く）。


| 項目    | 内容                             |
| ----- | ------------------------------ |
| 実行場所  | ブラウザ（ユーザーの端末）                  |
| 仕組み   | ページの中身（DOM）を書き換える              |
| メリット  | 動作が速い・通信不要                     |
| デメリット | サーバーの情報にはアクセスできない（例：ログイン状態やDB） |


## ⚙️ 2. PHP版おみくじ：サーバーが結果を返す

### 📄 ファイル名：`index.html`
```html
<body>
  <h1>おみくじ（PHP版）</h1>
  <form action="result.php" method="post">
    <button type="submit">おみくじを引く</button>
  </form>
</body>
</html>
```

### 💬 解説
- action="result.php"：ボタンを押すと、このPHPファイルにリクエストが送られる
- method="post"：HTTPメソッド（今回はPOST）
- HTML側は見た目と入力フォームだけを担当する


### 📄 ファイル名：`result.php`
```php
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
```

### 💬 解説

💬 解説
- PHPはサーバー上で動くプログラム。
- ブラウザがPHPファイルにアクセス → サーバーが結果を生成 → HTMLを返す。
- ボタンを押すたびにページ全体が再読み込みされ、新しい結果が出る。


| 項目    | 内容                                  |
| ----- | ----------------------------------- |
| 実行場所  | サーバー                                |
| 仕組み   | ブラウザ → サーバーにリクエスト → PHPがHTMLを生成して返す |
| メリット  | サーバー側の処理やデータベース連携が可能                |
| デメリット | ページがリロードされる・通信が必要                   |


## 🔍 3. クライアントサイドとサーバーサイドの比較
| 観点     | JavaScript版   | PHP版           |
| ------ | ------------- | -------------- |
| 実行場所   | ブラウザ（クライアント）  | サーバー           |
| 更新のしかた | DOM操作で即座に変更   | ページ全体を再生成      |
| 通信     | 不要（オフラインでもOK） | 必要（サーバーにリクエスト） |
| 速度     | 非常に速い         | 通信分だけ遅い        |
| 得意分野   | UIの動き・即時操作    | データ処理・認証・保存    |


## 🧭 4. まとめ：どちらも必要！
- JavaScript → 見た目や動きを作る「表の仕事」
- PHP → ロジックやデータ管理を行う「裏の仕事」

**💬 Webアプリはこの2つが協力して動く：**
>JavaScriptがユーザーの操作を受け取り、
PHPがデータベースや処理結果を返す

という分業構造。

## 5. 成果物
[おみくじアプリPHP](https://taiyo8911.cloudfree.jp/myapp/fortune/)
