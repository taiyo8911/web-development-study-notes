# 📝 TODOアプリ開発カリキュラム（シンプル版）
**LaravelでMVCフレームワークの基本を学ぶ**

---

## 🎯 このカリキュラムのゴール

**シンプルなTODOアプリを作成しながら、MVCフレームワークの基本を理解します：**
- MVCアーキテクチャの理解
- データベースとの連携（CRUD操作）
- Web通信の仕組みの理解

### 完成するアプリの機能
- タスクの一覧表示
- タスクの追加
- タスクの編集
- タスクの削除

**※優先度・期限・完了/未完了・検索・統計などの機能は含みません**

---

## 📚 カリキュラム構成（全6章）

| 章 | テーマ | 学習内容 | 学習時間 |
|---|---|---|---|
| 第1章 | MVCの基礎理解 | MVC概念・環境構築 | 2時間 |
| 第2章 | 一覧表示（Read） | データベースからデータ取得・表示 | 2時間 |
| 第3章 | 追加機能（Create） | フォームからデータ保存 | 2時間 |
| 第4章 | 編集機能（Update） | データの更新 | 2時間 |
| 第5章 | 削除機能（Delete） | データの削除 | 1時間 |
| 第6章 | ユーザー認証 | ログイン・ユーザー別タスク管理 | 3時間 |

**合計学習時間：約12時間**

---

## 第1章｜MVCフレームワークの基礎理解

### 1-1. MVCアーキテクチャとは？

**MVCは、Webアプリケーションを3つの役割に分けて整理する設計パターンです。**

```
┌─────────────────────────────────────────────────┐
│              Webアプリケーション                  │
├─────────────────────────────────────────────────┤
│                                                 │
│  Model (モデル)                                  │
│  ├─ データベースとのやり取り                      │
│  └─ ビジネスロジック                             │
│                                                 │
│  View (ビュー)                                   │
│  ├─ HTMLの生成                                   │
│  └─ ユーザーに表示する画面                        │
│                                                 │
│  Controller (コントローラー)                      │
│  ├─ リクエストの受付                             │
│  ├─ ModelとViewの橋渡し                          │
│  └─ 処理の流れを制御                             │
│                                                 │
└─────────────────────────────────────────────────┘
```

### 1-2. TODOアプリでのMVCの役割

**例：タスク一覧を表示する場合**

```
【ブラウザ】
    ↓ ① HTTPリクエスト (GET /tasks)
【Controller】TaskController.php
    ↓ ② データベースからタスク取得を依頼
【Model】Task.php
    ↓ ③ データベースにアクセス
【Database】tasks テーブル
    ↓ ④ タスクデータを返す
【Model】Task.php
    ↓ ⑤ タスクデータを Controller に返す
【Controller】TaskController.php
    ↓ ⑥ タスクデータを View に渡す
【View】index.blade.php
    ↓ ⑦ HTMLを生成
【ブラウザ】
    ⑧ HTMLを表示
```

**🌐 Web通信が発生するタイミング：**
- **①ブラウザ→サーバー**：ユーザーがURLにアクセス
- **⑧サーバー→ブラウザ**：HTMLをブラウザに返す

**💡 重要：②〜⑦はサーバー内部の処理なので、Web通信は発生しません**

### 1-3. データベース設計

**今回作成するテーブル**

```sql
-- usersテーブル（ユーザー情報）※Laravel Breezeが自動作成
users
├── id (主キー)
├── name (名前)
├── email (メールアドレス)
├── password (パスワード)
├── created_at (作成日時)
└── updated_at (更新日時)

-- tasksテーブル（タスク情報）
tasks
├── id (主キー・自動採番)
├── user_id (外部キー → users.id)
├── title (タスクのタイトル)
├── description (タスクの説明・任意)
├── created_at (作成日時・自動)
└── updated_at (更新日時・自動)
```

**削除した項目（次のステップで追加可能）：**
- ~~due_date (期限)~~
- ~~priority (優先度)~~
- ~~completed (完了フラグ)~~

### 1-4. 環境構築

#### 必要なツールをインストール（Mac）

```bash
# 1. Homebrewのインストール
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. PHPとComposerのインストール
brew install php composer

# 3. Node.jsのインストール
brew install node
```

#### Laravelプロジェクトの作成

```bash
# TODOアプリプロジェクトを作成
composer create-project laravel/laravel simple-todo
cd simple-todo

# データベース設定（SQLiteを使用）
touch database/database.sqlite

# .envファイルを編集
# DB_CONNECTION=sqlite
# 他のDB_*行をコメントアウトまたは削除

# 開発サーバー起動
php artisan serve
```

**✅ http://localhost:8000 でLaravelの画面が表示されればOK！**

