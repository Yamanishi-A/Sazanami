<?php

class Controller_Base extends Controller
{
    public $current_user = null;

    public function before()
    {
        parent::before();

        $user_id = Session::get('user_id');

        if ($user_id) {
            $result = DB::select()
                ->from('users')
                ->where('id', $user_id)
                ->execute()
                ->current();

            if ($result) {
                $this->current_user = $result;
            }
        }

        $uri = Uri::string();
        $public_uris = array('auth/login', 'auth/signup', '', 'welcome/index', 'playlists/discover', 'playlists/view'); 

        if ( ! $this->current_user && ! in_array($uri, $public_uris)) {
            Response::redirect('');
        }
    }
}