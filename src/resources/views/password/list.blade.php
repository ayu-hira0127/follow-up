@extends('layouts.app')

@section('title', '保存したパスワード一覧')

@section('content')
<div class="container password-list-page">
    <h1>🔐 保存したパスワード一覧</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif
    
    <a href="{{ route('home') }}" class="back-link">
        ← パスワードジェネレーターに戻る
    </a>
    
    @if(isset($savedPasswords) && count($savedPasswords) > 0)
        <div class="password-list">
            <table>
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>URL</th>
                        <th>パスワード</th>
                        <th>保存日時</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($savedPasswords as $password)
                    <tr>
                        <td>
                            <strong>{{ $password->name }}</strong>
                        </td>
                        <td>
                            <a href="{{ $password->url }}" target="_blank" rel="noopener noreferrer">
                                {{ $password->url }}
                            </a>
                        </td>
                        <td>
                            <div class="password-actions">
                                <input type="password" 
                                       class="password-field" 
                                       value="{{ $password->password }}" 
                                       readonly 
                                       id="password-{{ $password->id }}">
                                <button type="button" 
                                        class="toggle-password-btn" 
                                        onclick="togglePassword({{ $password->id }})">
                                    表示
                                </button>
                                <button type="button" 
                                        class="copy-password-btn" 
                                        onclick="copyPassword({{ $password->id }})">
                                    📋 コピー
                                </button>
                            </div>
                        </td>
                        <td class="date-info">
                            {{ \Carbon\Carbon::parse($password->created_at)->format('Y年m月d日 H:i') }}
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                <a href="{{ route('password.edit', $password->id) }}" 
                                   class="edit-btn">
                                    ✏️ 編集
                                </a>
                                <button type="button" 
                                        class="delete-btn" 
                                        onclick="deletePassword({{ $password->id }})">
                                    🗑️ 削除
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <p>
                まだパスワードが保存されていません。
            </p>
            <a href="{{ route('home') }}">
                パスワードを生成して保存する
            </a>
        </div>
    @endif
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function togglePassword(id) {
        const passwordField = document.getElementById('password-' + id);
        const button = event.target;
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            button.textContent = '非表示';
        } else {
            passwordField.type = 'password';
            button.textContent = '表示';
        }
    }
    
    function copyPassword(id) {
        const passwordField = document.getElementById('password-' + id);
        const button = event.target;
        
        // パスワードを選択してコピー
        passwordField.select();
        passwordField.setSelectionRange(0, 99999);
        
        try {
            navigator.clipboard.writeText(passwordField.value).then(function() {
                showCopySuccess(button);
            }).catch(function() {
                document.execCommand('copy');
                showCopySuccess(button);
            });
        } catch (err) {
            document.execCommand('copy');
            showCopySuccess(button);
        }
    }
    
    function showCopySuccess(button) {
        const originalText = button.textContent;
        const originalClass = button.className;
        
        button.textContent = '✅ コピーしました！';
        button.classList.add('copied');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.className = originalClass;
        }, 2000);
    }
    
    function deletePassword(id) {
        if (confirm('このパスワードを削除してもよろしいですか？')) {
            const form = document.getElementById('delete-form');
            form.action = '/password/' + id;
            form.submit();
        }
    }
</script>
@endpush

