<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Create Playlist - Sazanami</title>
</head>
<body>
    <h1>新しいプレイリストを作成</h1>
    
    <form action="/playlists/create" method="POST">
        <div style="margin-bottom: 10px;">
            <label>タイトル:</label><br>
            <input type="text" name="title" required style="width: 300px;">
        </div>
        <div style="margin-bottom: 10px;">
            <label>説明:</label><br>
            <textarea name="description" rows="4" style="width: 300px;"></textarea>
        </div>
        <button type="submit">作成する</button>
    </form>
    
    <br>
    <a href="/playlists/index">一覧に戻る</a>
</body>
</html>