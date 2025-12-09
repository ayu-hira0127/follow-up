@extends('layouts.app')

@section('title', 'パスワード編集')

@section('content')
<div class="container">
    <h1>✏️ パスワード編集</h1>
    
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif
    
    <a href="{{ route('password.list') }}" class="back-link">
        ← パスワード一覧に戻る
    </a>
    
    <form method="POST" action="{{ route('password.update', $password->id) }}" class="edit-form">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">名前（必須）:</label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $password->name) }}" 
                   required 
                   placeholder="例: Gmailアカウント">
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="url">URL（必須）:</label>
            <input type="url" 
                   id="url" 
                   name="url" 
                   value="{{ old('url', $password->url) }}" 
                   required 
                   placeholder="例: https://mail.google.com">
            @error('url')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="password">パスワード:</label>
            <div class="password-edit-container">
                <input type="password" 
                       id="password" 
                       name="password" 
                       value="" 
                       placeholder="変更する場合は新しいパスワードを入力"
                       class="password-edit-input">
                <button type="button" 
                        class="toggle-password-edit-btn" 
                        onclick="togglePasswordEdit()">
                    表示
                </button>
            </div>
            <p class="form-help">パスワードを変更しない場合は、このフィールドを空欄のままにしてください。現在のパスワードがそのまま使用されます。</p>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="update-btn">
                更新
            </button>
            <a href="{{ route('password.list') }}" class="cancel-btn">
                キャンセル
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function togglePasswordEdit() {
        const passwordField = document.getElementById('password');
        const button = document.querySelector('.toggle-password-edit-btn');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            button.textContent = '非表示';
        } else {
            passwordField.type = 'password';
            button.textContent = '表示';
        }
    }
</script>
@endpush

@push('styles')
<style>
    .edit-form {
        margin-top: 30px;
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

    .form-group input[type="text"],
    .form-group input[type="url"],
    .form-group input[type="password"] {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1em;
        transition: all 0.3s ease;
        background: #f7fafc;
        color: #2d3748;
    }

    .form-group input[type="text"]:focus,
    .form-group input[type="url"]:focus,
    .form-group input[type="password"]:focus {
        outline: none;
        border-color: #2c3e50;
        background: white;
        box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
    }

    .password-edit-container {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .password-edit-input {
        flex: 1;
    }

    .toggle-password-edit-btn {
        padding: 14px 18px;
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.95em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .toggle-password-edit-btn:hover {
        background: linear-gradient(135deg, #5a6268, #6c757d);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    .form-help {
        font-size: 0.9em;
        color: #718096;
        margin-top: 8px;
        padding: 10px;
        background: linear-gradient(135deg, #ecf0f1, #f8f9fa);
        border-left: 4px solid #2c3e50;
        border-radius: 4px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .update-btn {
        flex: 1;
        min-width: 200px;
        padding: 16px 24px;
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.1em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .update-btn:hover {
        background: linear-gradient(135deg, #34495e, #2c3e50);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
    }

    .cancel-btn {
        flex: 1;
        min-width: 200px;
        padding: 16px 24px;
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        text-decoration: none;
        border-radius: 12px;
        font-size: 1.1em;
        font-weight: 600;
        text-align: center;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .cancel-btn:hover {
        background: linear-gradient(135deg, #5a6268, #6c757d);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        color: white;
        text-decoration: none;
    }
</style>
@endpush

