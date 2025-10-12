**目標：Webアプリの通信の流れを、実際に手を動かして「体感的に理解」する**

---

## 🗺️ 学習の全体像

| 章 | 内容 | 実践内容 | 所要時間 |
| --- | --- | --- | --- |
| 第1章 | Webサービスとは何か | Webの全体像を図で理解 | 30分 |
| 第2章 | HTTP通信の流れ | ブラウザ → サーバーの通信を観察 | 1時間 |
| 第3章 | curlでリクエストを送る | コマンドラインから通信体験 | 1.5時間 |
| 第4章 | JSON APIを叩く | 実際のAPIを使った開発体験 | 2時間 |
| 第5章 | 総合演習 | 学んだことを統合して説明 | 1時間 |

**合計学習時間：約6時間（2〜3日）**

---

## 第1章｜Webサービスとは何か

### 💡 1-1. Webアプリの3層構造

Webサービスは以下の3つの層で構成されています。

```
[フロントエンド] ⇄ [サーバー] ⇄ [データベース]
   (ブラウザ)      (処理)         (保存)

```

| 層 | 役割 | 具体例 |
| --- | --- | --- |
| **フロントエンド** | ユーザーの入力を受け取り、結果を表示する | HTML/CSS/JavaScript |
| **サーバーサイド** | リクエストを処理し、データを取得・保存する | PHP/Laravel/Node.js |
| **データベース** | 情報を永続的に保存する | MySQL/PostgreSQL |

### 🧠 1-2. HTTP通信の基本

**HTTP**（Hypertext Transfer Protocol）は、ブラウザとサーバーがやり取りするための"言語"です。

**通信の基本パターン：**

1. **リクエスト**：ブラウザが「このページください」とサーバーに依頼
2. **処理**：サーバーが要求を処理（必要ならDBにアクセス）
3. **レスポンス**：サーバーが「これどうぞ」とデータを返す

> 💬 例え話
> 
> 
> レストランで注文（リクエスト）すると、厨房（サーバー）が調理し、料理（レスポンス）が出てくる。これとまったく同じ流れです。
> 

### ✏️ 実践1：Webの構造を図にする

**課題：** 紙やデジタルツールで、以下を図示してください。

- あなたのPC（ブラウザ）
- インターネット
- サーバー
- データベース

矢印で「リクエスト」「レスポンス」の流れを書き込みましょう。

---

## 第2章｜HTTP通信の流れを観察する

### 2-1. 開発者ツールで通信を見る

実際にWebサイトの通信を観察してみましょう。

**手順：**

1. **ChromeまたはSafariを開く**
2. 任意のWebサイト（例：`https://example.com`）にアクセス
3. 右クリック → **「検証」** を選択
4. **「Network（ネットワーク）」** タブをクリック
5. ページを再読み込み（⌘ + R）して通信一覧を観察

### 📍 観察ポイント

開発者ツールで以下の項目を確認してください：

| 項目 | 意味 | よくある値 |
| --- | --- | --- |
| **Name** | リクエスト先のファイル名やURL | index.html, style.css |
| **Method** | HTTPメソッド | GET, POST, PUT, DELETE |
| **Status** | サーバーの応答状態 | 200（成功）, 404（見つからない） |
| **Type** | データの種類 | document, script, json |
| **Size** | データサイズ | 10.2 KB など |

### ✏️ 実践2：通信を記録する

**課題：** 以下の表を埋めてください。

| サイト名 | リクエストURL | Method | Status | Type | 備考 |
| --- | --- | --- | --- | --- | --- |
| 例：Yahoo | https://www.yahoo.co.jp/ | GET | 200 | document | トップページ取得 |
|  |  |  |  |  |  |
|  |  |  |  |  |  |
|  |  |  |  |  |  |

**ヒント：** 画像（jpg/png）、CSS、JavaScriptなど、様々なタイプのリクエストを見つけてみましょう。

---

## 第3章｜HTTPリクエストを手で送る（curl入門）

ブラウザを使わず、コマンドラインから直接HTTPリクエストを送ってみます。

### 3-1. curlコマンドの基本

**curlとは？**

URLを指定してデータを取得できるコマンドラインツールです。macには標準でインストールされています。

**基本の使い方：**

ターミナルを開いて以下を実行してください。

```bash
curl https://example.com
```

**結果：** HTMLのコードがターミナルに表示されます。これがサーバーからの**レスポンス**です。

### 3-2. レスポンスの詳細を確認する

ヘッダー情報（通信の詳細）も見てみましょう。

```bash
curl -i https://example.com
```

