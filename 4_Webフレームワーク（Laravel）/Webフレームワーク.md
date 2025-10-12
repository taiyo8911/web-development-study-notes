**目標：LaravelでMVCアーキテクチャを理解し、ToDoアプリを作成する**

---

## 🗺️ 学習の全体像

| 章 | 内容 | 実践内容 | 所要時間 |
| --- | --- | --- | --- |
| 第1章 | Laravelとフレームワーク | なぜフレームワークを使うのか | 1時間 |
| 第2章 | 環境構築 | Composerインストール・プロジェクト作成 | 2時間 |
| 第3章 | MVCの基本 | ルーティング・コントローラー・ビュー | 2.5時間 |
| 第4章 | データベース連携 | マイグレーション・モデル・Eloquent | 3時間 |
| 第5章 | CRUD機能の実装 | 作成・読取・更新・削除 | 3時間 |
| 第6章 | バリデーション | データの検証とエラー表示 | 1.5時間 |
| 第7章 | 認証機能 | ログイン・ログアウト | 2時間 |
| 第8章 | 総合演習：ToDoアプリ | 学んだことを統合して実装 | 5時間 |

**合計学習時間：約20時間（4週間）**

---

## 第1章｜Laravelとフレームワーク

### 💡 1-1. フレームワークとは？

**フレームワーク = アプリ開発の「骨組み」**

```
[生PHP]              [Laravel]
自分で全部書く    →    骨組みが用意されている
└ 大変・時間かかる      └ 早い・安全
```

### 🤔 1-2. なぜLaravelを使うのか？

**生PHPとの比較：**

| 機能 | 生PHP | Laravel |
| --- | --- | --- |
| **ルーティング** | 自分で書く | `Route::get()`で簡単 |
| **DB接続** | `mysqli_connect()`を毎回 | 設定ファイルで一元管理 |
| **SQL** | 文字列で書く（危険） | Eloquentで安全 |
| **セキュリティ** | 自分で対策 | CSRF保護が標準装備 |
| **ファイル構成** | 自由（混乱しがち） | MVC構造で整理される |

**例：データベースからユーザー一覧を取得**

```php
// 生PHP（大変）
$conn = mysqli_connect('localhost', 'user', 'password', 'database');
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['name'];
}
mysqli_close($conn);

// Laravel（簡単！）
$users = User::all();
foreach ($users as $user) {
    echo $user->name;
}
```

### 📊 1-3. MVCアーキテクチャとは？

**MVC = Model-View-Controller**
アプリを3つの役割に分ける設計パターン

```
[ユーザー]
    ↓
[ルート] → リクエストを受け取る
    ↓
[Controller] → 処理の司令塔
    ↓         ↓
[Model]    [View]
データ管理   画面表示
    ↓         ↓
[Database] → [HTML]
```

| 役割 | 担当 | 例 |
| --- | --- | --- |
| **Model（モデル）** | データベースとやり取り | User.php → usersテーブル |
| **View（ビュー）** | 画面の表示 | welcome.blade.php |
| **Controller（コントローラー）** | 処理の制御 | UserController.php |

**具体例：ユーザー一覧ページ**

```
① ルート: GET /users → UserControllerのindexメソッドを呼ぶ
② Controller: Userモデルからデータを取得
③ Model: データベースからユーザー情報を取得
④ Controller: データをビューに渡す
⑤ View: HTMLを生成してブラウザに返す
```

---

## 第2章｜環境構築

### 💻 2-1. 必要なツール

**1. Homebrew（パッケージマネージャー）**

```bash
# Homebrewがインストール済みか確認
brew --version

# なければインストール
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

**2. PHP（バージョン8.1以上）**

```bash
# PHPのインストール
brew install php

# バージョン確認
php -v
```

**3. Composer（PHPのパッケージマネージャー）**

```bash
# Composerをインストール
brew install composer

# バージョン確認
composer --version
```

### 🚀 2-2. Laravelプロジェクトの作成

```bash
# 作業ディレクトリに移動
cd ~/Desktop

# Laravelプロジェクトを作成
composer create-project laravel/laravel todo-app

# プロジェクトに移動
cd todo-app

# 開発サーバーを起動
php artisan serve
```

**ブラウザで確認：** `http://localhost:8000`

Laravelのウェルカムページが表示されればOK！

### 📁 2-3. Laravelのディレクトリ構造