**データベースのデータについて**

今回使用するSQLiteはファイルベースのデータベースで、database/database.sqliteというファイルがデータベース本体です。
```
todo-app/
├── database/
│   └── database.sqlite  ← このファイルにデータが保存されている
├── app/
├── routes/
└── ...
```

---

## 第2章｜タスク一覧の実装（Read）

### 2-1. この章で学ぶこと

- データベースのテーブル作成（マイグレーション）
- モデルの作成と役割
- コントローラーでデータ取得
- ビューでデータ表示

**💡 注意：この章ではまずシンプルな構造でタスクテーブルを作成します。第6章でユーザー認証を追加する際に、user_idカラムを追加します。**

### 2-2. データベースとモデルの作成

#### ① モデルとマイグレーションを同時に作成

```bash
php artisan make:model Task -m
```

**📁 生成されるファイル：**
- `app/Models/Task.php` ← モデル
- `database/migrations/xxxx_create_tasks_table.php` ← マイグレーション

#### ② マイグレーションファイルを編集

`database/migrations/xxxx_create_tasks_table.php`

```php
public function up(): void
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');              // タイトル
        $table->text('description')->nullable(); // 説明（任意）
        $table->timestamps();                 // created_at, updated_at
        // 注：user_idは第6章で追加します
    });
}
```

**💡 これにより、データベースにtasksテーブルが作成されます**

#### ③ マイグレーション実行

```bash
php artisan migrate
```

**✅ これでデータベースにテーブルができました！**

#### ④ モデルの設定

`app/Models/Task.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // 一括代入を許可するカラム
    protected $fillable = [
        'title',
        'description',
    ];
}
```

**💡 Modelの役割：データベースとのやり取りを簡単にする**

### 2-3. コントローラーの作成

#### ① コントローラーを作成

```bash
php artisan make:controller TaskController --resource
```

**📁 生成されるファイル：**
- `app/Http/Controllers/TaskController.php`

#### ② コントローラーにタスク一覧表示の処理を書く

`app/Http/Controllers/TaskController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * タスク一覧を表示
     */
    public function index()
    {
        // データベースから全タスクを取得（新しい順）
        $tasks = Task::orderBy('created_at', 'desc')->get();

        // ビューにデータを渡して表示
        return view('tasks.index', compact('tasks'));
    }
}
```

**💡 Controllerの役割：Modelからデータを取得し、Viewに渡す**

### 2-4. ルートの設定

`routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// トップページにアクセスしたらタスク一覧にリダイレクト
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// タスク関連のルートを一括登録
Route::resource('tasks', TaskController::class);
```

**💡 Route（ルート）の役割：URLとコントローラーの処理を紐付ける**

**resourceメソッドで自動生成されるルート：**
```
GET    /tasks          → index()   タスク一覧
GET    /tasks/create   → create()  タスク作成画面
POST   /tasks          → store()   タスク保存
GET    /tasks/{id}     → show()    タスク詳細
GET    /tasks/{id}/edit → edit()   タスク編集画面
PUT    /tasks/{id}     → update()  タスク更新
DELETE /tasks/{id}     → destroy() タスク削除
```

### 2-5. ビューの作成

#### ① レイアウトファイルを作成

`resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TODO App')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        header {
            background: #4c51bf;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: #4c51bf;
            color: white;
        }

        .btn-primary:hover {
            background: #434190;
        }

        .btn-success {
            background: #48bb78;
            color: white;
        }

        .btn-danger {
            background: #f56565;
            color: white;
        }

        .task-item {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .task-item:hover {
            background: #f7fafc;
        }

        .task-title {
            flex: 1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2d3748;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e0;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #48bb78;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container">
        <header>
            <h1>📝 My TODO App</h1>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>
</html>
```

**💡 Viewの役割：HTMLを生成してブラウザに表示**

#### ② タスク一覧画面を作成

`resources/views/tasks/index.blade.php`

```blade
@extends('layouts.app')

@section('title', 'タスク一覧')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            ➕ 新しいタスクを追加
        </a>
    </div>

    @if($tasks->isEmpty())
        <p style="text-align: center; color: #718096; padding: 40px;">
            タスクがありません。新しいタスクを追加してください。
        </p>
    @else
        <div class="task-list">
            @foreach($tasks as $task)
                <div class="task-item">
                    <div class="task-title">
                        <strong>{{ $task->title }}</strong>
                        @if($task->description)
                            <br>
                            <small style="color: #718096;">{{ $task->description }}</small>
                        @endif
                    </div>

                    <div>
                        <a href="{{ route('tasks.edit', $task) }}"
                           class="btn"
                           style="background: #4299e1; color: white; padding: 5px 10px;">
                            編集
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
```

