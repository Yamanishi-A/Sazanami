<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Sazanami</title>
</head>
<body>
    <h1>Sazanami - アカウント作成</h1>
    <form action="/auth/signup" method="POST">
        <div>
            <label>ユーザー名:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>メールアドレス:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>パスワード:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">登録する</button>
    </form>
    <p><a href="/auth/login">すでにアカウントをお持ちの方はこちら（ログイン）</a></p>
</body>
</html>