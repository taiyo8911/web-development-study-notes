# 📝 TODOアプリ開発カリキュラム
**LaravelでTODOアプリを作りながらWeb開発を完全マスター**

---

## 🎯 このカリキュラムのゴール

**実際に使えるTODOアプリを作成しながら、以下のスキルを習得します：**
- MVCアーキテクチャの理解と実装
- データベース設計とCRUD操作
- ユーザー認証とセキュリティ
- モダンなUI/UXの実装

---

## 🗺️ TODOアプリの全体像

### 完成イメージ
```
TODOアプリ
├── 🏠 トップページ（ランディング）
├── 📝 タスク一覧画面
│   ├── タスク追加
│   ├── タスク編集
│   ├── タスク削除
│   ├── 完了/未完了の切り替え
│   └── フィルター/検索機能
├── 🔐 認証機能
│   ├── ユーザー登録
│   ├── ログイン
│   └── ログアウト
└── 👤 マイページ
    ├── プロフィール編集
    └── タスク統計表示
```

---

## 📚 カリキュラム構成（全5章）

| 章 | テーマ | 作る機能 | 学習時間 |
|---|---|---|---|
| 第1章 | TODOアプリの設計 | アプリ全体の構想・画面設計 | 2時間 |
| 第2章 | 基本機能の実装 | タスク一覧・追加・表示 | 4時間 |
| 第3章 | CRUD機能の完成 | 編集・削除・完了機能 | 4時間 |
| 第4章 | ユーザー機能 | 認証・マイタスク管理 | 4時間 |
| 第5章 | UI/UX改善 | デザイン・使いやすさ向上 | 3時間 |

**合計学習時間：約17時間**

---

## 第1章｜TODOアプリの設計と環境構築

### 1-1. TODOアプリの機能設計

#### 🎨 画面構成を理解する

**1. タスク一覧画面（メイン画面）**
```
┌─────────────────────────────────────┐
│  📝 My TODO App        [ログアウト]  │
├─────────────────────────────────────┤
│  [+ 新しいタスクを追加]              │
│                                     │
│  フィルター: [全て▼] 検索: [_____]  │
├─────────────────────────────────────┤
│  □ 買い物リストを作成     [編集][×] │
│  ✓ メール返信           [編集][×] │
│  □ 会議資料の準備       [編集][×] │
└─────────────────────────────────────┘
```

**2. タスク追加画面（モーダル/別ページ）**
```
┌─────────────────────────────────────┐
│  新しいタスクを追加                   │
├─────────────────────────────────────┤
│  タイトル*                           │
│  [_______________________________]  │
│                                     │
│  説明                               │
│  [_______________________________]  │
│  [_______________________________]  │
│                                     │
│  期限                               │
│  [📅 日付を選択]                    │
│                                     │
│  優先度                             │
│  ○高 ○中 ●低                       │
│                                     │
│  [キャンセル] [保存]                │
└─────────────────────────────────────┘
```

#### 📊 データベース設計

**必要なテーブル：**

```sql
-- usersテーブル（ユーザー情報）
users
├── id (主キー)
├── name (名前)
├── email (メールアドレス)
├── password (パスワード)
└── timestamps (作成・更新日時)

-- tasksテーブル（タスク情報）
tasks
├── id (主キー)
├── user_id (外部キー → users.id)
├── title (タイトル)
├── description (説明)
├── due_date (期限)
├── priority (優先度: high/medium/low)
├── completed (完了フラグ)
└── timestamps (作成・更新日時)
```

### 1-2. 環境構築（スピード重視版）

#### 必要なツールをインストール

```bash
# 1. Homebrewのインストール（Mac）
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. PHPとComposerのインストール
brew install php composer

# 3. Node.jsのインストール（フロントエンド用）
brew install node
```

#### Laravelプロジェクトの作成

```bash
# TODOアプリプロジェクトを作成
composer create-project laravel/laravel todo-app
cd todo-app

# データベース設定（SQLiteを使用）
touch database/database.sqlite

# .envファイルを編集
# DB_CONNECTION=sqlite
# 他のDB_*行をコメントアウト

# 開発サーバー起動
php artisan serve
```