### 2-6. 動作確認

```bash
php artisan serve
```

ブラウザで http://localhost:8000 にアクセス

**現時点ではタスクがないので「タスクがありません」と表示されます。次の章で追加機能を実装します。**

### 2-7. この章のまとめ

**🌐 この章でのWeb通信：**
1. ブラウザで `/tasks` にアクセス **→ HTTPリクエスト（Web通信発生）**
2. サーバー（Laravel）が処理
   - ルート → Controller → Model → Database → Model → Controller → View
3. HTMLを生成してブラウザに返す **→ HTTPレスポンス（Web通信発生）**
4. ブラウザがHTMLを表示

**理解度チェック：**
- [ ] MVCそれぞれの役割を説明できる
- [ ] マイグレーションでテーブルを作成できる
- [ ] Controllerでデータを取得する方法を理解している
- [ ] Viewでデータを表示する方法を理解している

### 🔄 MVCの流れ（具体例）
**例：タスク一覧ページを表示する**
```
① ユーザーが「/tasks」にアクセス
         ↓
② Route（routes/web.php）がリクエストを受け取る
   Route::get('/tasks', [TaskController::class, 'index']);
         ↓
③ Controller（TaskController@index）が動く
   - Modelに「データちょうだい」と依頼
         ↓
④ Model（Task）がデータベースから取得
   - SELECT * FROM tasks
         ↓
⑤ ControllerがModelから受け取ったデータをViewに渡す
         ↓
⑥ View（tasks/index.blade.php）がHTMLを生成
         ↓
⑦ ブラウザに表示！
```

### 🗄️ マイグレーションでテーブルを作成する方法
マイグレーションとは？
→データベースの設計図
- テーブルの構造をコードで管理
- チーム開発でDB構造を共有できる
- 変更履歴が残る

#### 📝 Step1：マイグレーションファイルを作成
#### 🔧 Step2：テーブル構造を定義
#### 🚀 Step3：マイグレーションを実行


### 🎯 まとめ：MVCの流れ完全版
#### 実例：タスク一覧ページの完全な流れ
1️⃣ ルート定義（routes/web.php）
```php
Route::get('/tasks', [TaskController::class, 'index']);
```

2️⃣ コントローラー（app/Http/Controllers/TaskController.php）
```php
public function index()
{
    // Modelからデータ取得
    $tasks = Task::orderBy('created_at', 'desc')->get();
    
    // Viewにデータを渡す
    return view('tasks.index', compact('tasks'));
}
```

3️⃣ モデル（app/Models/Task.php）
```php
class Task extends Model
    {
        protected $fillable = ['title', 'description', 'completed'];
    }
```

4️⃣ ビュー（resources/views/tasks/index.blade.php）
```php
blade<h1>タスク一覧</h1>
@foreach($tasks as $task)
    <p>{{ $task->title }}</p>
@endforeach
```

---

## 第3章｜タスク追加の実装（Create）

### 3-1. この章で学ぶこと

- フォームの作成
- フォームデータの受け取り
- データベースへの保存
- バリデーション（入力チェック）

### 3-2. タスク作成画面のビュー

`resources/views/tasks/create.blade.php`

```blade
@extends('layouts.app')

@section('title', '新しいタスク')

@section('content')
    <h2 style="margin-bottom: 20px;">新しいタスクを作成</h2>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">タイトル *</label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title') }}"
                   required
                   placeholder="例：買い物リストを作成">
        </div>

        <div class="form-group">
            <label for="description">説明（任意）</label>
            <textarea name="description"
                      id="description"
                      placeholder="タスクの詳細を入力してください">{{ old('description') }}</textarea>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('tasks.index') }}" class="btn"
               style="background: #e2e8f0; color: #2d3748;">
                キャンセル
            </a>
            <button type="submit" class="btn btn-primary">
                保存
            </button>
        </div>
    </form>
@endsection
```

**💡 重要な要素：**
- `action="{{ route('tasks.store') }}"` → 送信先のURL
- `method="POST"` → POST方式で送信
- `@csrf` → セキュリティトークン（必須）

### 3-3. コントローラーに保存処理を追加

`app/Http/Controllers/TaskController.php`

```php
/**
 * タスク作成画面を表示
 */
public function create()
{
    return view('tasks.create');
}

/**
 * タスクを保存
 */
public function store(Request $request)
{
    // バリデーション（入力チェック）
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
    ]);

    // データベースに保存
    Task::create($validated);

    // 一覧画面にリダイレクト（成功メッセージ付き）
    return redirect()->route('tasks.index')
        ->with('success', 'タスクを作成しました！');
}
```

