<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>My Playlists - Sazanami</title>
</head>
<body>
    <h1>こんにちは、<?php echo $user['username']; ?>さん！</h1>
    <a href="/auth/logout">ログアウト</a>
    <hr>

    <h2>プレイリスト一覧</h2>
    <a href="/playlists/create">＋ 新しいプレイリストを作成</a>

    <ul>
        <?php if (empty($playlists)): ?>
            <p>プレイリストがまだありません。「新しいプレイリストを作成」から作ってみましょう！</p>
        <?php else: ?>
            <?php foreach ($playlists as $playlist): ?>
                <li style="margin-bottom: 10px;">
                    <a href="/playlists/view/<?php echo $playlist['id']; ?>" style="font-size: 1.2em; font-weight: bold;">
                        <?php echo $playlist['title']; ?>
                    </a>
                    <br>
                    <small><?php echo $playlist['description']; ?></small>
                    <br>
                    <a href="/playlists/delete/<?php echo $playlist['id']; ?>" onclick="return confirm('本当に削除しますか？');" style="color: red; font-size: 0.8em;">
                        [削除する]
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</body>
</html>