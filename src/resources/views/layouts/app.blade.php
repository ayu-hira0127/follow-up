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
            max-width: 800px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
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
            text-decoration: none;
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
            <span class="user-info">{{ auth()->user()->name }}</span>
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