- **`i`オプション**：HTTPヘッダーも表示

**確認ポイント：**

- `HTTP/1.1 200 OK` → ステータスコード
- `Content-Type: text/html` → データ形式
- `Content-Length: 1256` → データサイズ

### 3-3. POSTリクエストを送る

GETだけでなく、POSTリクエスト（データ送信）も試してみましょう。

```bash
curl -X POST https://httpbin.org/post -d "name=Taro&message=Hello"
```

**解説：**

- `X POST`：POSTメソッドを指定
- `d`：送信するデータ
- `httpbin.org`：テスト用のサーバー（送信内容をそのまま返してくれる）

**結果例：**

```json
{
  "form": {
    "name": "Taro",
    "message": "Hello"
  },
  "headers": {
    "Content-Type": "application/x-www-form-urlencoded"
  }
}
```

### ✏️ 実践3：curlで通信してみる

以下の操作を実際に行い、結果をメモしてください。

**課題A：** `https://www.google.com` にGETリクエストを送る

```bash
curl https://www.google.com
```

**課題B：** httpbin.orgに自分の名前と好きな言葉を送信する

```bash
curl -X POST https://httpbin.org/post -d "name=あなたの名前&message=好きな言葉"
```

**課題C：** JSONデータを送信する

```bash
curl -X POST https://httpbin.org/post \
-H "Content-Type: application/json" \
-d '{"name":"Hanako","age":25}'
```

**ヒント：** `-H`でヘッダーを指定できます。

---

## 第4章｜JSON APIを叩いてみよう

実際のWebアプリで使われるAPI通信を体験します。

### 4-1. 公開APIでリアルタイムデータ取得

**課題：** **為替レートAPIでUSD**の現在価格を取得してみましょう。

```bash
curl https://api.exchangerate-api.com/v4/latest/USD
```

**結果例：**

```json
{
  "base": "USD",                    // 基準通貨（米ドル）
  "date": "2025-10-11",            // データの日付
  "time_last_updated": 1760140801, // 最終更新時刻（UNIXタイムスタンプ）
  "rates": {                        // 各通貨のレート
    "JPY": 151.8,                  // 1ドル = 151.8円
    "EUR": 0.862,                  // 1ドル = 0.862ユーロ
    "GBP": 0.75,                   // 1ドル = 0.75ポンド
    ...
  }
}
```

**重要ポイント：**

- データ形式は**JSON**（JavaScriptで扱いやすい）
- APIは「データだけ」を返す（HTMLは含まない）
- フロントエンドがこのデータを受け取って画面に表示する

### 4-2. JavaScriptでAPIを使う

curlの次は、実際のWebページからAPIを叩いてみましょう。

**手順：**

1. エディタ（VSCodeなど）で`usd-price.html`を作成
2. 以下のコードを貼り付け
3. ブラウザで開く

```html
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>為替レート表示</title>
  <style>
    body {
      font-family: sans-serif;
      max-width: 600px;
      margin: 50px auto;
      padding: 20px;
      background-color: #f5f5f5;
    }
    #output {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    button {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <h1>💱 現在の為替レート（USD基準）</h1>
  <button onclick="fetchRates()">レートを取得</button>
  <div id="output">
    <p>ボタンをクリックしてレートを取得してください。</p>
  </div>

  <script>
    function fetchRates() {
      document.getElementById('output').innerHTML = '<p>取得中...</p>';
      
      fetch('https://api.exchangerate-api.com/v4/latest/USD')
        .then(response => response.json())
        .then(data => {
          const jpy = data.rates.JPY;
          const eur = data.rates.EUR;
          const gbp = data.rates.GBP;
          const updated = new Date(data.time_last_updated * 1000).toLocaleString('ja-JP');
          
          document.getElementById('output').innerHTML = `
            <h2>為替レート情報</h2>
            <p><strong>JPY (日本円):</strong> ¥${jpy.toFixed(2)}</p>
            <p><strong>EUR (ユーロ):</strong> €${eur.toFixed(4)}</p>
            <p><strong>GBP (ポンド):</strong> £${gbp.toFixed(4)}</p>
            <p><small>更新日時: ${updated}</small></p>
          `;
        })
        .catch(error => {
          document.getElementById('output').innerHTML = 
            `<p style="color: red;">エラーが発生しました: ${error}</p>`;
        });
    }
  </script>
</body>
</html>
```

**コードの解説：**

| 行 | 説明 |
| --- | --- |
| `fetch(URL)` | 指定したURLにGETリクエストを送信 |
| `.then(res => res.json())` | レスポンスをJSON形式に変換 |
| `.then(data => {...})` | 変換したデータを使って処理 |
| `.catch(error => {...})` | エラーが起きた時の処理 |

