<?php
class Controller_Playlists extends Controller_Base
{
    public function action_index()
    {
        // Controller_Baseのbefore()で取得したログインユーザー情報をViewに渡す
        $data['user'] = $this->current_user;
        return View::forge('playlists/index', $data);
    }
}