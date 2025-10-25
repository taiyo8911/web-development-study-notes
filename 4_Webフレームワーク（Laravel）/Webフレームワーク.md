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

## 📚 カリキュラム構成（全5章）

| 章 | テーマ | 学習内容 | 学習時間 |
|---|---|---|---|
| 第1章 | MVCの基礎理解 | MVC概念・環境構築 | 2時間 |
| 第2章 | 一覧表示（Read） | データベースからデータ取得・表示 | 2時間 |
| 第3章 | 追加機能（Create） | フォームからデータ保存 | 2時間 |
| 第4章 | 編集機能（Update） | データの更新 | 2時間 |
| 第5章 | 削除機能（Delete） | データの削除 | 1時間 |

**合計学習時間：約9時間**

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

**今回作成するtasksテーブル（シンプル版）**

```sql
tasks テーブル
├── id (主キー・自動採番)
├── title (タスクのタイトル)
├── description (タスクの説明・任意)
├── created_at (作成日時・自動)
└── updated_at (更新日時・自動)
```

**削除した項目：**
- ~~due_date (期限)~~
- ~~priority (優先度)~~
- ~~completed (完了フラグ)~~
- ~~user_id (ユーザー紐付け)~~

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

---

## 第2章｜タスク一覧の実装（Read）

### 2-1. この章で学ぶこと

- データベースのテーブル作成（マイグレーション）
- モデルの作成と役割
- コントローラーでデータ取得
- ビューでデータ表示

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

## 🎯 全体のまとめ

### 完成したCRUD機能

| 機能 | HTTPメソッド | URL | Controllerメソッド |
|---|---|---|---|
| 一覧表示 | GET | /tasks | index() |
| 作成画面 | GET | /tasks/create | create() |
| 保存 | POST | /tasks | store() |
| 編集画面 | GET | /tasks/{id}/edit | edit() |
| 更新 | PUT | /tasks/{id} | update() |
| 削除 | DELETE | /tasks/{id} | destroy() |

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

### MVCの理解度チェック

**以下の質問に答えられますか？**

- [ ] Model, View, Controllerそれぞれの役割は？
- [ ] ブラウザでURLにアクセスしてから画面が表示されるまでの流れは？
- [ ] どのタイミングでWeb通信が発生する？
- [ ] データベースにアクセスするのはどの層？
- [ ] HTMLを生成するのはどの層？
- [ ] ルーティングの役割は？

---

## 🚀 次のステップ

このシンプルなTODOアプリを理解できたら、次のステップに進みましょう：

### レベル1：機能追加

1. **完了フラグの追加**
   - tasksテーブルに `completed` カラムを追加
   - 完了/未完了を切り替える機能

2. **期限の追加**
   - tasksテーブルに `due_date` カラムを追加
   - 期限を表示

3. **優先度の追加**
   - tasksテーブルに `priority` カラムを追加
   - 優先度で色分け

### レベル2：認証機能

1. **Laravel Breezeで認証追加**
   - ユーザー登録・ログイン機能
   - ユーザーごとのタスク管理

### レベル3：高度な機能

1. **検索・フィルター機能**
2. **カテゴリー・タグ機能**
3. **API開発**
4. **テストコードの作成**

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

MVCフレームワークの基本を理解し、シンプルなTODOアプリが完成しました。
ここで学んだ基礎を土台に、より高度な機能を追加していきましょう！