**✅ http://localhost:8000 でLaravelの画面が表示されればOK！**

---

## 第2章｜基本機能の実装（タスクの表示と追加）

### 2-1. タスクモデルとテーブルの作成

#### モデルとマイグレーションを同時に作成

```bash
php artisan make:model Task -m
```

#### マイグレーションファイルを編集
`database/migrations/xxxx_create_tasks_table.php`

```php
public function up(): void
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->date('due_date')->nullable();
        $table->enum('priority', ['high', 'medium', 'low'])->default('low');
        $table->boolean('completed')->default(false);
        $table->timestamps();
    });
}
```

#### マイグレーション実行

```bash
php artisan migrate
```

### 2-2. コントローラーの作成

```bash
php artisan make:controller TaskController --resource
```

`app/Http/Controllers/TaskController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // タスク一覧画面
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('tasks.index', compact('tasks'));
    }

    // タスク作成画面
    public function create()
    {
        return view('tasks.create');
    }

    // タスク保存処理
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:high,medium,low'
        ]);

        Task::create($validated);
        
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを作成しました！');
    }
}
```

### 2-3. ビューの作成

#### レイアウトファイル
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
        
        .task-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .task-title {
            flex: 1;
        }
        
        .task-completed {
            text-decoration: line-through;
            color: #a0aec0;
        }
        
        .priority-high {
            border-left: 4px solid #f56565;
        }
        
        .priority-medium {
            border-left: 4px solid #ed8936;
        }
        
        .priority-low {
            border-left: 4px solid #48bb78;
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
        input[type="date"],
        textarea,
        select {
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

#### タスク一覧画面
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
                <div class="task-item priority-{{ $task->priority }}">
                    <input type="checkbox" 
                           class="task-checkbox" 
                           {{ $task->completed ? 'checked' : '' }}>
                    
                    <div class="task-title {{ $task->completed ? 'task-completed' : '' }}">
                        <strong>{{ $task->title }}</strong>
                        @if($task->description)
                            <br>
                            <small style="color: #718096;">{{ $task->description }}</small>
                        @endif
                        @if($task->due_date)
                            <br>
                            <small style="color: #a0aec0;">
                                📅 期限: {{ $task->due_date->format('Y/m/d') }}
                            </small>
                        @endif
                    </div>
                    
                    <div>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm" 
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

#### タスク作成画面
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
            <label for="description">説明</label>
            <textarea name="description" 
                      id="description" 
                      placeholder="タスクの詳細を入力してください">{{ old('description') }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="due_date">期限</label>
            <input type="date" 
                   name="due_date" 
                   id="due_date" 
                   value="{{ old('due_date') }}">
        </div>
        
        <div class="form-group">
            <label for="priority">優先度</label>
            <select name="priority" id="priority">
                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>低</option>
                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>中</option>
                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>高</option>
            </select>
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

### 2-4. ルートの設定

`routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return redirect()->route('tasks.index');
});

Route::resource('tasks', TaskController::class);
```

### 2-5. モデルの設定

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
        'title',
        'description',
        'due_date',
        'priority',
        'completed'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date'
    ];
}
```

**🎉 ここまでで基本的なタスクの表示と追加ができるようになりました！**

---

## 第3章｜CRUD機能の完成（編集・削除・完了機能）

### 3-1. 編集機能の実装

#### コントローラーに編集機能を追加
`app/Http/Controllers/TaskController.php`に追加

```php
// タスク編集画面
public function edit(Task $task)
{
    return view('tasks.edit', compact('task'));
}

// タスク更新処理
public function update(Request $request, Task $task)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
        'due_date' => 'nullable|date',
        'priority' => 'required|in:high,medium,low'
    ]);

    $task->update($validated);
    
    return redirect()->route('tasks.index')
        ->with('success', 'タスクを更新しました！');
}