```
todo-app/
├── app/               # アプリのコア
│   ├── Http/
│   │   └── Controllers/  # コントローラー
│   └── Models/           # モデル
├── database/
│   └── migrations/       # マイグレーションファイル
├── resources/
│   └── views/            # ビュー（Bladeテンプレート）
├── routes/
│   └── web.php           # ルート定義
├── public/               # 公開ディレクトリ
├── .env                  # 環境設定
└── artisan               # コマンドラインツール
```

**覚えておくべき重要なフォルダ：**

- `app/Http/Controllers/` → コントローラーを置く
- `app/Models/` → モデルを置く
- `resources/views/` → ビューを置く
- `routes/web.php` → URLとコントローラーを紐付ける

---

## 第3章｜MVCの基本

### 🛣️ 3-1. ルーティング

**ルート = URLとコントローラーを紐付ける設定**

`routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;

// トップページ
Route::get('/', function () {
    return view('welcome');
});

// /helloにアクセスしたら"Hello, Laravel!"を表示
Route::get('/hello', function () {
    return 'Hello, Laravel!';
});

// パラメータ付き
Route::get('/user/{name}', function ($name) {
    return "こんにちは、{$name}さん";
});
```

**試してみよう：**

- `http://localhost:8000/hello`
- `http://localhost:8000/user/太郎`

### 🎨 3-2. ビュー（Blade）

**Blade = Laravelのテンプレートエンジン**

`resources/views/greeting.blade.php`

```blade
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>挨拶ページ</title>
</head>
<body>
    <h1>こんにちは、{{ $name }}さん！</h1>
    <p>今日は{{ $date }}です。</p>
</body>
</html>
```

`routes/web.php`

```php
Route::get('/greeting/{name}', function ($name) {
    return view('greeting', [
        'name' => $name,
        'date' => date('Y年m月d日')
    ]);
});
```

**Bladeの便利な機能：**

```blade
{{-- コメント --}}

{{-- 変数の表示（自動エスケープ） --}}
<p>{{ $message }}</p>

{{-- 条件分岐 --}}
@if($count > 10)
    <p>多い</p>
@else
    <p>少ない</p>
@endif

{{-- ループ --}}
@foreach($users as $user)
    <p>{{ $user->name }}</p>
@endforeach
```

### 🎮 3-3. コントローラー

**コントローラー = 処理をまとめる場所**

```bash
# コントローラーを作成
php artisan make:controller UserController
```

`app/Http/Controllers/UserController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // ユーザー一覧ページ
    public function index()
    {
        $users = ['太郎', '花子', '次郎'];
        return view('users.index', ['users' => $users]);
    }

    // ユーザー詳細ページ
    public function show($id)
    {
        return view('users.show', ['id' => $id]);
    }
}
```

`routes/web.php`

```php
use App\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
```

`resources/views/users/index.blade.php`

```blade
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー一覧</title>
</head>
<body>
    <h1>ユーザー一覧</h1>
    <ul>
        @foreach($users as $user)
            <li>{{ $user }}</li>
        @endforeach
    </ul>
</body>
</html>
```

### ✏️ 実践1：自己紹介ページを作る

**課題：** 以下を実装してください

1. `/profile`にアクセスすると自己紹介ページが表示される
2. ProfileControllerを作成
3. Bladeテンプレートで名前・年齢・趣味を表示

**ヒント：**

```bash
php artisan make:controller ProfileController
```

---

## 第4章｜データベース連携

### 🗄️ 4-1. データベースの設定

`.env`ファイルを編集

```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

**SQLiteを使う理由：**

- 設定が簡単（ファイル1つだけ）
- インストール不要
- 練習に最適

```bash
# データベースファイルを作成
touch database/database.sqlite
```

### 🔄 4-2. マイグレーション

**マイグレーション = データベースの設計図**

```bash
# tasksテーブルのマイグレーションを作成
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
            $table->id();                    // 主キー
            $table->string('title');         // タイトル
            $table->text('description')->nullable();  // 説明（任意）
            $table->boolean('completed')->default(false);  // 完了フラグ
            $table->timestamps();            // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

**マイグレーションを実行：**

```bash
php artisan migrate
```

### 📦 4-3. モデル

**モデル = データベースとやり取りするクラス**

```bash
# Taskモデルを作成
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

    // 複数代入を許可するカラム
    protected $fillable = [
        'title',
        'description',
        'completed'
    ];

    // 真偽値にキャストするカラム
    protected $casts = [
        'completed' => 'boolean'
    ];
}
```

### 🔍 4-4. Eloquent（データ操作）

