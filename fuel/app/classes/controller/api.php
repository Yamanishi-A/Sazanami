<?php

class Controller_Api extends Controller_Base
{
    /**
     * 配列をJSON形式にして返す共通メソッド
     */
    private function response_json($data, $status = 200)
    {
        $response = \Response::forge(json_encode($data), $status);
        $response->set_header('Content-Type', 'application/json');
        return $response;
    }

    // ==========================================
    // Ajax: プレイリストの作成
    // ==========================================
    public function action_create_playlist()
    {
        if (\Input::method() == 'POST') {
            // JavaScriptの fetch() からJSON形式で送られてくるデータを受け取る
            $title = \Input::json('title');
            $description = \Input::json('description');

            if (empty($title)) {
                return $this->response_json(array('error' => 'タイトルは必須です'), 400);
            }

            list($insert_id, $rows) = \DB::insert('playlists')->set(array(
                'user_id'     => $this->current_user['id'],
                'title'       => $title,
                'description' => $description,
                'created_at'  => date('Y-m-d H:i:s'),
            ))->execute();

            // 成功したら、画面側ですぐに表示できるよう作成したデータを返す
            return $this->response_json(array(
                'id'          => $insert_id,
                'title'       => $title,
                'description' => $description,
            ));
        }
    }

    // ==========================================
    // Ajax: 楽曲の追加（URL解析込み）
    // ==========================================
    public function action_add_track()
    {
        if (\Input::method() == 'POST') {
            $playlist_id = \Input::json('playlist_id');
            $url = \Input::json('url');

            try {
                if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
                    throw new \Exception('Invalid URL');
                }

                // 1. プラットフォームの精密な判定
                $platform = 'other';
                if (strpos($url, 'youtu') !== false) {
                    $platform = 'youtube';
                } elseif (strpos($url, 'spotify') !== false) {
                    $platform = 'spotify';
                } elseif (strpos($url, 'nicovideo') !== false || strpos($url, 'nico.ms') !== false) {
                    $platform = 'niconico';
                }

                // 2. URLから曲名、アーティスト、サムネイルを自動取得！
                $metadata = $this->fetch_metadata($url, $platform);
                $title = $metadata['title'];
                $artist = $metadata['artist'];
                $thumbnail = $metadata['thumbnail'];

                // 3. 楽曲の登録・取得 (URLが既にDBにあるかチェック)
                $existing_track = \DB::select()->from('tracks')->where('url', $url)->execute()->current();
                
                if ($existing_track) {
                    $track_id = $existing_track['id'];
                    // 既に登録済みの場合は、DB上のタイトルを使う
                    $title = $existing_track['title']; 
                } else {
                    // 新規URLの場合はINSERT
                    list($track_id, $rows) = \DB::insert('tracks')->set(array(
                        'url'        => $url,
                        'platform'   => $platform,
                        'title'      => $title,
                        'artist'     => $artist,
                        'thumbnail_url'  => $thumbnail,
                        'created_at' => date('Y-m-d H:i:s'),
                    ))->execute();
                }

                // 4. 中間テーブルへ紐付け
                list($pt_id, $rows) = \DB::insert('playlist_tracks')->set(array(
                    'playlist_id' => $playlist_id,
                    'track_id'    => $track_id,
                    'created_at'  => date('Y-m-d H:i:s'),
                ))->execute();

                // フロントエンドに返すデータ（サムネイル等も含める）
                return $this->response_json(array(
                    'success' => true,
                    'track' => array(
                        'id' => $pt_id,
                        'title' => $title,
                        'platform' => $platform,
                        'url' => $url,
                        'thumbnail_url' => $metadata['thumbnail'], // 画面表示用に返す
                        'artist' => $metadata['artist']
                    )
                ));

            } catch (\Exception $e) {
                return $this->response_json(array('error' => $e->getMessage()), 400);
            }
        }
    }

    // ==========================================
    // Ajax: 楽曲の削除
    // ==========================================
    public function action_delete_track()
    {
        if (\Input::method() == 'POST') {
            $pt_id = \Input::json('pt_id');

            if (empty($pt_id)) {
                return $this->response_json(array('error' => 'IDが指定されていません'), 400);
            }

            try {
                // playlist_tracks テーブルから紐付けデータを削除
                \DB::delete('playlist_tracks')->where('id', $pt_id)->execute();
                
                return $this->response_json(array('success' => true));
            } catch (\Exception $e) {
                return $this->response_json(array('error' => '削除に失敗しました'), 500);
            }
        }
    }

    /**
     * URLからタイトル、アーティスト名、サムネイルを取得するヘルパーメソッド
     */
    private function fetch_metadata($url, $platform)
    {
        // 初期値（取得できなかった場合）
        $meta = array(
            'title'     => 'Unknown Track',
            'artist'    => 'Unknown Artist',
            'thumbnail' => ''
        );

        // 1. oEmbed API を利用する (YouTube, Spotifyに有効。APIキー不要！)
        $oembed_url = '';
        if ($platform === 'youtube') {
            $oembed_url = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';
        } elseif ($platform === 'spotify') {
            $oembed_url = 'https://open.spotify.com/oembed?url=' . urlencode($url);
        }

        if ($oembed_url) {
            // 外部通信を行う（エラーが起きてもアプリが止まらないよう ignore_errors を設定）
            $context = stream_context_create(array('http' => array('ignore_errors' => true)));
            $response = @file_get_contents($oembed_url, false, $context);
            
            if ($response) {
                $data = json_decode($response, true);
                if ($data) {
                    $meta['title']     = isset($data['title']) ? $data['title'] : $meta['title'];
                    $meta['artist']    = isset($data['author_name']) ? $data['author_name'] : $meta['artist'];
                    $meta['thumbnail'] = isset($data['thumbnail_url']) ? $data['thumbnail_url'] : $meta['thumbnail'];
                    return $meta; // oEmbedで取得成功したらここで返す
                }
            }
        }

        // 2. oEmbedが使えない場合（ニコニコ動画など）は、HTMLの <title> タグを直接スクレイピングする
        $context = stream_context_create(array('http' => array(
            'ignore_errors' => true,
            'header' => "User-Agent: Mozilla/5.0\r\n" // ボット弾きを回避するための偽装
        )));
        $html = @file_get_contents($url, false, $context);
        
        if ($html) {
            // 正規表現で <title>〇〇</title> の中身を抜き出す
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                $title = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
                // 「 - YouTube」などの不要な文字を削る簡易処理
                $title = str_replace(array(' - YouTube', ' - ニコニコ動画'), '', $title);
                $meta['title'] = $title;
            }
        }

        return $meta;
    }

    // ==========================================
    // Ajax: プレイリストの保存（作成・編集・画像アップロード）
    // ==========================================
    public function action_save_playlist()
    {
        if (\Input::method() == 'POST') {
            $id = \Input::post('id');
            $title = \Input::post('title');
            $description = \Input::post('description');
            
            // セッションからユーザーIDを取得（未ログインの場合は安全のため弾く）
            $user_id = \Session::get('user_id');
            if (!$user_id) {
                return $this->response_json(array('error' => 'ログインが必要です'), 401);
            }

            $cover_image = null;

            // ▼ 修正版: 完全にこの if ブロックの中だけでアップロード処理を行います
            // $_FILES['cover_image']['error'] も確認し、空のファイル送信も防ぎます
            if (!empty($_FILES) && isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                
                $upload_dir = DOCROOT . 'assets/img/covers/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $config = array(
                    'path' => $upload_dir,
                    'randomize' => true,
                    'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png', 'webp'),
                );

                try {
                    // ここでだけ process を呼びます
                    \Upload::process($config);

                    if (\Upload::is_valid()) {
                        \Upload::save();
                        $file = \Upload::get_files(0);
                        $cover_image = '/assets/img/covers/' . $file['saved_as'];
                    }
                } catch (\Exception $e) {
                    // 画像がない場合やエラー時はスルーして、文字データだけ保存へ進む
                }
            }

            try {
                if ($id) {
                    // ---------- 【編集（UPDATE）】 ----------
                    $update_data = array(
                        'title' => $title,
                        'description' => $description,
                    );
                    // 新しい画像がアップロードされた場合のみ、カラムを更新する
                    if (\Input::post('remove_cover') === '1') {
                        $update_data['cover_image'] = null;
                    }
                    if ($cover_image) {
                        $update_data['cover_image'] = $cover_image;
                    }
                    
                    \DB::update('playlists')->set($update_data)->where('id', $id)->where('user_id', $user_id)->execute();
                    
                    return $this->response_json(array('success' => true, 'mode' => 'edit', 'cover_image' => $cover_image));

                } else {
                    // ---------- 【新規作成（INSERT）】 ----------
                    $insert_data = array(
                        'user_id' => $user_id,
                        'title' => $title,
                        'description' => $description,
                        'cover_image' => $cover_image,
                        'created_at' => date('Y-m-d H:i:s'),
                    );
                    
                    list($new_id, $rows) = \DB::insert('playlists')->set($insert_data)->execute();
                    
                    return $this->response_json(array('success' => true, 'mode' => 'create', 'id' => $new_id, 'cover_image' => $cover_image));
                }
            } catch (\Exception $e) {
                return $this->response_json(array('error' => 'データベースエラーが発生しました: ' . $e->getMessage()), 500);
            }
        }
    }

    // ==========================================
    // Ajax: プレイリストの削除
    // ==========================================
    public function action_delete_playlist()
    {
        if (\Input::method() == 'POST') {
            $id = \Input::json('id');
            $user_id = \Session::get('user_id');

            if (empty($id) || !$user_id) {
                return $this->response_json(array('error' => '不正なリクエストです'), 400);
            }

            try {
                // 1. 中間テーブル（playlist_tracks）から関連する楽曲データを削除
                \DB::delete('playlist_tracks')->where('playlist_id', $id)->execute();
                
                // 2. プレイリスト本体を削除（他人のものを消せないよう user_id も条件に含める）
                \DB::delete('playlists')->where('id', $id)->where('user_id', $user_id)->execute();
                
                return $this->response_json(array('success' => true));
            } catch (\Exception $e) {
                return $this->response_json(array('error' => '削除に失敗しました'), 500);
            }
        }
    }

    // ==========================================
    // Ajax: ユーザー設定の更新（プロフィール・アイコン）
    // ==========================================
    public function action_update_settings()
    {
        if (\Input::method() == 'POST') {
            $user_id = \Session::get('user_id');
            if (!$user_id) return $this->response_json(array('error' => 'Unauthorized'), 401);

            $username = \Input::post('username');
            $bio = \Input::post('bio');
            // ▼ 修正: remove_icon に変更
            $remove_icon = \Input::post('remove_icon') === '1';

            $icon_url = null;

            // ▼ 修正: $_FILES['icon'] に変更
            if (!empty($_FILES) && isset($_FILES['icon']) && $_FILES['icon']['error'] !== UPLOAD_ERR_NO_FILE) {
                // 保存先ディレクトリも icons にすると綺麗です
                $upload_dir = DOCROOT . 'assets/img/icons/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                $config = array(
                    'path' => $upload_dir,
                    'randomize' => true,
                    'ext_whitelist' => array('jpg', 'jpeg', 'png', 'webp'),
                );

                try {
                    \Upload::process($config);
                    if (\Upload::is_valid()) {
                        \Upload::save();
                        $file = \Upload::get_files(0);
                        $icon_url = '/assets/img/icons/' . $file['saved_as'];
                    }
                } catch (\Exception $e) { /* Error handling */ }
            }

            try {
                $update_data = array('username' => $username, 'bio' => $bio);
                
                // ▼ 修正: DBのカラム名を icon に変更
                if ($remove_icon) {
                    $update_data['icon'] = null;
                } elseif ($icon_url) {
                    $update_data['icon'] = $icon_url;
                }

                \DB::update('users')->set($update_data)->where('id', $user_id)->execute();
                return $this->response_json(array('success' => true));
            } catch (\Exception $e) {
                return $this->response_json(array('error' => 'DB Error'), 500);
            }
        }
    }

    // ==========================================
    // Ajax: パスワードリセット
    // ==========================================
    public function action_reset_password()
    {
        if (\Input::method() == 'POST') {
            $user_id = \Session::get('user_id');
            $new_password = \Input::json('password');

            if (!$user_id || empty($new_password)) return $this->response_json(array('error' => 'Invalid request'), 400);

            try {
                // 簡易的なハッシュ化（実際はAuthパッケージ等の適切な方式に従ってください）
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                \DB::update('users')->set(array('password' => $hashed_password))->where('id', $user_id)->execute();
                return $this->response_json(array('success' => true));
            } catch (\Exception $e) {
                return $this->response_json(array('error' => 'DB Error'), 500);
            }
        }
    }

    // ==========================================
    // Ajax: アカウント削除
    // ==========================================
    public function action_delete_account()
    {
        if (\Input::method() == 'POST') {
            $user_id = \Session::get('user_id');
            if (!$user_id) return $this->response_json(array('error' => 'Unauthorized'), 401);

            try {
                // 関連データの削除
                \DB::delete('playlists')->where('user_id', $user_id)->execute();
                \DB::delete('users')->where('id', $user_id)->execute();
                
                \Session::destroy();
                return $this->response_json(array('success' => true));
            } catch (\Exception $e) {
                return $this->response_json(array('error' => 'Delete failed'), 500);
            }
        }
    }
}