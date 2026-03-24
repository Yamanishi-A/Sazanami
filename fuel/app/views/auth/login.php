<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Log In - Sazanami</title>
</head>
<body>
    <h1>Sazanami - ログイン</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="/auth/login" method="POST">
        <div>
            <label>メールアドレス:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>パスワード:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">ログイン</button>
    </form>
    <p><a href="/auth/signup">アカウント登録はこちら</a></p>
</body>
</html>