**Eloquent = LaravelのORM（データベース操作を簡単にする仕組み）**

```php
// 全データ取得
$tasks = Task::all();

// 1件取得
$task = Task::find(1);

// 条件付き取得
$completed = Task::where('completed', true)->get();

// 作成
Task::create([
    'title' => '買い物',
    'description' => '牛乳を買う'
]);

// 更新
$task = Task::find(1);
$task->completed = true;
$task->save();

// 削除
$task = Task::find(1);
$task->delete();
```

### ✏️ 実践2：tinkerでデータ操作

**Tinker = Laravelの対話型シェル**

```bash
# tinkerを起動
php artisan tinker
```

```php
// タスクを作成
Task::create(['title' => 'Laravelを学ぶ', 'description' => 'MVCを理解する']);

// 全タスクを取得
Task::all();

// タスクを更新
$task = Task::find(1);
$task->completed = true;
$task->save();

// 確認
Task::all();

// 終了
exit
```

---

## 第5章｜CRUD機能の実装

### 📝 5-1. CRUD とは？

**CRUD = データ操作の基本4つ**

| 操作 | 意味 | HTTPメソッド | URL例 |
| --- | --- | --- | --- |
| **C**reate | 作成 | POST | /tasks |
| **R**ead | 読取 | GET | /tasks, /tasks/{id} |
| **U**pdate | 更新 | PUT/PATCH | /tasks/{id} |
| **D**elete | 削除 | DELETE | /tasks/{id} |

### 🎮 5-2. TaskController作成

```bash
# resourceオプションでCRUDメソッドが自動生成される
php artisan make:controller TaskController --resource
```

`routes/web.php`

```php
use App\Http\Controllers\TaskController;

Route::resource('tasks', TaskController::class);
```

**生成されるルート：**

```bash
php artisan route:list --name=tasks
```

| メソッド | URI | アクション | 用途 |
| --- | --- | --- | --- |
| GET | /tasks | index | 一覧表示 |
| GET | /tasks/create | create | 作成フォーム |
| POST | /tasks | store | 保存 |
| GET | /tasks/{id} | show | 詳細表示 |
| GET | /tasks/{id}/edit | edit | 編集フォーム |
| PUT | /tasks/{id} | update | 更新 |
| DELETE | /tasks/{id} | destroy | 削除 |

