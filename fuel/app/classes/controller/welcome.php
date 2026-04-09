<?php
class Controller_Welcome extends \Controller_Base
{
    public function action_index()
    {
        $data['user'] = $this->current_user;

        $data['trending_playlists'] = \Model_Playlist::get_trending_playlists(4);

        return \Response::forge(\View::forge('welcome/index', $data));
    }
    
    public function action_hello()
    {
        return \Response::forge(\Presenter::forge('welcome/hello'));
    }

    public function action_404()
    {
        return \Response::forge(\Presenter::forge('welcome/404'), 404);
    }
}