**💡 バリデーションルール：**
- `required` → 必須入力
- `max:255` → 最大255文字
- `nullable` → 空でもOK

### 3-4. 動作確認

1. ブラウザで http://localhost:8000 にアクセス
2. 「新しいタスクを追加」ボタンをクリック
3. タイトルと説明を入力
4. 「保存」ボタンをクリック
5. タスク一覧に追加されたタスクが表示される

### 3-5. この章のまとめ

**🌐 この章でのWeb通信：**

**【タスク作成画面の表示】**
1. 「新しいタスクを追加」リンクをクリック
2. **→ HTTPリクエスト（Web通信発生）** `GET /tasks/create`
3. サーバーが作成画面のHTMLを生成
4. **→ HTTPレスポンス（Web通信発生）** HTMLを返す
5. ブラウザがフォームを表示

**【タスクの保存】**
1. フォームに入力して「保存」ボタンをクリック
2. **→ HTTPリクエスト（Web通信発生）** `POST /tasks` + フォームデータ
3. サーバーがデータをチェック・保存
4. **→ HTTPレスポンス（Web通信発生）** リダイレクト指示
5. ブラウザが自動的に一覧画面にアクセス
6. **→ HTTPリクエスト（Web通信発生）** `GET /tasks`
7. **→ HTTPレスポンス（Web通信発生）** 一覧画面のHTML

**理解度チェック：**
- [ ] フォームでデータを送信できる
- [ ] POSTメソッドとGETメソッドの違いを説明できる
- [ ] バリデーションの役割を理解している
- [ ] リダイレクトの仕組みを理解している

---

## 第4章｜タスク編集の実装（Update）

### 4-1. この章で学ぶこと

- 特定のタスクデータの取得
- フォームに既存データを表示
- データの更新

### 4-2. タスク編集画面のビュー

`resources/views/tasks/edit.blade.php`

```blade
@extends('layouts.app')

@section('title', 'タスクの編集')

@section('content')
    <h2 style="margin-bottom: 20px;">タスクを編集</h2>

    <form action="{{ route('tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">タイトル *</label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title', $task->title) }}"
                   required>
        </div>

        <div class="form-group">
            <label for="description">説明（任意）</label>
            <textarea name="description"
                      id="description">{{ old('description', $task->description) }}</textarea>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('tasks.index') }}" class="btn"
               style="background: #e2e8f0; color: #2d3748;">
                キャンセル
            </a>
            <button type="submit" class="btn btn-primary">
                更新
            </button>
        </div>
    </form>
@endsection
```

**💡 新しい要素：**
- `@method('PUT')` → 更新を表すHTTPメソッド
- `old('title', $task->title)` → エラー時は入力値、通常時は既存値を表示

### 4-3. コントローラーに編集処理を追加

`app/Http/Controllers/TaskController.php`

```php
/**
 * タスク編集画面を表示
 */
public function edit(Task $task)
{
    return view('tasks.edit', compact('task'));
}

/**
 * タスクを更新
 */
public function update(Request $request, Task $task)
{
    // バリデーション
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
    ]);

    // データベースを更新
    $task->update($validated);

    // 一覧画面にリダイレクト
    return redirect()->route('tasks.index')
        ->with('success', 'タスクを更新しました！');
}
```

**💡 `Task $task` の仕組み：**
- URLの `/tasks/1/edit` にアクセスすると
- Laravelが自動的にID=1のTaskをデータベースから取得
- `$task` 変数に格納してくれる（ルートモデルバインディング）

### 4-4. 動作確認

1. タスク一覧で「編集」ボタンをクリック
2. タイトルや説明を変更
3. 「更新」ボタンをクリック
4. 一覧画面で変更が反映されていることを確認

### 4-5. この章のまとめ

**🌐 この章でのWeb通信：**

**【編集画面の表示】**
1. 一覧画面の「編集」リンクをクリック
2. **→ HTTPリクエスト（Web通信発生）** `GET /tasks/1/edit`
3. サーバーがデータベースからタスクを取得
4. 編集画面のHTMLを生成
5. **→ HTTPレスポンス（Web通信発生）** HTMLを返す
6. ブラウザがフォーム（既存データ入り）を表示

**【タスクの更新】**
1. フォームを編集して「更新」ボタンをクリック
2. **→ HTTPリクエスト（Web通信発生）** `PUT /tasks/1` + フォームデータ
3. サーバーがデータをチェック・更新
4. **→ HTTPレスポンス（Web通信発生）** リダイレクト指示
5. ブラウザが一覧画面にアクセス
6. **→ HTTPリクエスト（Web通信発生）** `GET /tasks`
7. **→ HTTPレスポンス（Web通信発生）** 一覧画面のHTML