### ✏️ 実践4：別のAPIを使ってみる

**課題：** 以下の無料APIを使って、オリジナルのページを作成してください。

**おすすめAPI：**

1. **ランダム名言API**
    
    ```
    https://api.quotable.io/random
    ```
    
    レスポンス例：
    
    ```json
    {
      "content": "The only way to do great work is to love what you do.",
      "author": "Steve Jobs"
    }
    ```
    
2. **猫の画像API**
    
    ```
    https://api.thecatapi.com/v1/images/search
    ```
    
3. **公開APIリスト**
    - https://github.com/public-apis/public-apis

**作成するもの：**

- ボタンを押すとランダムな名言が表示されるページ
- または、ボタンを押すとランダムな猫画像が表示されるページ

---

## 第5章｜総合演習：通信の流れを説明できるようにする

### 🎯 この章のゴール

「Webアプリの通信の流れ」を、技術者に説明できるレベルまで理解を深めます。

### 📝 課題1：通信の観察記録

以下の3つのWebサイトで通信を観察し、表を埋めてください。

**対象サイト：**

1. ニュースサイト（例：Yahoo ニュース）
2. ECサイト（例：Amazon）
3. SNS（例：Twitter/X）

**記録項目：**

| サイト名 | 画像のリクエスト数 | APIリクエストの有無 | 最も大きいファイル | 気づいたこと |
| --- | --- | --- | --- | --- |
|  |  |  |  |  |
|  |  |  |  |  |
|  |  |  |  |  |

### 📝 課題2：curlで実験

以下の通信を実行し、結果の違いを確認してください。

### 1.通常のGETリクエスト

```bash
curl https://www.google.com
```

### 2. ヘッダーにカスタム情報を追加

```bash
curl -H "User-Agent: MyApp/1.0" https://httpbin.org/get
```

### 3. クエリパラメータをつける

```bash
curl "https://httpbin.org/get?name=Taro&age=25"
```

**確認ポイント：** `args`や`headers`の内容がどう変わるか観察しましょう。

### 📝 課題3：通信フロー図の作成

**作成するもの：**

ブラウザからAPIサーバー、データベースまでの通信の流れを図にしてください。

**含めるべき要素：**

- ブラウザ（フロントエンド）
- Webサーバー（サーバーサイド）
- データベース
- リクエストの矢印（メソッドも記載）
- レスポンスの矢印（データ形式も記載）

**ツール例：**

- 手書き（写真を撮る）
- Excalidraw（[https://excalidraw.com/）](https://excalidraw.com/%EF%BC%89)
- Google図形描画
- [draw.io](http://draw.io/)

### 📝 課題4：説明文の作成

作成した図を見ながら、以下の質問に答えてください（各200字程度）。

**質問1：** ユーザーがブラウザでボタンをクリックしてから、画面にデータが表示されるまで、どんな処理が行われますか？

**質問2：** GETリクエストとPOSTリクエストの違いは何ですか？それぞれどんな時に使いますか？

**質問3：** JSONがWebアプリで多用される理由は何だと思いますか？

---

## 🏁 ゴール確認チェックリスト

以下の項目がすべて✅になれば、ステップ①は完了です！

- [ ]  Webアプリの3層構造（フロント・サーバー・DB）を説明できる
- [ ]  HTTPリクエストとレスポンスの関係を理解している
- [ ]  開発者ツールのNetworkタブで通信を観察できる
- [ ]  curlコマンドでGET/POSTリクエストを送信できる
- [ ]  fetchを使ってJavaScriptからAPIを叩ける
- [ ]  JSONデータを取得して画面に表示できる
- [ ]  ブラウザ→サーバー→DBの通信の流れを図で説明できる

---

## 🚀 次のステップへ

お疲れさまでした！Webの通信の仕組みを体験できましたね。

**次は「ステップ② フロントエンド基礎」で、**

- HTMLで構造を作る
- CSSでデザインする
- JavaScriptで動きをつける

というフロントエンド開発の基礎を学んでいきます。

今回学んだ「通信の流れ」を常に意識しながら進めていきましょう！

---

## 📚 参考リンク

- [MDN Web Docs - HTTP入門](https://developer.mozilla.org/ja/docs/Web/HTTP)
- [curl公式ドキュメント](https://curl.se/docs/)
- [公開APIリスト](https://github.com/public-apis/public-apis)
- [JSON入門](https://www.json.org/json-ja.html)