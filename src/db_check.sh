# パスワード履歴の詳細分析
sqlite3 /Users/hiramotoayuri/follow_up/src/database/database.sqlite "
SELECT 
    '🔐 生成パスワード: ' || password as 'パスワード',
    '📏 長さ: ' || length || '文字' as '詳細',
    CASE 
        WHEN json_extract(options, '$.lowercase') = 'true' THEN '✓小文字 '
        ELSE ''
    END ||
    CASE 
        WHEN json_extract(options, '$.uppercase') = 'true' THEN '✓大文字 '
        ELSE ''
    END ||
    CASE 
        WHEN json_extract(options, '$.numbers') = 'true' THEN '✓数字 '
        ELSE ''
    END ||
    CASE 
        WHEN json_extract(options, '$.symbols') = 'true' THEN '✓記号'
        ELSE ''
    END as '使用文字種',
    '🕐 ' || datetime(created_at, 'localtime') as '生成日時'
FROM password_histories 
ORDER BY created_at DESC;
"