**理解度チェック：**
- [ ] PUTメソッドの役割を理解している
- [ ] ルートモデルバインディングの仕組みを理解している
- [ ] old()ヘルパーの使い方を理解している

---

## 第5章｜タスク削除の実装（Delete）

### 5-1. この章で学ぶこと

- データの削除
- JavaScriptでの確認ダイアログ
- フォームの隠しフィールド

### 5-2. 編集画面に削除ボタンを追加

`resources/views/tasks/edit.blade.php` を更新

```blade
@extends('layouts.app')

@section('title', 'タスクの編集')

@section('content')
    <h2 style="margin-bottom: 20px;">タスクを編集</h2>

    <form action="{{ route('tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">タイトル *</label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title', $task->title) }}"
                   required>
        </div>

        <div class="form-group">
            <label for="description">説明（任意）</label>
            <textarea name="description"
                      id="description">{{ old('description', $task->description) }}</textarea>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: space-between;">
            <div>
                <a href="{{ route('tasks.index') }}" class="btn"
                   style="background: #e2e8f0; color: #2d3748;">
                    キャンセル
                </a>
                <button type="submit" class="btn btn-primary">
                    更新
                </button>
            </div>

            <!-- 削除ボタンを追加 -->
            <button type="button"
                    onclick="if(confirm('本当に削除しますか？')) { document.getElementById('delete-form').submit(); }"
                    class="btn btn-danger">
                削除
            </button>
        </div>
    </form>

    <!-- 削除用フォーム（非表示） -->
    <form id="delete-form" action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection
```

**💡 削除の仕組み：**
1. 削除ボタンをクリック
2. JavaScriptで確認ダイアログ表示
3. OKならば隠しフォームを送信
4. DELETE メソッドでサーバーに送信

### 5-3. コントローラーに削除処理を追加

`app/Http/Controllers/TaskController.php`

```php
/**
 * タスクを削除
 */
public function destroy(Task $task)
{
    // データベースから削除
    $task->delete();

    // 一覧画面にリダイレクト
    return redirect()->route('tasks.index')
        ->with('success', 'タスクを削除しました！');
}
```

### 5-4. 動作確認

1. タスクの編集画面を開く
2. 「削除」ボタンをクリック
3. 確認ダイアログで「OK」をクリック
4. 一覧画面でタスクが削除されていることを確認

### 5-5. この章のまとめ

**🌐 この章でのWeb通信：**

1. 「削除」ボタンをクリック
2. 確認ダイアログで「OK」をクリック
3. **→ HTTPリクエスト（Web通信発生）** `DELETE /tasks/1`
4. サーバーがデータベースからタスクを削除
5. **→ HTTPレスポンス（Web通信発生）** リダイレクト指示
6. ブラウザが一覧画面にアクセス
7. **→ HTTPリクエスト（Web通信発生）** `GET /tasks`
8. **→ HTTPレスポンス（Web通信発生）** 一覧画面のHTML

**理解度チェック：**
- [ ] DELETEメソッドの役割を理解している
- [ ] JavaScriptとフォームの連携を理解している
- [ ] CRUD操作すべてを実装できる

---

# 第6章｜ユーザー認証機能の実装（新規プロジェクト）

**目標：Laravel Breezeで認証付きToDoアプリを最初から作る**

---

## 🗺️ この章の全体像

| セクション | 内容 | 所要時間 |
|---|---|---|
| 6-1 | なぜ新しいプロジェクトで作るのか | 15分 |
| 6-2 | プロジェクト作成とBreeze導入 | 30分 |
| 6-3 | Breezeの構造を理解する | 30分 |
| 6-4 | タスクテーブルの作成 | 30分 |
| 6-5 | タスクのCRUD実装 | 2時間 |
| 6-6 | 完成版の確認 | 30分 |

**合計学習時間：約4.5時間**

---

## 💡 6-1. なぜ新しいプロジェクトで作るのか？

### 第5章までと第6章の違い

| 項目 | 第5章まで | 第6章（これから） |
|---|---|---|
| **プロジェクト** | `todo-app` | `todo-auth`（新規） |
| **認証** | なし | あり（Laravel Breeze） |
| **レイアウト** | 自作の`@extends` | Breezeの`<x-app-layout>` |
| **スタイル** | 自作CSS | TailwindCSS |
| **目的** | Laravel基礎理解 | 認証付き本格アプリ |

### なぜ分けるのか？

**理由1：レイアウトシステムが異なる**
```blade
<!-- 第5章までの方式 -->
@extends('layouts.app')
@section('content')
    <h1>タスク一覧</h1>
@endsection

<!-- Breezeの方式（第6章） -->
<x-app-layout>
    <x-slot name="header">
        <h2>タスク一覧</h2>
    </x-slot>
    <div class="py-12">
        <!-- コンテンツ -->
    </div>
</x-app-layout>
```