### 📋 5-3. 一覧表示（Read）

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
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('tasks.index', compact('tasks'));
    }
}
```

`resources/views/tasks/index.blade.php`

```blade
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タスク一覧</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .task-item {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .task-item.completed {
            background: #f0f0f0;
            text-decoration: line-through;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .btn-primary { background: #007bff; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <h1>📝 ToDoリスト</h1>
    
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">新規作成</a>

    @if($tasks->isEmpty())
        <p>タスクがありません</p>
    @else
        @foreach($tasks as $task)
            <div class="task-item {{ $task->completed ? 'completed' : '' }}">
                <div>
                    <h3>{{ $task->title }}</h3>
                    @if($task->description)
                        <p>{{ $task->description }}</p>
                    @endif
                </div>
                <div>
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-success">編集</a>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('削除しますか？')">削除</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</body>
</html>
```

### ➕ 5-4. 作成（Create）

`app/Http/Controllers/TaskController.php`

```php
// 作成フォーム表示
public function create()
{
    return view('tasks.create');
}

// 保存処理
public function store(Request $request)
{
    Task::create([
        'title' => $request->title,
        'description' => $request->description
    ]);

    return redirect()->route('tasks.index');
}
```

`resources/views/tasks/create.blade.php`

```blade
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タスク作成</title>
</head>
<body>
    <h1>新規タスク作成</h1>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div>
            <label>タイトル</label>
            <input type="text" name="title" required>
        </div>
        <div>
            <label>説明</label>
            <textarea name="description"></textarea>
        </div>
        <button type="submit">作成</button>
        <a href="{{ route('tasks.index') }}">戻る</a>
    </form>
</body>
</html>
```

### ✏️ 5-5. 編集（Update）

`app/Http/Controllers/TaskController.php`

```php
// 編集フォーム表示
public function edit(Task $task)
{
    return view('tasks.edit', compact('task'));
}

// 更新処理
public function update(Request $request, Task $task)
{
    $task->update([
        'title' => $request->title,
        'description' => $request->description,
        'completed' => $request->has('completed')
    ]);

    return redirect()->route('tasks.index');
}
```

`resources/views/tasks/edit.blade.php`

```blade
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タスク編集</title>
</head>
<body>
    <h1>タスク編集</h1>

    <form action="{{ route('tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div>
            <label>タイトル</label>
            <input type="text" name="title" value="{{ $task->title }}" required>
        </div>
        <div>
            <label>説明</label>
            <textarea name="description">{{ $task->description }}</textarea>
        </div>
        <div>
            <label>
                <input type="checkbox" name="completed" {{ $task->completed ? 'checked' : '' }}>
                完了
            </label>
        </div>
        <button type="submit">更新</button>
        <a href="{{ route('tasks.index') }}">戻る</a>
    </form>
</body>
</html>
```

### 🗑️ 5-6. 削除（Delete）

`app/Http/Controllers/TaskController.php`

```php
// 削除処理
public function destroy(Task $task)
{
    $task->delete();
    return redirect()->route('tasks.index');
}
```

---

## 第6章｜バリデーション

### ✅ 6-1. バリデーションとは？

**ユーザー入力のチェック**

- 必須項目が空でないか
- 文字数制限を超えていないか
- メールアドレスの形式は正しいか

### 🔒 6-2. バリデーションの実装

`app/Http/Controllers/TaskController.php`

```php
public function store(Request $request)
{
    // バリデーション
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable|max:1000'
    ]);

    Task::create($validated);
    return redirect()->route('tasks.index');
}

public function update(Request $request, Task $task)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable|max:1000'
    ]);

    $task->update([
        ...$validated,
        'completed' => $request->has('completed')
    ]);

    return redirect()->route('tasks.index');
}
```

**主なバリデーションルール：**

| ルール | 意味 |
| --- | --- |
| `required` | 必須 |
| `max:255` | 最大255文字 |
| `min:8` | 最小8文字 |
| `email` | メール形式 |
| `nullable` | 空でもOK |
| `unique:users,email` | usersテーブルのemailカラムで重複チェック |

### ⚠️ 6-3. エラーメッセージの表示

`resources/views/tasks/create.blade.php`

```blade
<form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    
    <div>
        <label>タイトル</label>
        <input type="text" name="title" value="{{ old('title') }}" required>
        @error('title')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label>説明</label>
        <textarea name="description">{{ old('description') }}</textarea>
        @error('description')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">作成</button>
</form>
```

**ポイント：**

- `old('title')` → 送信失敗時に入力内容を保持
- `@error('title')` → エラーメッセージを表示

---

## 第7章｜認証機能

### 🔐 7-1. Laravel Breezeのインストール

```bash
# Breezeをインストール
composer require laravel/breeze --dev

# セットアップ
php artisan breeze:install blade

# npm install & build
npm install
npm run dev

# マイグレーション実行
php artisan migrate
```

**生成されるページ：**

- `/register` - 新規登録
- `/login` - ログイン
- `/dashboard` - ダッシュボード

### 🛡️ 7-2. ログイン必須にする

`routes/web.php`

```php
use App\Http\Controllers\TaskController;

Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});
```

### 👤 7-3. ユーザーごとにタスクを分ける

`database/migrations/YYYY_MM_DD_HHMMSS_create_tasks_table.php`に追加

```php
public function up(): void
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 追加
        $table->string('title');
        $table->text('description')->nullable();
        $table->boolean('completed')->default(false);
        $table->timestamps();
    });
}
```

```bash
# マイグレーションをリセット
php artisan migrate:fresh
```

`app/Models/Task.php`

```php
protected $fillable = [
    'user_id', // 追加
    'title',
    'description',
    'completed'
];

// リレーション定義
public function user()
{
    return $this->belongsTo(User::class);
}
```

`app/Models/User.php`に追加

```php
public function tasks()
{
    return $this->hasMany(Task::class);
}
```

`app/Http/Controllers/TaskController.php`

```php
// 自分のタスクのみ表示
public function index()
{
    $tasks = auth()->user()->tasks()->orderBy('created_at', 'desc')->get();
    return view('tasks.index', compact('tasks'));
}

// 作成時にuser_idを自動設定
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable|max:1000'
    ]);

    auth()->user()->tasks()->create($validated);
    return redirect()->route('tasks.index');
}
```

---

## 第8章｜総合演習：ToDoアプリ

### 🎯 8-1. 完成イメージ

**機能一覧：**

- ✅ ユーザー登録・ログイン
- ✅ タスクのCRUD
- ✅ 完了・未完了の切り替え
- ✅ ユーザーごとにタスクを管理
- ✅ バリデーション
- ✅ レスポンシブデザイン

### 🎨 8-2. スタイリングの完成版

`resources/views/layouts/app.blade.php`を作成

```blade
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ToDoアプリ')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: sans-serif;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 5px 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>📝 ToDoアプリ</h1>
            @auth
                <p>ようこそ、{{ auth()->user()->name }}さん</p>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">ログアウト</button>
                </form>
            @endauth
        </div>
    </header>

    <div class="container">
        @yield('content')
    </div>
