<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo $playlist['title']; ?> - Sazanami</title>
</head>
<body>
    <h1><?php echo $playlist['title']; ?></h1>
    <p><?php echo $playlist['description']; ?></p>
    <a href="/playlists/index">一覧に戻る</a>
    <hr>

    <h2>楽曲を追加する</h2>
    <?php if (isset($error)): ?>
        <p style="color: red; font-weight: bold;">エラー: <?php echo $error; ?></p>
    <?php endif; ?>

    <form action="/playlists/view/<?php echo $playlist['id']; ?>" method="POST">
        <input type="text" name="url" placeholder="YouTubeやSpotifyのURLを入力" required style="width: 400px;">
        <button type="submit">追加</button>
    </form>

    <hr>

    <h2>収録楽曲一覧</h2>
    <?php if (empty($tracks)): ?>
        <p>まだ楽曲が追加されていません。</p>
    <?php else: ?>
        <ul>
            <?php foreach ($tracks as $track): ?>
                <li style="margin-bottom: 10px;">
                    <strong><?php echo $track['title']; ?></strong> 
                    <span style="background-color: #eee; padding: 2px 5px; border-radius: 3px; font-size: 0.8em;">
                        <?php echo $track['platform']; ?>
                    </span>
                    <br>
                    <a href="<?php echo $track['url']; ?>" target="_blank" style="font-size: 0.9em;"><?php echo $track['url']; ?></a>
                    <br>
                    <small style="color: gray;">追加日時: <?php echo $track['added_at']; ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>