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

        if (!Session::get('user_id')) {
            $controller = Request::active()->controller;
            $action = Request::active()->action;
            
            $is_welcome = ($controller === 'Controller_Welcome');
            $is_auth = ($controller === 'Controller_Auth');
            $is_public_playlist = ($controller === 'Controller_Playlists' && in_array($action, ['discover', 'view']));

            if (!$is_welcome && !$is_auth && !$is_public_playlist) {
                return Response::redirect('/');
            }
        }
    }
}