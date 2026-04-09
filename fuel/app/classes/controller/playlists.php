<?php

class Controller_Playlists extends \Controller_Base
{
    // 一覧表示
    public function action_index()
    {
        $data['user'] = $this->current_user;
        $data['playlists'] = \Model_Playlist::get_user_playlists($this->current_user['id']);

        return \View::forge('playlists/index', $data);
    }

    // 新規作成
    public function action_create()
    {
        if (\Input::method() == 'POST') {
            \Model_Playlist::create(
                $this->current_user['id'], 
                \Input::post('title'), 
                \Input::post('description')
            );
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

        $user_id = \Session::get('user_id');
        
        // Modelからプレイリスト情報を取得
        $playlist = \Model_Playlist::get_playlist_by_id($id);
        if (!$playlist) {
            return \Response::redirect('/');
        }

        $is_owner = ($user_id && $playlist['user_id'] == $user_id);
        
        // Modelから楽曲とユーザー情報を取得
        $tracks = \Model_Track::get_tracks_by_playlist($id);
        $user = \Model_User::get_user_by_id($user_id);

        $view = \View::forge('playlists/view');
        $view->set('playlist', $playlist);
        $view->set('tracks', $tracks);
        $view->set('user', $user);
        $view->set('is_owner', $is_owner);

        return \Response::forge($view);
    }

    // 編集
    public function action_edit($id = null)
    {
        if (\Input::method() == 'POST') {
            $update_data = array(
                'title'       => \Input::post('title'),
                'description' => \Input::post('description')
            );
            \Model_Playlist::update($id, $this->current_user['id'], $update_data);
            \Response::redirect('playlists/index');
        }
    }

    // 削除
    public function action_delete($id = null)
    {
        \Model_Playlist::delete($id, $this->current_user['id']);
        \Response::redirect('playlists/index');
    }

    // ユーザー設定
    public function action_settings()
    {
        $data['user'] = $this->current_user;
        return \View::forge('user/settings', $data);
    }

    // 一覧ページ (Discover)
    public function action_discover()
    {
        // 複雑な集計ロジックはすべてModelが担当
        $data['playlists'] = \Model_Playlist::get_discover_playlists();
        return \Response::forge(\View::forge('playlists/discover', $data));
    }
}