</body>
</html>
```

### 🚀 8-3. 拡張課題

**レベル1：検索機能**

```php
// TaskController.php
public function index(Request $request)
{
    $query = auth()->user()->tasks();

    if ($request->keyword) {
        $query->where('title', 'like', "%{$request->keyword}%");
    }

    $tasks = $query->orderBy('created_at', 'desc')->get();
    return view('tasks.index', compact('tasks'));
}
```

```blade
{{-- index.blade.php --}}
<form action="{{ route('tasks.index') }}" method="GET">
    <input type="text" name="keyword" placeholder="検索..." value="{{ request('keyword') }}">
    <button type="submit" class="btn btn-primary">検索</button>
</form>
```

**レベル2：フィルタリング機能**

```blade
{{-- index.blade.php --}}
<div>
    <a href="{{ route('tasks.index') }}">全て</a>
    <a href="{{ route('tasks.index', ['filter' => 'active']) }}">未完了</a>
    <a href="{{ route('tasks.index', ['filter' => 'completed']) }}">完了</a>
</div>
```

```php
// TaskController.php
public function index(Request $request)
{
    $query = auth()->user()->tasks();

    if ($request->filter === 'active') {
        $query->where('completed', false);
    } elseif ($request->filter === 'completed') {
        $query->where('completed', true);
    }

    $tasks = $query->orderBy('created_at', 'desc')->get();
    return view('tasks.index', compact('tasks'));
}
```

**レベル3：期限機能**

```bash
# マイグレーションを作成
php artisan make:migration add_due_date_to_tasks_table
```

```php
// マイグレーション
public function up(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->date('due_date')->nullable()->after('description');
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn('due_date');
    });
}
```

```php
// Task.php
protected $fillable = [
    'user_id',
    'title',
    'description',
    'due_date', // 追加
    'completed'
];

protected $casts = [
    'completed' => 'boolean',
    'due_date' => 'date'
];
```

**レベル4：カテゴリ機能**

タスクをカテゴリ分けできるようにする（仕事・プライベート・その他など）

---

## 🏁 ゴール確認チェックリスト

### Laravel基礎

- [ ] Composerでプロジェクトを作成できる
- [ ] `php artisan serve`で開発サーバーを起動できる
- [ ] Laravelのディレクトリ構造を理解している

### MVC

- [ ] ルーティングの設定ができる
- [ ] コントローラーを作成できる
- [ ] Bladeテンプレートで画面を作成できる
- [ ] MVCの役割を説明できる

### データベース

- [ ] マイグレーションを作成・実行できる
- [ ] Eloquentモデルを作成できる
- [ ] CRUD操作ができる
- [ ] リレーションを定義できる

### 認証

- [ ] Laravel Breezeで認証機能を実装できる
- [ ] ログイン必須のルートを設定できる
- [ ] ユーザーごとにデータを管理できる

### セキュリティ

- [ ] バリデーションを実装できる
- [ ] CSRF保護を理解している
- [ ] XSS対策（自動エスケープ）を理解している

---

## 🔍 重要な概念の復習

### 1. MVCの流れ

```
① ユーザーが /tasks にアクセス
② routes/web.php がリクエストを受け取る
③ TaskController@index メソッドが実行される
④ Task モデルがDBからデータを取得
⑤ コントローラーがデータをビューに渡す
⑥ resources/views/tasks/index.blade.php がHTMLを生成
⑦ ブラウザに表示
```

### 2. Eloquentの基本

```php
// 取得
Task::all();                        // 全件
Task::find(1);                      // ID指定
Task::where('completed', true)->get();  // 条件付き

// 作成
Task::create([...]);

// 更新
$task->update([...]);
$task->save();

// 削除
$task->delete();
```

### 3. Bladeの基本

```blade
{{-- 変数の表示 --}}
{{ $task->title }}

{{-- 条件分岐 --}}
@if($task->completed)
    完了
@else
    未完了
@endif

{{-- ループ --}}
@foreach($tasks as $task)
    <p>{{ $task->title }}</p>
