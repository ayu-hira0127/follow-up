<?php
/**
 * パスワードジェネレーター
 * ローカルで編集可能な環境で作成
 */
function generatePassword($length = 8, $options = []) {
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    
    // 基本文字セット（常に英数字は使用）
    $characters = $lowercase . $uppercase . $numbers;
    $requiredChars = '';
    
    // 記号を追加
    if (isset($options['symbols']) && $options['symbols']) {
        $characters .= $symbols;
        $requiredChars .= $symbols[rand(0, strlen($symbols) - 1)];
    }
    
    // 必須文字の指定（基本セットから必ず1文字ずつ取得）
    if (isset($options['lowercase']) && $options['lowercase']) {
        $requiredChars .= $lowercase[rand(0, strlen($lowercase) - 1)];
    }
    
    if (isset($options['uppercase']) && $options['uppercase']) {
        $requiredChars .= $uppercase[rand(0, strlen($uppercase) - 1)];
    }
    
    if (isset($options['numbers']) && $options['numbers']) {
        $requiredChars .= $numbers[rand(0, strlen($numbers) - 1)];
    }
    
    // 必須文字が指定されたパスワード長を超える場合の調整
    if (strlen($requiredChars) > $length) {
        return str_shuffle(substr($requiredChars, 0, $length));
    }
    
    // 必須文字以外の残り文字数を生成
    $remainingLength = $length - strlen($requiredChars);
    $password = $requiredChars;
    
    for ($i = 0; $i < $remainingLength; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    // パスワードをシャッフル
    return str_shuffle($password);
}

// フォームからの送信を処理
$generatedPassword = '';
$length = 8;
$options = [
    'lowercase' => false,
    'uppercase' => false,
    'numbers' => false,
    'symbols' => false
];

if ($_POST) {
    $length = isset($_POST['length']) ? intval($_POST['length']) : 8;
    $options = [
        'lowercase' => isset($_POST['lowercase']),
        'uppercase' => isset($_POST['uppercase']),
        'numbers' => isset($_POST['numbers']),
        'symbols' => isset($_POST['symbols'])
    ];
    $generatedPassword = generatePassword($length, $options);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワードジェネレーター</title>
    <link rel="stylesheet" href="css/password_generator.css">
</head>
<body>
    <div class="container">
        <h1>パスワードジェネレーター</h1>
        
        <form method="POST">
            <div class="form-group">
                <label for="length">パスワードの長さ:</label>
                <input type="number" id="length" name="length" value="<?php echo $length; ?>" min="4" max="50">
            </div>
            
            <div class="form-group">
                <label>文字種オプション</label>
                <div class="description">
                    💡 基本は英数字（a-z, A-Z, 0-9）を使用します<br>
                    チェックした文字種を必ず1文字以上含めます
                </div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" name="lowercase" <?php echo $options['lowercase'] ? 'checked' : ''; ?>>
                        <span class="checkbox-label">小文字を必ず含める (a-z)</span>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="uppercase" <?php echo $options['uppercase'] ? 'checked' : ''; ?>>
                        <span class="checkbox-label">大文字を必ず含める (A-Z)</span>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="numbers" <?php echo $options['numbers'] ? 'checked' : ''; ?>>
                        <span class="checkbox-label">数字を必ず含める (0-9)</span>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" name="symbols" <?php echo $options['symbols'] ? 'checked' : ''; ?>>
                        <span class="checkbox-label">記号を必ず含める (!@#$%^&*()_+-=[]{}|;:,.<>?)</span>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="generate-btn">パスワード生成</button>
        </form>
        
        <?php if ($generatedPassword): ?>
        <div class="result">
            <h3>生成されたパスワード</h3>
            <div class="password-container">
                <input type="text" class="password-input" id="password" value="<?php echo htmlspecialchars($generatedPassword); ?>" readonly>
                <button type="button" class="copy-btn" onclick="copyToClipboard()">📋 コピー</button>
            </div>
        </div>
        <?php endif; ?>
    </div>

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
            button.textContent = 'コピーしました！';
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
</body>
</html>
