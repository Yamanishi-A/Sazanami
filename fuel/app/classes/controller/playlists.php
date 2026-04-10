<?php

class Controller_Playlists extends \Controller_Base
{
    // 一覧表示
    public function action_index()
    {
        $data['playlists'] = \Model_Playlist::get_discover_playlists();
        return \Response::forge(\View::forge('playlists/index', $data));
    }

    // 新規作成
    public function post_create()
    {
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);

        \Model_Playlist::create(
            $user_id, 
            \Input::post('title'), 
            \Input::post('description')
        );
        \Response::redirect('/users');
    }

    // 詳細表示
    public function action_view($id = null)
    {
        if ($id === null) {
            return \Response::redirect('/playlists');
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
        $creator = \Model_User::get_user_by_id($playlist['user_id']);

        $view = \View::forge('playlists/view');
        $view->set('playlist', $playlist);
        $view->set('tracks', $tracks);
        $view->set('user', $user);
        $view->set('is_owner', $is_owner);
        $view->set('creator', $creator);

        return \Response::forge($view);
    }

    // 編集
    public function post_edit($id = null)
    {
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);

        $update_data = array(
            'title'       => \Input::post('title'),
            'description' => \Input::post('description')
        );

        try {
            \Model_Playlist::update($id, $user_id, $update_data);
            \Response::redirect('/users');
            return $this->response_json(array('success' => true));
        } catch (\Exception $e) {
            return $this->response_json(array('error' => '編集に失敗しました'), 500);
        }

    }

    // 削除
    public function post_delete($id = null)
    {
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);

        try {
            \Model_Playlist::delete($id, $user_id);
            \Response::redirect('/users');
            return $this->response_json(array('success' => true));
        } catch (\Exception $e) {
            return $this->response_json(array('error' => '削除に失敗しました'), 500);
        }

    }
}