@endforeach

{{-- レイアウト継承 --}}
@extends('layouts.app')
@section('content')
    ...
@endsection
```

### 4. ルーティングのパターン

```php
// 基本
Route::get('/tasks', [TaskController::class, 'index']);

// パラメータ付き
Route::get('/tasks/{id}', [TaskController::class, 'show']);

// リソースルート（CRUD全部）
Route::resource('tasks', TaskController::class);

// ミドルウェア（認証必須）
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
});
```

---

## 💡 よくある質問（FAQ）

**Q1: artisanコマンドとは？**

A: Laravelの便利なコマンドラインツールです。主なコマンド：

```bash
php artisan serve              # 開発サーバー起動
php artisan make:controller    # コントローラー作成
php artisan make:model         # モデル作成
php artisan make:migration     # マイグレーション作成
php artisan migrate            # マイグレーション実行
php artisan tinker             # 対話型シェル
php artisan route:list         # ルート一覧表示
```

**Q2: マイグレーションとモデルの違いは？**

A:
- **マイグレーション**: テーブル構造を定義（設計図）
- **モデル**: テーブルのデータを操作（実際の作業道具）

**Q3: compact()とは？**

A: 変数を配列に変換する便利な関数

```php
// これと同じ
$tasks = Task::all();
return view('tasks.index', ['tasks' => $tasks]);

// compact()を使うと
$tasks = Task::all();
return view('tasks.index', compact('tasks'));
```

**Q4: {{ }} と {!! !!} の違いは？**

A:
- `{{ $value }}` → 自動エスケープ（安全）
- `{!! $value !!}` → エスケープなし（危険！）

基本的に`{{ }}`を使いましょう。

**Q5: なぜCSRFトークンが必要？**

A: フォーム送信の偽造を防ぐため。Laravelが自動でチェックしてくれます。

```blade
<form method="POST">
    @csrf  {{-- これがないとエラーになる --}}
    ...
</form>
```

**Q6: old()関数は何をしている？**

A: バリデーションエラー時に、入力した値を保持します。

```blade
<input type="text" name="title" value="{{ old('title') }}">
```

**Q7: リレーションの定義方法は？**

A:

```php
// User.php（1対多の「1」側）
public function tasks()
{
    return $this->hasMany(Task::class);
}

// Task.php（1対多の「多」側）
public function user()
{
    return $this->belongsTo(User::class);
}

// 使い方
$user->tasks;       // ユーザーの全タスク
$task->user;        // タスクの所有者
```

---

## 🚀 次のステップへ

お疲れさまでした！Laravelの基礎とMVCアーキテクチャを習得できましたね。

**重要なポイントのおさらい：**

1. **MVCで役割を分離** → 保守しやすいコードに
2. **Eloquentでシンプルに** → SQLを書かずにDB操作
3. **Bladeで見やすく** → テンプレートで画面構築
4. **バリデーションで安全に** → ユーザー入力をチェック
5. **認証機能で保護** → ユーザーごとにデータ管理

**次は「ステップ⑤ データベース設計」で、**

- ER図の考え方
- 正規化とテーブル設計
- リレーション（1対多、多対多）
- 複雑なSQL（JOIN、サブクエリ）

というデータベース設計の基礎を学んでいきます。

今回作ったToDoアプリを**GitHubにアップロード**して、ポートフォリオを充実させましょう！

---

## 📚 参考リンク

- [Laravel公式ドキュメント（日本語）](https://readouble.com/laravel/)
- [Laravel News](https://laravel-news.com/)
- [Laracasts（動画学習）](https://laracasts.com/)
- [Laravel日本語コミュニティ](https://laravel.jp/)

---

## 🎁 ボーナス：便利なartisanコマンド集

```bash
# 開発でよく使う
php artisan serve                    # サーバー起動
php artisan tinker                   # 対話型シェル
php artisan migrate:fresh            # DB初期化＋マイグレーション
php artisan route:list               # ルート一覧

# ファイル作成
php artisan make:controller TaskController --resource
php artisan make:model Task -m       # モデル＋マイグレーション
php artisan make:migration add_column_to_table
php artisan make:request TaskRequest # フォームリクエスト
php artisan make:seeder TaskSeeder   # シーダー

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 本番環境用
php artisan config:cache             # 設定キャッシュ
php artisan route:cache              # ルートキャッシュ
php artisan optimize                 # 最適化
```

---

**🎉 ステップ④完了です！次のステップに進みましょう！**
