@extends('layouts.app')

@section('title', 'パスワードジェネレーター')

{{-- CSSは共通レイアウトで読み込み済み --}}

@section('content')
<div class="container">
    <h1>🔐 パスワードジェネレーター</h1>
    
    <form method="POST" action="{{ route('password.generate') }}">
        @csrf
        <div class="form-group">
            <label for="length">パスワードの長さ:</label>
            <input type="number" id="length" name="length" value="{{ $length ?? 8 }}" min="4" max="50">
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
                    <input type="checkbox" name="lowercase" {{ ($options['lowercase'] ?? false) ? 'checked' : '' }}>
                    <span class="checkbox-label">小文字を必ず含める (a-z)</span>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" name="uppercase" {{ ($options['uppercase'] ?? false) ? 'checked' : '' }}>
                    <span class="checkbox-label">大文字を必ず含める (A-Z)</span>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" name="numbers" {{ ($options['numbers'] ?? false) ? 'checked' : '' }}>
                    <span class="checkbox-label">数字を必ず含める (0-9)</span>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" name="symbols" {{ ($options['symbols'] ?? false) ? 'checked' : '' }}>
                    <span class="checkbox-label">記号を必ず含める (!@#$%^&*()_+-=[]{}|;:,.<>?)</span>
                </div>
            </div>
        </div>
        
        <button type="submit" class="generate-btn">パスワード生成</button>
    </form>
    
    @if(isset($generatedPassword) && $generatedPassword)
    <div class="result">
        <h3>生成されたパスワード</h3>
        <div class="password-container">
            <input type="text" class="password-input" id="password" value="{{ $generatedPassword }}" readonly>
            <button type="button" class="copy-btn" onclick="copyToClipboard()">📋 コピー</button>
        </div>
    </div>
    @endif
    
    {{-- パスワード履歴表示（ログイン時のみ） --}}
    @auth
        @if(isset($passwordHistories) && count($passwordHistories) > 0)
        <div class="result">
            <h3>📋 最近の生成履歴</h3>
            <div class="history-list">
                @foreach($passwordHistories as $history)
                    <div class="history-item">
                        <div class="history-password">{{ is_array($history) ? $history['password'] : $history->password }}</div>
                        <div class="history-info">
                            長さ: {{ is_array($history) ? $history['length'] : $history->length }} | 
                            生成日時: {{ \Carbon\Carbon::parse(is_array($history) ? $history['created_at'] : $history->created_at)->format('Y年m月d日 H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @endauth
</div>
@endsection

@push('scripts')
<script>
    function copyToClipboard() {
        const passwordInput = document.getElementById('password');
        const copyBtn = document.querySelector('.copy-btn');
        
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
            document.execCommand('copy');
            showCopySuccess(copyBtn);
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
    });
</script>
@endpush
