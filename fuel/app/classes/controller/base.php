<?php

class Controller_Base extends \Controller
{
    public $current_user = null;

    public function before()
    {
        parent::before();

        // CSRF 検証：すべての POST リクエストに対して実施
        if (\Input::method() === 'POST') {
            $header_token = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : '';
            $post_token   = \Input::post(\Config::get('security.csrf_token_key', 'fuel_csrf_token'), '');
            $expected     = \Security::fetch_token();

            if ($header_token !== $expected && $post_token !== $expected) {
                if (\Input::is_ajax()) {
                    $res = \Response::forge(json_encode(array('error' => 'CSRF token mismatch')), 403);
                    $res->set_header('Content-Type', 'application/json');
                    return $res;
                }
                return \Response::forge('CSRF validation failed', 403);
            }
        }

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