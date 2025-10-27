<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 パスワードジェネレーター - Laravel App</title>
    <link rel="stylesheet" href="{{ asset('css/password_generator.css') }}">
    <style>
        /* 認証ナビゲーション用の追加スタイル */
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
            border: 1px solid rgba(255, 255, 255, 0.2);
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

        .auth-nav .user-name {
            color: #27ae60;
            font-weight: 700;
        }

        .auth-nav .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .auth-nav .logout-btn:hover {
            background: #c82333;
        }

        /* モバイル対応 */
        @media (max-width: 768px) {
            .auth-nav {
                position: static;
                margin-bottom: 20px;
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        /* パスワードジェネレーター用のボディ調整 */
        body {
            padding-top: 80px;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- 認証ナビゲーション -->
    @if (Route::has('login'))
        <div class="auth-nav">
            @auth
                <!-- ログイン済みユーザー -->
                <span class="user-name">{{ Auth::user()->name }}さん</span>
                <span>ログイン中</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">ログアウト</button>
                </form>
            @else
                <!-- ゲストユーザー -->
                <a href="{{ route('login') }}">ログイン</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">新規登録</a>
                @endif
            @endauth
        </div>
    @endif

    <!-- セッションメッセージ -->
    @if(session('success'))
        <div id="session-message" style="position: fixed; top: 100px; left: 50%; transform: translateX(-50%); z-index: 1001; background: #d4edda; color: #155724; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); animation: slideDown 0.3s ease;">
            {{ session('success') }}
        </div>
        <style>
            @keyframes slideDown {
                from { opacity: 0; transform: translateX(-50%) translateY(-20px); }
                to { opacity: 1; transform: translateX(-50%) translateY(0); }
            }
        </style>
    @endif

    <div class="container">
        <h1>🔐 パスワードジェネレーター</h1>
        
        <form method="POST" action="{{ route('password.generate') }}">
            @csrf
            <div class="form-group">
                <label for="length">パスワードの長さ:</label>
                <input type="number" id="length" name="length" value="{{ $length }}" min="4" max="50">
                @error('length')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>文字種オプション</label>
                <div class="description">
                    💡 基本は英数字（a-z, A-Z, 0-9）を使用します<br>
                    チェックした文字種を必ず1文字以上含めます
                </div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" name="lowercase" {{ $options['lowercase'] ? 'checked' : '' }}>
                        <span class="checkbox-label">小文字を必ず含める (a-z)</span>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="uppercase" {{ $options['uppercase'] ? 'checked' : '' }}>
                        <span class="checkbox-label">大文字を必ず含める (A-Z)</span>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="numbers" {{ $options['numbers'] ? 'checked' : '' }}>
                        <span class="checkbox-label">数字を必ず含める (0-9)</span>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="symbols" {{ $options['symbols'] ? 'checked' : '' }}>
                        <span class="checkbox-label">記号を必ず含める (!@#$%^&*()_+-=[]{}|;:,.<>?)</span>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="generate-btn">パスワード生成</button>
        </form>
        
        @if($generatedPassword)
        <div class="result">
            <h3>生成されたパスワード</h3>
            <div class="password-container">
                <input type="text" class="password-input" id="password" value="{{ $generatedPassword }}" readonly>
                <button type="button" class="copy-btn" onclick="copyToClipboard()">📋 コピー</button>
            </div>
        </div>
        @endif

        <!-- パスワード履歴 -->
        @if($passwordHistories->count() > 0)
        <div style="margin-top: 40px;">
            <h3 style="color: #2c3e50; margin-bottom: 20px; text-align: center;">📚 パスワード履歴</h3>
            <div style="max-height: 400px; overflow-y: auto; background: #f8f9fa; border-radius: 16px; padding: 20px;">
                @foreach($passwordHistories as $index => $history)
                <div style="background: white; margin-bottom: 15px; padding: 15px; border-radius: 12px; border: 1px solid #e9ecef; transition: all 0.2s ease;" 
                     onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'"
                     onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                        <!-- パスワード表示部分 -->
                        <div style="flex: 1; min-width: 200px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                <input type="text" 
                                       value="{{ $history->password }}" 
                                       readonly 
                                       id="history-password-{{ $index }}"
                                       style="font-family: 'Monaco', 'Menlo', monospace; font-weight: 600; font-size: 14px; padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa; flex: 1; letter-spacing: 1px;"
                                       onclick="this.select(); this.setSelectionRange(0, 99999);">
                                <button type="button" 
                                        onclick="copyHistoryPassword({{ $index }})" 
                                        style="background: #2c3e50; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; transition: all 0.2s ease;"
                                        onmouseover="this.style.background='#34495e'"
                                        onmouseout="this.style.background='#2c3e50'">
                                    📋 コピー
                                </button>
                            </div>
                            
                            <!-- 設定情報 -->
                            <div style="font-size: 12px; color: #6c757d; display: flex; flex-wrap: wrap; gap: 10px;">
                                <span><strong>長さ:</strong> {{ $history->length }}文字</span>
                                @php
                                    $options = is_array($history->options) ? $history->options : [];
                                @endphp
                                @if(!empty($options))
                                    @if($options['lowercase'] ?? false)
                                        <span style="background: #e3f2fd; color: #1976d2; padding: 2px 6px; border-radius: 4px;">小文字</span>
                                    @endif
                                    @if($options['uppercase'] ?? false)
                                        <span style="background: #f3e5f5; color: #7b1fa2; padding: 2px 6px; border-radius: 4px;">大文字</span>
                                    @endif
                                    @if($options['numbers'] ?? false)
                                        <span style="background: #e8f5e8; color: #388e3c; padding: 2px 6px; border-radius: 4px;">数字</span>
                                    @endif
                                    @if($options['symbols'] ?? false)
                                        <span style="background: #fff3e0; color: #f57c00; padding: 2px 6px; border-radius: 4px;">記号</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        <!-- 生成日時 -->
                        <div style="font-size: 11px; color: #868e96; text-align: right;">
                            @if(is_string($history->created_at))
                                {{ \Carbon\Carbon::parse($history->created_at)->format('m/d H:i') }}
                            @else
                                {{ $history->created_at->format('m/d H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- 履歴が多い場合の案内 -->
                @auth
                    <div style="text-align: center; padding: 10px; color: #6c757d; font-size: 12px;">
                        💡 最新10件を表示しています（ログインユーザー特典）
                    </div>
                @else
                    <div style="text-align: center; padding: 10px; color: #6c757d; font-size: 12px;">
                        💡 最新5件を表示しています（<a href="{{ route('register') }}" style="color: #2c3e50;">登録</a>すると10件まで保存）
                    </div>
                @endauth
            </div>
        </div>
        @endif

        <!-- 認証状態の表示 -->
        @auth
        <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #e8f5e8, #f0f8f0); border-radius: 16px; text-align: center; border: 2px solid #27ae60;">
            <h3 style="color: #27ae60; margin-bottom: 10px;">✅ ログイン中</h3>
            <p style="color: #2d5a2d; margin: 0;">{{ Auth::user()->name }}さん、安全にパスワードを生成できます。</p>
        </div>
        @else
        <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #fff3cd, #ffeaa7); border-radius: 16px; text-align: center; border: 2px solid #ffc107;">
            <h3 style="color: #856404; margin-bottom: 10px;">💡 ヒント</h3>
            <p style="color: #856404; margin: 0;">
                <a href="{{ route('register') }}" style="color: #2c3e50; text-decoration: none; font-weight: 600;">アカウント作成</a>すると、
                パスワード履歴や高度な設定機能をご利用いただけます。
            </p>
        </div>
        @endauth
    </div>

    <script>
        function copyToClipboard() {
            try {
                const passwordInput = document.getElementById('password');
                const copyBtn = document.querySelector('.copy-btn');
                
                if (!passwordInput || !copyBtn) {
                    console.log('パスワード要素が見つかりません');
                    return;
                }
                
                // パスワードを選択してコピー
                passwordInput.select();
                passwordInput.setSelectionRange(0, 99999); // モバイル対応
                
                try {
                    // Clipboard API を使用
                    navigator.clipboard.writeText(passwordInput.value).then(function() {
                        showCopySuccess(copyBtn);
                    }).catch(function() {
                        // フォールバック: execCommand
                        document.execCommand('copy');
                        showCopySuccess(copyBtn);
                    });
                } catch (err) {
                    // 最終フォールバック
                    try {
                        document.execCommand('copy');
                        showCopySuccess(copyBtn);
                    } catch (finalErr) {
                        console.log('コピーに失敗しました:', finalErr);
                    }
                }
            } catch (error) {
                console.log('copyToClipboard関数でエラー:', error);
            }
        }
        
        function showCopySuccess(button) {
            const originalText = button.textContent;
            const originalClass = button.className;
            
            // ボタンの表示を変更
            button.textContent = '✅ コピーしました！';
            button.classList.add('copied');
            
            // 2秒後に元に戻す
            setTimeout(() => {
                button.textContent = originalText;
                button.className = originalClass;
            }, 2000);
        }

        // 履歴のパスワードをコピーする関数
        function copyHistoryPassword(index) {
            try {
                const passwordInput = document.getElementById(`history-password-${index}`);
                const copyBtn = event.target;
                
                if (!passwordInput || !copyBtn) {
                    console.log('履歴パスワード要素が見つかりません:', index);
                    return;
                }
                
                // パスワードを選択してコピー
                passwordInput.select();
                passwordInput.setSelectionRange(0, 99999);
                
                try {
                    // Clipboard API を使用
                    navigator.clipboard.writeText(passwordInput.value).then(function() {
                        showCopySuccess(copyBtn);
                    }).catch(function() {
                        // フォールバック: execCommand
                        document.execCommand('copy');
                        showCopySuccess(copyBtn);
                    });
                } catch (err) {
                    // 最終フォールバック
                    try {
                        document.execCommand('copy');
                        showCopySuccess(copyBtn);
                    } catch (finalErr) {
                        console.log('履歴パスワードのコピーに失敗しました:', finalErr);
                    }
                }
            } catch (error) {
                console.log('copyHistoryPassword関数でエラー:', error);
            }
        }
        
        // パスワード入力フィールドをクリックした時に全選択
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.addEventListener('click', function() {
                    this.select();
                    this.setSelectionRange(0, 99999);
                });
                
                passwordInput.addEventListener('focus', function() {
                    this.select();
                    this.setSelectionRange(0, 99999);
                });
            }

            // セッションメッセージの自動非表示
            try {
                const successMessage = document.getElementById('session-message');
                if (successMessage) {
                    setTimeout(() => {
                        try {
                            successMessage.style.opacity = '0';
                            successMessage.style.transform = 'translateX(-50%) translateY(-20px)';
                            setTimeout(() => {
                                try {
                                    if (successMessage && successMessage.parentNode) {
                                        successMessage.parentNode.removeChild(successMessage);
                                    }
                                } catch (removeError) {
                                    console.log('セッションメッセージの削除でエラー:', removeError);
                                }
                            }, 300);
                        } catch (hideError) {
                            console.log('セッションメッセージの非表示でエラー:', hideError);
                        }
                    }, 3000);
                }
            } catch (findError) {
                console.log('セッションメッセージの検索でエラー:', findError);
            }
        });
    </script>
</body>
</html>
