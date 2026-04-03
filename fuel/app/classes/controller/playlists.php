<?php

class Controller_Playlists extends Controller_Base
{
    // 一覧表示
    public function action_index()
    {
        $data['user'] = $this->current_user;
        
        $data['playlists'] = \DB::select()->from('playlists')
            ->where('user_id', $this->current_user['id'])
            ->order_by('created_at', 'desc')
            ->execute()
            ->as_array();

        return \View::forge('playlists/index', $data);
    }

    // 新規作成
    public function action_create()
    {
        if (\Input::method() == 'POST') {
            $title = \Input::post('title');
            $description = \Input::post('description');

            \DB::insert('playlists')->set(array(
                'user_id'     => $this->current_user['id'],
                'title'       => $title,
                'description' => $description,
                'created_at'  => date('Y-m-d H:i:s'),
            ))->execute();

            \Response::redirect('playlists/index');
        }
        return \View::forge('playlists/create');
    }

    // 詳細表示
    public function action_view($id = null)
    {
        if ($id === null) {
            return \Response::redirect('playlists/index');
        }

        // ログインユーザーIDを取得（未ログインの場合は null）
        $user_id = \Session::get('user_id');

        // プレイリスト本体の取得
        $playlist = \DB::select()->from('playlists')->where('id', $id)->execute()->current();
        if (!$playlist) {
            return \Response::redirect('/'); // 存在しない場合はトップへ
        }

        // ==========================================
        // ▼ 追加: ログインユーザーが「このプレイリストの作者」かどうか判定
        // ==========================================
        $is_owner = ($user_id && $playlist['user_id'] == $user_id);

        // --- 以下は既存の楽曲取得処理など ---
        $query = \DB::select('t.*', array('pt.id', 'pt_id'))
            ->from(array('playlist_tracks', 'pt'))
            ->join(array('tracks', 't'), 'INNER')->on('pt.track_id', '=', 't.id')
            ->where('pt.playlist_id', $id)
            ->order_by('pt.created_at', 'asc')
            ->execute();
        $tracks = $query->as_array();

        // ユーザー情報を取得（ヘッダー表示用）
        $user = $user_id ? \DB::select()->from('users')->where('id', $user_id)->execute()->current() : null;

        $view = \View::forge('playlists/view');
        $view->set('playlist', $playlist);
        $view->set('tracks', $tracks);
        $view->set('user', $user);
        
        // ▼ 追加: 判定フラグをビュー（HTML）に渡す
        $view->set('is_owner', $is_owner);

        return \Response::forge($view);
    }

    // 編集
    public function action_edit($id = null)
    {
        if (\Input::method() == 'POST') {
            \DB::update('playlists')
                ->set(array(
                    'title'       => \Input::post('title'),
                    'description' => \Input::post('description'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ))
                ->where('id', $id)
                ->where('user_id', $this->current_user['id']) 
                ->execute();

            \Response::redirect('playlists/index');
        }
    }

    // 削除
    public function action_delete($id = null)
    {
        \DB::delete('playlists')
            ->where('id', $id)
            ->where('user_id', $this->current_user['id'])
            ->execute();

        \Response::redirect('playlists/index');
    }

    // ユーザー設定
    public function action_settings()
    {
        $data['user'] = $this->current_user;
        return \View::forge('user/settings', $data);
    }

    // 一覧ページ
    public function action_discover()
    {
        // 1. プレイリスト本体と、作成者(usersテーブル)の情報を結合して取得
        $query = \DB::select(
            'p.id',
            'p.title',
            'p.cover_image',
            array('u.username', 'creatorName') // usersテーブルのusernameをcreatorNameとして取得
        )
        ->from(array('playlists', 'p'))
        ->join(array('users', 'u'), 'INNER')->on('p.user_id', '=', 'u.id')
        ->order_by('p.created_at', 'desc') // 新しい順
        ->execute()
        ->as_array();

        $discover_playlists = array();

        // 2. 各プレイリストに含まれる「楽曲数」と「プラットフォームの種類」を取得
        foreach ($query as $playlist) {
            
            // そのプレイリストの楽曲数をカウント
            $track_count = \DB::select(\DB::expr('COUNT(*) as count'))
                ->from('playlist_tracks')
                ->where('playlist_id', $playlist['id'])
                ->execute()
                ->get('count', 0);

            // そのプレイリストに含まれる楽曲のプラットフォーム(youtube, spotify等)を重複なしで取得
            $platforms_query = \DB::select('t.platform')
                ->from(array('playlist_tracks', 'pt'))
                ->join(array('tracks', 't'), 'INNER')->on('pt.track_id', '=', 't.id')
                ->where('pt.playlist_id', $playlist['id'])
                ->group_by('t.platform') // 種類をまとめる
                ->execute()
                ->as_array();

            $platforms = array();
            foreach ($platforms_query as $pq) {
                $platforms[] = $pq['platform'];
            }

            // フロントエンド(Knockout.js)が要求するプロパティ名に合わせて配列を構築
            $discover_playlists[] = array(
                'id'            => $playlist['id'],
                'title'         => $playlist['title'],
                'coverImage'    => $playlist['cover_image'],
                'creatorName'   => $playlist['creatorName'],
                'creatorAvatar' => null, // 現状アバター画像機能がないためnull
                'trackCount'    => (int)$track_count,
                'platforms'     => $platforms
            );
        }

        // ビューにデータを渡す
        $data['playlists'] = $discover_playlists;

        return \Response::forge(\View::forge('playlists/discover', $data));
    }

    // url解析
    private function _parse_url($url)
    {
        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid URL');
        }

        $platform = '';
        $title = '';

        \Config::load('sazanami', true);
        $platforms = \Config::get('sazanami.supported_platforms', array());

        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            $platform = 'youtube';
            $title = 'YouTube Video';
        } elseif (strpos($url, 'spotify.com') !== false) {
            $platform = 'spotify';
            $title = 'Spotify Track';
        } elseif (strpos($url, 'nicovideo.jp') !== false) {
            $platform = 'niconico';
            $title = 'Niconico Video';
        } else {
            throw new \Exception('Invalid URL: 未対応のプラットフォームです');
        }

        return array(
            'platform' => $platform,
            'title'    => $title,
        );
    }
}