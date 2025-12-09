<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>
    
    <!-- 暫定：インラインCSS（スタイル崩れ解決） -->
    <style>
        /* パスワードジェネレーター - 基本スタイル */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            max-width: 900px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        /* パスワード一覧ページ用にコンテナ幅を広げる */
        .password-list-page .container {
            max-width: 1200px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2d3748;
            font-size: 2.5em;
            font-weight: 700;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 600;
            font-size: 1.1em;
        }

        .description {
            font-size: 0.9em;
            color: #718096;
            margin-bottom: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #ecf0f1, #f8f9fa);
            border-left: 4px solid #2c3e50;
        }

        input[type="number"] {
            width: 120px;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1.1em;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .checkbox-group {
            display: grid;
            gap: 12px;
            margin-top: 15px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: #f7fafc;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .checkbox-item:hover {
            background: #edf2f7;
            transform: translateX(5px);
        }

        .checkbox-item:has(input[type="checkbox"]:checked) {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            border-color: #2c3e50;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            accent-color: #2c3e50;
            cursor: pointer;
        }

        .checkbox-label {
            font-weight: 500;
            cursor: pointer;
            flex: 1;
        }

        .generate-btn {
            width: 100%;
            padding: 18px 24px;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.3em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 25px;
        }

        .generate-btn:hover {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(44, 62, 80, 0.5);
        }

        .password-container {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .password-input {
            flex: 1;
            min-width: 250px;
            padding: 16px 20px;
            font-size: 1.2em;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-weight: 600;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            color: #2d3748;
            text-align: center;
            letter-spacing: 2px;
            user-select: all;
            cursor: pointer;
        }

        .copy-btn {
            padding: 16px 20px;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .copy-btn:hover {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            transform: translateY(-2px);
        }

        .result {
            margin-top: 30px;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 16px;
            text-align: center;
            border: 2px solid #6c757d;
        }

        /* パスワード履歴表示 */
        .history-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 15px;
        }

        .history-item {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .history-item:hover {
            border-color: #2c3e50;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.1);
        }

        .history-password {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 1.1em;
            font-weight: 600;
            color: #2c3e50;
            background: #f7fafc;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            word-break: break-all;
            user-select: all;
            cursor: pointer;
        }

        .history-info {
            font-size: 0.9em;
            color: #718096;
            text-align: right;
        }

        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        /* 認証ナビゲーション */
        .auth-nav {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 10px 15px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .auth-nav a, .auth-nav button {
            color: #2c3e50;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 14px;
        }

        .auth-nav a:hover, .auth-nav button:hover {
            background: #2c3e50;
            color: white;
        }

        /* パスワード一覧ページ用スタイル */
        .password-list {
            margin-top: 20px;
        }

        .password-list table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.1);
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
        }

        .password-list thead {
            background: linear-gradient(135deg, #2c3e50, #34495e);
        }

        .password-list th {
            padding: 18px 20px;
            text-align: left;
            color: white;
            font-weight: 600;
            font-size: 1em;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .password-list th:last-child {
            text-align: center;
        }

        .password-list tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .password-list tbody tr:hover {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            transform: translateX(3px);
        }

        .password-list tbody tr:last-child {
            border-bottom: none;
        }

        .password-list td {
            padding: 18px 20px;
            vertical-align: middle;
            color: #2d3748;
        }

        .password-list td strong {
            color: #2c3e50;
            font-size: 1.1em;
        }

        .password-list td a {
            color: #34495e;
            text-decoration: none;
            word-break: break-all;
            transition: all 0.2s ease;
        }

        .password-list td a:hover {
            color: #2c3e50;
            text-decoration: underline;
        }

        /* 編集ボタンはURLのスタイルを適用しない */
        .password-list td a.edit-btn {
            color: white;
            word-break: normal;
        }

        .password-list td a.edit-btn:hover {
            color: white;
            text-decoration: none;
        }

        .password-field {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 1em;
            background: #f7fafc;
            color: #2d3748;
            transition: all 0.3s ease;
        }

        .password-field:focus {
            outline: none;
            border-color: #2c3e50;
            background: white;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }

        .toggle-password-btn,
        .copy-password-btn {
            padding: 12px 18px;
            border: none;
            border-radius: 12px;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .toggle-password-btn {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }

        .toggle-password-btn:hover {
            background: linear-gradient(135deg, #5a6268, #6c757d);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .copy-password-btn {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
        }

        .copy-password-btn:hover {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
        }

        .copy-password-btn.copied {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .edit-btn {
            display: inline-block;
            padding: 10px 18px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .edit-btn:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .delete-btn {
            padding: 10px 18px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #c82333, #dc3545);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .back-link {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-link:hover {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
        }

        .empty-state {
            padding: 50px 40px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 16px;
            margin-top: 20px;
            border: 2px solid #e2e8f0;
        }

        .empty-state p {
            font-size: 1.2em;
            color: #718096;
            margin-bottom: 25px;
        }

        .empty-state a {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .empty-state a:hover {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 2px solid;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-color: #b8dacc;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-color: #f1b0b7;
        }

        .password-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .date-info {
            color: #718096;
            font-size: 0.95em;
        }
    </style>
    
    <!-- ページ固有のCSS -->
    @stack('styles')
</head>
<body>
    <!-- 認証ナビゲーション -->
    <nav class="auth-nav">
        @guest
            <a href="{{ route('login') }}" class="nav-link">ログイン</a>
            <a href="{{ route('register') }}" class="nav-link">登録</a>
        @else
        <a href="{{ route('password.list') }}" class="nav-link"><span class="user-info">{{ auth()->user()->name }}</span></a>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">ログアウト</button>
            </form>
        @endguest
    </nav>

    <!-- メインコンテンツ -->
    <main>
        @yield('content')
    </main>

    <!-- ページ固有のJS -->
    @stack('scripts')
</body>
</html>
