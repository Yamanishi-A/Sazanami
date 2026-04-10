<?php

class Controller_Base extends \Controller
{
    public $current_user = null;

    public function before()
    {
        parent::before();

        $user_id = \Session::get('user_id');

        if ($user_id) {
            $result = \Model_User::get_user_by_id($user_id);

            if ($result) {
                $this->current_user = $result;
            }
        }

        if (!\Session::get('user_id')) {
            $controller = \Request::active()->controller;
            $action = \Request::active()->action;
            
            $is_users = ($controller === 'Controller_Users');

            if ($controller === 'Controller_Users' && in_array($action, ['index', 'settings'])) {
                return \Response::redirect('/');
            }
        }
    }
}