**理由2：最初からBreezeを入れた方が簡単**
- 後から追加すると既存コードとの調整が必要
- 新規プロジェクトなら設定がクリーン

**理由3：実務でもよくあるパターン**
- 練習用の簡易版 → 本番用の完成版という流れ

---

## 🚀 6-2. プロジェクト作成とBreeze導入

### Step1: 新規プロジェクト作成
```bash
# 作業ディレクトリに移動
cd ~/Desktop

# 新しいプロジェクト作成
composer create-project laravel/laravel todo-auth

# プロジェクトに移動
cd todo-auth
```

### Step2: データベース設定

`.env`ファイルを編集
```env
DB_CONNECTION=sqlite
# 以下をコメントアウト
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

データベースファイルを作成
```bash
touch database/database.sqlite
```

### Step3: Laravel Breezeのインストール
```bash
# Breezeパッケージをインストール
composer require laravel/breeze --dev

# Breezeをセットアップ（Bladeテンプレートを選択）
php artisan breeze:install blade

# Node.jsの依存関係をインストール
npm install

# CSSとJavaScriptをビルド
npm run dev
```

**別のターミナルを開いて**、`npm run dev`を実行し続ける（開発中はずっと起動）

### Step4: マイグレーション実行

最初のターミナルに戻って
```bash
# データベースにテーブルを作成
php artisan migrate

# サーバー起動
php artisan serve
```

### Step5: 動作確認

ブラウザで以下にアクセス

1. `http://localhost:8000` → トップページ
2. 右上の「Register」→ 新規ユーザー登録
3. ログイン後、ダッシュボードが表示されればOK！

---

## 🔍 6-3. Breezeの構造を理解する

### 自動生成されたファイル
```
todo-auth/
├── app/
│   └── Http/
│       └── Controllers/
│           └── Auth/               # 認証関連のコントローラー
│               ├── LoginController.php
│               └── RegisterController.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php       # メインレイアウト
│       │   ├── guest.blade.php     # ログイン前のレイアウト
│       │   └── navigation.blade.php # ナビゲーションバー
│       ├── auth/
│       │   ├── login.blade.php     # ログインページ
│       │   └── register.blade.php  # 登録ページ
│       └── dashboard.blade.php     # ダッシュボード
└── routes/
    └── auth.php                    # 認証関連のルート
```

### レイアウトファイルの中身を見てみよう

`resources/views/layouts/app.blade.php`（一部抜粋）
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
```

**重要なポイント：**
- `{{ $header }}` → ページのヘッダー部分
- `{{ $slot }}` → ページのメインコンテンツ
- `@include('layouts.navigation')` → ナビゲーションバーを読み込み

### ダッシュボードの中身を見てみよう

`resources/views/dashboard.blade.php`
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

**解説：**
1. `<x-app-layout>` → `layouts/app.blade.php`を使う
2. `<x-slot name="header">` → ヘッダー部分の内容
3. `<div class="py-12">` → メインコンテンツ

---

## 🗄️ 6-4. タスクテーブルの作成

### Step1: マイグレーション作成
```bash
php artisan make:migration create_tasks_table
```

`database/migrations/YYYY_MM_DD_HHMMSS_create_tasks_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

**重要：**
- `user_id` → どのユーザーのタスクかを記録
- `constrained()` → usersテーブルと紐付け
- `onDelete('cascade')` → ユーザーが削除されたらタスクも削除
```bash
# マイグレーション実行
php artisan migrate
```

### Step2: Taskモデル作成
```bash
php artisan make:model Task
```

`app/Models/Task.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'completed'
    ];

    protected $casts = [
        'completed' => 'boolean'
    ];

    // リレーション：このタスクの所有者
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Step3: Userモデルにリレーション追加

`app/Models/User.php`に追加
```php
// 既存のコードの下に追加
public function tasks()
{
    return $this->hasMany(Task::class);
}
```

---

## 📝 6-5. タスクのCRUD実装

### Step1: TaskControllerを作成
```bash
php artisan make:controller TaskController --resource
```

### Step2: ルート設定

`routes/web.php`
```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;  // 追加
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // タスク機能（ログイン必須）
    Route::resource('tasks', TaskController::class);
});

