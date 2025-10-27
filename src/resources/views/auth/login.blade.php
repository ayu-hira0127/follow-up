<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button {background:  #2c3e50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>
    <h2>ログイン</h2>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" required>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit">ログイン</button>
    </form>
    
    <p style="text-align: center; margin-top: 20px;">
        <a href="{{ route('register') }}">新規登録</a> | 
        <a href="/">ホーム</a>
    </p>
</body>
</html>