// タスク削除処理
public function destroy(Task $task)
{
    $task->delete();
    
    return redirect()->route('tasks.index')
        ->with('success', 'タスクを削除しました！');
}
```

#### 編集画面の作成
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
            <label for="description">説明</label>
            <textarea name="description" 
                      id="description">{{ old('description', $task->description) }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="due_date">期限</label>
            <input type="date" 
                   name="due_date" 
                   id="due_date" 
                   value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
        </div>
        
        <div class="form-group">
            <label for="priority">優先度</label>
            <select name="priority" id="priority">
                <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>低</option>
                <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>中</option>
                <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>高</option>
            </select>
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
            
            <button type="button" 
                    onclick="if(confirm('本当に削除しますか？')) { document.getElementById('delete-form').submit(); }"
                    class="btn btn-danger">
                削除
            </button>
        </div>
    </form>
    
    <form id="delete-form" action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection
```

### 3-2. 完了機能の実装（Ajax対応）

#### 完了状態を切り替えるルートを追加
`routes/web.php`に追加

```php
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
```

#### コントローラーにtoggleメソッドを追加

```php
// タスク完了状態の切り替え
public function toggle(Task $task)
{
    $task->completed = !$task->completed;
    $task->save();
    
    if (request()->ajax()) {
        return response()->json(['completed' => $task->completed]);
    }
    
    return redirect()->route('tasks.index');
}
```

#### タスク一覧画面を更新（JavaScript追加）
`resources/views/tasks/index.blade.php`を更新

```blade
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.task-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const taskTitle = this.parentElement.querySelector('.task-title');
            
            fetch(`/tasks/${taskId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.completed) {
                    taskTitle.classList.add('task-completed');
                } else {
                    taskTitle.classList.remove('task-completed');
                }
            });
        });
    });
});
</script>
@endsection
```

---

## 第4章｜ユーザー機能（認証とマイタスク管理）

### 4-1. Laravel Breezeで認証機能を追加

```bash
# Laravel Breezeをインストール
composer require laravel/breeze --dev

# Breezeの初期設定
php artisan breeze:install blade

# フロントエンドアセットをビルド
npm install && npm run build

# マイグレーション実行
php artisan migrate
```

### 4-2. タスクとユーザーの紐付け

#### マイグレーションファイルを作成

```bash
php artisan make:migration add_user_id_to_tasks_table
```

```php
public function up(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
```

```bash
php artisan migrate
```

### 4-3. モデルのリレーション設定

#### User.php
```php
public function tasks()
{
    return $this->hasMany(Task::class);
}
```

#### Task.php
```php
protected $fillable = [
    'user_id',  // 追加
    'title',
    'description',
    'due_date',
    'priority',
    'completed'
];

public function user()
{
    return $this->belongsTo(User::class);
}
```

### 4-4. コントローラーの更新（認証対応）

`app/Http/Controllers/TaskController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tasks = Auth::user()->tasks()->orderBy('created_at', 'desc')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:high,medium,low'
        ]);

        Auth::user()->tasks()->create($validated);
        
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを作成しました！');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:high,medium,low'
        ]);

        $task->update($validated);
        
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを更新しました！');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        
        return redirect()->route('tasks.index')
            ->with('success', 'タスクを削除しました！');
    }

    public function toggle(Task $task)
    {
        $this->authorize('update', $task);
        
        $task->completed = !$task->completed;
        $task->save();
        
        if (request()->ajax()) {
            return response()->json(['completed' => $task->completed]);
        }
        
        return redirect()->route('tasks.index');
    }
}
```

### 4-5. ポリシーの作成（認可設定）

```bash
php artisan make:policy TaskPolicy --model=Task
```

`app/Policies/TaskPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }
}
```

---

## 第5章｜UI/UX改善と追加機能

### 5-1. フィルター・検索機能

#### コントローラーに検索機能を追加

```php
public function index(Request $request)
{
    $query = Auth::user()->tasks();
    
    // 検索
    if ($request->filled('search')) {
        $query->where('title', 'like', "%{$request->search}%")
              ->orWhere('description', 'like', "%{$request->search}%");
    }
    
    // フィルター
    if ($request->filled('filter')) {
        switch($request->filter) {
            case 'active':
                $query->where('completed', false);
                break;
            case 'completed':
                $query->where('completed', true);
                break;
            case 'today':
                $query->whereDate('due_date', today());
                break;
            case 'overdue':
                $query->where('completed', false)
                      ->whereDate('due_date', '<', today());
                break;
        }
    }
    
    // 優先度フィルター
    if ($request->filled('priority')) {
        $query->where('priority', $request->priority);
    }
    
    $tasks = $query->orderBy('created_at', 'desc')->get();
    
    return view('tasks.index', compact('tasks'));
}
```

#### 検索・フィルターUIの追加

```blade
<div style="background: #f7fafc; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
    <form action="{{ route('tasks.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
        
        <input type="text" 
               name="search" 
               placeholder="検索..." 
               value="{{ request('search') }}"
               style="flex: 1;">
        
        <select name="filter" onchange="this.form.submit()">
            <option value="">すべて</option>
            <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>未完了</option>
            <option value="completed" {{ request('filter') == 'completed' ? 'selected' : '' }}>完了済み</option>
            <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>今日の期限</option>
            <option value="overdue" {{ request('filter') == 'overdue' ? 'selected' : '' }}>期限切れ</option>
        </select>
        
        <select name="priority" onchange="this.form.submit()">
            <option value="">優先度</option>
            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>高</option>
            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>中</option>
            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>低</option>
        </select>
        
        <button type="submit" class="btn btn-primary">検索</button>
        
        @if(request()->hasAny(['search', 'filter', 'priority']))
            <a href="{{ route('tasks.index') }}" class="btn" style="background: #e2e8f0; color: #2d3748;">
                クリア
            </a>
        @endif
    </form>