require __DIR__.'/auth.php';
```

### Step3: TaskController実装

`app/Http/Controllers/TaskController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 一覧表示
    public function index()
    {
        $tasks = auth()->user()->tasks()
            ->orderBy('completed')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('tasks.index', compact('tasks'));
    }

    // 作成フォーム表示
    public function create()
    {
        return view('tasks.create');
    }

    // 保存処理
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000'
        ]);

        auth()->user()->tasks()->create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを作成しました');
    }

    // 編集フォーム表示
    public function edit(Task $task)
    {
        // 自分のタスクかチェック
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        return view('tasks.edit', compact('task'));
    }

    // 更新処理
    public function update(Request $request, Task $task)
    {
        // 自分のタスクかチェック
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|max:1000',
            'completed' => 'boolean'
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'completed' => $request->has('completed')
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを更新しました');
    }

    // 削除処理
    public function destroy(Task $task)
    {
        // 自分のタスクかチェック
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを削除しました');
    }
}
```

### Step4: ビューファイル作成

#### タスク一覧ページ
```bash
mkdir resources/views/tasks
```

`resources/views/tasks/index.blade.php`
```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Tasks') }}
            </h2>
            <a href="{{ route('tasks.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新規作成
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 成功メッセージ -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- タスクがない場合 -->
            @if ($tasks->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <p class="mb-4">まだタスクがありません</p>
                        <a href="{{ route('tasks.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            最初のタスクを作成
                        </a>
                    </div>
                </div>
            @else
                <!-- タスク一覧 -->
                <div class="space-y-4">
                    @foreach ($tasks as $task)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg 
                                    {{ $task->completed ? 'opacity-60' : '' }}">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold {{ $task->completed ? 'line-through' : '' }}">
                                            {{ $task->title }}
                                        </h3>
                                        @if ($task->description)
                                            <p class="mt-2 text-gray-600">{{ $task->description }}</p>
                                        @endif
                                        <p class="mt-2 text-sm text-gray-500">
                                            作成日: {{ $task->created_at->format('Y/m/d H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('tasks.edit', $task) }}" 
                                           class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                            編集
                                        </a>
                                        <form action="{{ route('tasks.destroy', $task) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('本当に削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
```

#### タスク作成ページ

`resources/views/tasks/create.blade.php`
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <!-- タイトル -->
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-bold mb-2">
                                タイトル <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror"
                                   required>
                            @error('title')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 説明 -->
                        <div class="mb-6">
                            <label for="description" class="block text-gray-700 font-bold mb-2">
                                説明
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="flex items-center justify-between">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                作成
                            </button>
                            <a href="{{ route('tasks.index') }}" 
                               class="text-gray-600 hover:text-gray-800">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

#### タスク編集ページ

`resources/views/tasks/edit.blade.php`
```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- タイトル -->
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 font-bold mb-2">
                                タイトル <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title', $task->title) }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror"
                                   required>
                            @error('title')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 説明 -->
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 font-bold mb-2">
                                説明
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 完了チェックボックス -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="completed" 
                                       value="1"
                                       {{ old('completed', $task->completed) ? 'checked' : '' }}
                                       class="mr-2">
                                <span class="text-gray-700">完了</span>
                            </label>
                        </div>

                        <!-- ボタン -->
                        <div class="flex items-center justify-between">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                更新
                            </button>
                            <a href="{{ route('tasks.index') }}" 
                               class="text-gray-600 hover:text-gray-800">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### Step5: ナビゲーションにタスクリンクを追加

`resources/views/layouts/navigation.blade.php`の`<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">`内に追加
```blade
<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>
    
    <!-- タスクリンクを追加 -->
    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
        {{ __('Tasks') }}
    </x-nav-link>
</div>
```

---

## ✅ 6-6. 完成版の確認

### 動作確認の手順

1. **サーバーが起動しているか確認**
```bash
   php artisan serve
```
   別ターミナルで
```bash
   npm run dev
```

2. **ブラウザでアクセス**
   - `http://localhost:8000/tasks`

3. **テストの流れ**
   - [ ] ログインしていない状態で`/tasks`にアクセス → ログインページにリダイレクト
   - [ ] ログイン後、ナビゲーションバーに「Tasks」が表示される
   - [ ] 「新規作成」からタスクを作成できる
   - [ ] タスク一覧が表示される
   - [ ] タスクを編集できる
   - [ ] 完了チェックをつけると見た目が変わる
   - [ ] タスクを削除できる

4. **複数ユーザーでテスト**
   - 別のユーザーでログイン
   - タスクが分離されていることを確認

---

## 🎯 この章のゴール確認

- [ ] Laravel Breezeをインストールできた
- [ ] ユーザー登録・ログインができる
- [ ] ログインしたユーザーだけがタスクを操作できる
- [ ] タスクのCRUD（作成・読取・更新・削除）ができる
- [ ] ユーザーごとにタスクが分離されている
- [ ] Breezeのレイアウト（`<x-app-layout>`）を使ってビューを作成できる

---

## 💡 重要ポイントのまとめ

### 1. 認証の仕組み
```php
// ログイン中のユーザーを取得
auth()->user()

// ログイン中のユーザーのID
auth()->id()

// ログイン中のユーザーのタスクだけを取得
auth()->user()->tasks()
```

### 2. ミドルウェアで保護
```php
// routes/web.php
Route::middleware('auth')->group(function () {
    // この中のルートはログイン必須
    Route::resource('tasks', TaskController::class);
});
```

### 3. 自分のデータかチェック
```php
// TaskController.php
if ($task->user_id !== auth()->id()) {
    abort(403);  // 403 Forbidden
}
```

### 4. Breezeのレイアウト
```blade
<x-app-layout>
    <x-slot name="header">
        <!-- ページタイトル -->
    </x-slot>

    <div class="py-12">
        <!-- メインコンテンツ -->
    </div>
</x-app-layout>
```

---

## 🚀 次のステップへ

お疲れさまでした！認証付きのToDoアプリが完成しました。

**この章で習得したこと：**
- Laravel Breezeの導入
- ユーザー認証の実装
- ログインユーザーごとのデータ管理
- TailwindCSSを使ったスタイリング
- セキュリティの基本（他人のデータにアクセスさせない）

---

## 🎯 全体のまとめ

### 完成した機能

**CRUD機能**

| 機能 | HTTPメソッド | URL | Controllerメソッド |
|---|---|---|---|
| 一覧表示 | GET | /tasks | index() |
| 作成画面 | GET | /tasks/create | create() |
| 保存 | POST | /tasks | store() |
| 編集画面 | GET | /tasks/{id}/edit | edit() |
| 更新 | PUT | /tasks/{id} | update() |
| 削除 | DELETE | /tasks/{id} | destroy() |

**認証機能**

| 機能 | HTTPメソッド | URL |
|---|---|---|
| ユーザー登録画面 | GET | /register |
| ユーザー登録処理 | POST | /register |
| ログイン画面 | GET | /login |
| ログイン処理 | POST | /login |
| ログアウト処理 | POST | /logout |

### Web通信が発生するタイミングのまとめ

**Web通信は「ブラウザとサーバーの間」で発生します：**

1. **ページ表示時**
   - ブラウザ → サーバー：HTTPリクエスト
   - サーバー → ブラウザ：HTMLレスポンス

2. **フォーム送信時**
   - ブラウザ → サーバー：HTTPリクエスト + データ
   - サーバー → ブラウザ：リダイレクト or HTMLレスポンス

3. **リダイレクト後**
   - ブラウザ → サーバー：新しいHTTPリクエスト
   - サーバー → ブラウザ：HTMLレスポンス

**サーバー内部（Model ↔ Controller ↔ View）ではWeb通信は発生しません**

### 最終理解度チェック

**以下の質問に答えられますか？**

**MVCフレームワーク**
- [ ] Model, View, Controllerそれぞれの役割は？
- [ ] ブラウザでURLにアクセスしてから画面が表示されるまでの流れは？
- [ ] どのタイミングでWeb通信が発生する？
- [ ] データベースにアクセスするのはどの層？
- [ ] HTMLを生成するのはどの層？
- [ ] ルーティングの役割は？

**CRUD操作**
- [ ] Create（作成）、Read（読み取り）、Update（更新）、Delete（削除）をすべて実装できる
- [ ] GET、POST、PUT、DELETEメソッドの使い分けができる
- [ ] バリデーションを実装できる

**ユーザー認証**
- [ ] Laravel Breezeで認証機能を追加できる
- [ ] リレーション（belongsTo, hasMany）を理解している
- [ ] ログイン中のユーザーのデータのみ取得できる
- [ ] 他のユーザーのデータにアクセスできないようにする方法を理解している

---


## 📚 参考資料

### 公式ドキュメント
- [Laravel公式ドキュメント](https://laravel.com/docs)
- [Laravel日本語ドキュメント](https://readouble.com/laravel/)

### 学習リソース
- [Laracasts](https://laracasts.com/) - 動画チュートリアル
- [Laravel Daily](https://laraveldaily.com/) - Tips & Tricks

---

**🎉 お疲れ様でした！**

MVCフレームワークの基本を理解し、ユーザー認証付きのTODOアプリが完成しました。

**このカリキュラムで学んだこと：**
- MVCアーキテクチャの基礎
- CRUD操作の実装（Create, Read, Update, Delete）
- データベースとの連携（マイグレーション、モデル、リレーション）
- ユーザー認証とセッション管理
- Web通信の仕組み

ここで学んだ基礎を土台に、より高度な機能を追加していきましょう！
