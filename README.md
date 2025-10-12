# web-development-study-notes
 Webエンジニアの学習記録

**目標：Webアプリエンジニアとして転職できる力を身につける**
---

## 🎯 カリキュラム全体像
### 目的
Webアプリの通信の流れを理解し、フロント〜サーバー〜DB〜デプロイまで自力で構築できる力を身につける。

### 最終成果
自作のフルスタックWebアプリ（オリジナルポートフォリオ）

### 環境
- **OS**: macOS
- **前提**: ターミナル操作・ブラウザ動作を前提

---

## 🧭 ステップ構成（通信の流れに沿って）

| 月 | 学習ステップ | 主な内容 | 成果物 / ゴール |
|---|---|---|---|
| **1〜2月** | ① Webサービスの仕組み / PC操作基礎 | ・HTTP / HTTPSの基礎<br>・ブラウザ〜サーバーの通信の流れ<br>・Linux基本操作（ls, cd, mkdir, rm, nano, chmodなど）<br>・CLIの使い方 | ・Linuxでファイル操作練習<br>・Web通信の図解を自分で説明できる |
| **3〜4月** | ② フロントエンド基礎（静的サイト） | ・HTML / CSS / JavaScript基礎<br>・DOM操作とイベント<br>・APIをfetchで叩いてデータ表示<br>・開発者ツールの使い方 | ・APIを使った小アプリ（例：天気検索、ランダム名言表示など） |
| **5〜6月** | ③ サーバーサイド基礎（通信の裏側） | ・リクエストとレスポンスの流れ<br>・REST APIとは<br>・PHP基礎文法<br>・フォーム送信〜サーバー処理〜HTML返却 | ・自作のミニ掲示板（DBなし） |
| **7〜8月** | ④ Laravelによる開発（MVCの理解） | ・Laravelの基本構成（ルーティング / コントローラ / モデル / ビュー）<br>・Bladeテンプレート<br>・ルーティングとコントローラの連携<br>・CRUD処理（データ登録・更新・削除）<br>・バリデーション / 認証（ログイン機能） | ・LaravelでToDoアプリ（DB連携＋ログイン機能） |
| **9月** | ⑤ データベース設計（MySQL / SQL） | ・ER図の考え方<br>・テーブル設計（1対多、多対多）<br>・基本SQL（SELECT, JOIN, GROUP BY）<br>・Eloquent ORMの使い方 | ・Laravel + MySQL 連携練習プロジェクト |
| **10月** | ⑥ 環境構築・チーム開発基礎 | ・Dockerの概念と構築<br>・docker-composeでLaravel環境を構築<br>・Git / GitHubの基本操作（commit, branch, merge）<br>・GitHub Flow / Pull Request | ・GitHub上で開発環境を共有できる状態に |
| **11月** | ⑦ 応用・ポートフォリオ制作 | ・自分で企画・設計・実装<br>・認証 / API通信 / DB連携を統合<br>・UI/UXを意識した設計 | ・オリジナルWebアプリ（下記アイデア例あり） |
| **12月** | ⑧ デプロイと転職準備 | ・VPS or Render/Vercelでデプロイ<br>・README / 開発ドキュメント整備<br>・技術スタックの棚卸し<br>・職務経歴書 / GitHub整備 | ・公開済みポートフォリオ＋GitHubリポジトリ |

---

## 💡 オリジナルポートフォリオ案（EC・ブログ以外）

**重視ポイント：「Webの通信を理解している」ことが伝わるもの**

| アイデア | 通信の特徴 | 技術的チャレンジ |
|---|---|---|
| 📅 **イベント予約アプリ** | GET: 一覧表示<br>POST: 予約登録<br>DELETE: 予約取消 | 認証＋DB連携＋カレンダーUI |
| 🧮 **支出記録＋グラフ表示アプリ** | APIでデータ取得→可視化 | Chart.js＋Eloquent |
| 🎮 **簡単なオンラインゲームスコア共有アプリ** | JSON API通信 | JavaScript Fetch + Laravel API |
| 📋 **チーム用タスクボード（Trello風）** | 双方向更新 | Vue.js＋Laravel Sanctum認証 |

---

## 📚 学習リソース（厳選）

| 分野 | 教材例 |
|---|---|
| **Linux / CLI** | [Linux超入門 - ドットインストール](https://dotinstall.com/) |
| **HTML/CSS/JS** | [MDN Web Docs](https://developer.mozilla.org/ja/), [Progate](https://prog-8.com/) |
| **PHP** | [ドットインストール PHP入門](https://dotinstall.com/) |
| **Laravel** | [Laravel公式ドキュメント](https://laravel.com/docs), Udemy講座（初心者向けLaravel実践） |
| **SQL** | [SQLZOO](https://sqlzoo.net/), paiza SQL入門編 |
| **Docker** | Techpit: Laravel × Docker環境構築講座 |
| **Git/GitHub** | [Git入門 - ドットインストール](https://dotinstall.com/) |

---

## 🚀 学習の進め方ルール

### 1. 「通信の流れ」を意識して学ぶ
常に「**リクエスト → サーバー処理 → レスポンス**」の構造を頭に置く。

### 2. 月ごとに小さな成果物を作る
例：API連携 → CRUD → 認証 → Docker → デプロイ

### 3. GitHubに毎月1リポジトリ追加
成長の証明になる。

### 4. 9〜12月は"作りながら学ぶ"フェーズ
ポートフォリオ作りが最重要。

### 5. アウトプット中心
学んだことをZenn / Qiitaに記事化しても◎（転職で評価されやすい）

---

## 📝 教材の形式

**チュートリアル教材（手を動かしながら進める）**
- 実践を重視
- 各ステップで動くものを作る
- 理解度を確認できる課題つき

---

## 🎯 このカリキュラムで得られるスキル
✅ Web通信の仕組みを深く理解  
✅ フロントエンド開発（HTML/CSS/JavaScript）  
✅ サーバーサイド開発（PHP/Laravel）  
✅ データベース設計と操作（MySQL/SQL）  
✅ 開発環境構築（Docker）  
✅ バージョン管理（Git/GitHub）  
✅ デプロイとインフラ基礎  
✅ ポートフォリオ作成力

---
