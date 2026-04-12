<?php

class Controller_Playlists extends \Controller_Base
{
    // 一覧表示
    public function action_index()
    {
        $data['playlists'] = \Model_Playlist::get_discover_playlists();
        return \Response::forge(\View::forge('playlists/index', $data));
    }

    // 詳細表示
    public function action_view($id = null)
    {
        if (filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false) {
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

}