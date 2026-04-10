<?php

class Controller_Users extends \Controller_Base
{
    // 一覧表示
    public function action_index()
    {
        $data['user'] = $this->current_user;
        $data['playlists'] = \Model_Playlist::get_user_playlists($this->current_user['id']);

        return \View::forge('users/index', $data);
    }

    // ユーザー設定
    public function action_settings()
    {
        $data['user'] = $this->current_user;
        return \View::forge('users/settings', $data);
    }

    public function action_view($target_user_id = null)
    {
        if ($target_user_id === null) {
            return \Response::redirect('/playlists');
        }

        // ターゲットとなるユーザーの情報を取得
        $target_user = \Model_User::get_user_by_id($target_user_id);
        if (!$target_user) {
            return \Response::redirect('/playlists'); // 存在しないユーザーなら戻す
        }

        // ヘッダー用のログインユーザー情報と、表示対象のユーザー情報を渡す
        $data['user'] = $this->current_user;
        $data['target_user'] = $target_user;

        if ($this->current_user && $this->current_user['id'] == $target_user_id) {
            $data['playlists'] = \Model_Playlist::get_user_playlists($target_user_id);
            return \Response::forge(\View::forge('users/index', $data));
            
        } else {
            // 他人のページの場合
            $data['target_user'] = $target_user;
            $data['playlists'] = \Model_Playlist::get_rich_user_playlists($target_user_id);
            
            // ユーザープロフィール画面を表示する
            return \Response::forge(\View::forge('users/user', $data));
        }
    }
}