</div>
```

### 5-2. タスク統計の表示

#### ダッシュボードコントローラーの作成

```bash
php artisan make:controller DashboardController
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'total' => $user->tasks()->count(),
            'completed' => $user->tasks()->where('completed', true)->count(),
            'active' => $user->tasks()->where('completed', false)->count(),
            'overdue' => $user->tasks()
                ->where('completed', false)
                ->whereDate('due_date', '<', today())
                ->count(),
            'today' => $user->tasks()
                ->whereDate('due_date', today())
                ->count(),
        ];
        
        $recentTasks = $user->tasks()
            ->latest()
            ->take(5)
            ->get();
        
        $urgentTasks = $user->tasks()
            ->where('completed', false)
            ->where('priority', 'high')
            ->orWhere(function($query) {
                $query->where('completed', false)
                      ->whereDate('due_date', '<=', today()->addDays(3));
            })
            ->take(5)
            ->get();
        
        return view('dashboard', compact('stats', 'recentTasks', 'urgentTasks'));
    }
}
```

#### ダッシュボードビューの作成
`resources/views/dashboard.blade.php`

```blade
@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
    <h2>📊 ダッシュボード</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 30px 0;">
        <div style="background: #edf2f7; padding: 20px; border-radius: 5px; text-align: center;">
            <div style="font-size: 2em; font-weight: bold; color: #2d3748;">{{ $stats['total'] }}</div>
            <div style="color: #718096;">全タスク</div>
        </div>
        
        <div style="background: #c6f6d5; padding: 20px; border-radius: 5px; text-align: center;">
            <div style="font-size: 2em; font-weight: bold; color: #22543d;">{{ $stats['completed'] }}</div>
            <div style="color: #2f855a;">完了済み</div>
        </div>
        
        <div style="background: #bee3f8; padding: 20px; border-radius: 5px; text-align: center;">
            <div style="font-size: 2em; font-weight: bold; color: #2c5282;">{{ $stats['active'] }}</div>
            <div style="color: #2b6cb0;">進行中</div>
        </div>
        
        <div style="background: #fed7d7; padding: 20px; border-radius: 5px; text-align: center;">
            <div style="font-size: 2em; font-weight: bold; color: #742a2a;">{{ $stats['overdue'] }}</div>
            <div style="color: #c53030;">期限切れ</div>
        </div>
        
        <div style="background: #feebc8; padding: 20px; border-radius: 5px; text-align: center;">
            <div style="font-size: 2em; font-weight: bold; color: #7c2d12;">{{ $stats['today'] }}</div>
            <div style="color: #c05621;">今日の期限</div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <h3>🔥 緊急のタスク</h3>
            @forelse($urgentTasks as $task)
                <div style="padding: 10px; border-left: 4px solid #f56565; margin: 10px 0;">
                    <strong>{{ $task->title }}</strong>
                    @if($task->due_date)
                        <br><small>期限: {{ $task->due_date->format('Y/m/d') }}</small>
                    @endif
                </div>
            @empty
                <p style="color: #718096;">緊急のタスクはありません</p>
            @endforelse
        </div>
        
        <div>
            <h3>📝 最近のタスク</h3>
            @forelse($recentTasks as $task)
                <div style="padding: 10px; border-left: 4px solid #4299e1; margin: 10px 0;">
                    <strong>{{ $task->title }}</strong>
                    <br><small>作成: {{ $task->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <p style="color: #718096;">タスクがありません</p>
            @endforelse
        </div>
    </div>
    
    <div style="margin-top: 30px; text-align: center;">
        <a href="{{ route('tasks.index') }}" class="btn btn-primary" style="font-size: 1.1em; padding: 15px 30px;">
            タスク一覧へ →
        </a>
    </div>
@endsection
```

---

## 🎯 学習の確認チェックリスト

### 基本機能
- [ ] タスクの追加ができる
- [ ] タスクの一覧表示ができる
- [ ] タスクの編集ができる
- [ ] タスクの削除ができる
- [ ] タスクの完了/未完了切り替えができる

### データベース
- [ ] マイグレーションを作成・実行できる
- [ ] モデルのリレーションを理解している
- [ ] Eloquentでデータ操作ができる

### 認証機能
- [ ] ユーザー登録・ログインができる
- [ ] ユーザーごとにタスクを管理できる
- [ ] 認可（ポリシー）を実装できる

### UI/UX
- [ ] 検索機能を実装できる
- [ ] フィルター機能を実装できる
- [ ] レスポンシブデザインを理解している
- [ ] Ajaxで非同期通信ができる

### Laravel理解
- [ ] MVCアーキテクチャを説明できる
- [ ] ルーティングの仕組みを理解している
- [ ] バリデーションを実装できる
- [ ] Bladeテンプレートを活用できる

---

## 🚀 次のステップ

### 追加機能のアイデア

1. **カテゴリー/タグ機能**
   - タスクをカテゴリー分け
   - タグによる分類と検索

2. **通知機能**
   - 期限前のリマインダー
   - メール通知

3. **チーム機能**
   - タスクの共有
   - コメント機能

4. **統計・分析**
   - タスク完了率のグラフ
   - 生産性の可視化

5. **API開発**
   - RESTful API
   - モバイルアプリ対応

### 学習を深めるために

1. **テストの実装**
   ```bash
   php artisan make:test TaskTest
   ```

2. **リファクタリング**
   - サービスクラスの活用
   - リポジトリパターン

3. **パフォーマンス改善**
   - クエリの最適化
   - キャッシュの活用

4. **セキュリティ強化**
   - XSS対策の理解
   - SQLインジェクション対策

---

## 📚 参考資料

### 公式ドキュメント
- [Laravel公式ドキュメント](https://laravel.com/docs)
- [Laravel日本語ドキュメント](https://readouble.com/laravel/)

### 学習リソース
- [Laracasts](https://laracasts.com/) - 動画チュートリアル
- [Laravel Daily](https://laraveldaily.com/) - Tips & Tricks
- [Laravel News](https://laravel-news.com/) - 最新情報

### コミュニティ
- [Laravel.jp](https://laravel.jp/) - 日本語コミュニティ
- [Qiita Laravelタグ](https://qiita.com/tags/laravel)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)

---

**🎉 これでTODOアプリの開発は完了です！**

作成したアプリをGitHubにアップロードして、ポートフォリオとして活用しましょう。継続的に機能を追加していくことで、より実践的なスキルが身につきます。
