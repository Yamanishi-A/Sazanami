<?php

class Controller_Auth extends \Controller_Base
{
    // サインアップ処理
    public function post_signup()
    {
        $username = \Input::post('username');
        $email = \Input::post('email');
        $password = \Input::post('password');

        try {
            $insert_id = \Model_User::create_user($username, $email, $password);

            if ($insert_id) {
                \Session::set('user_id', $insert_id);
                \Response::redirect('/users');
            }
        } catch (\Exception $e) {
            if (\Input::is_ajax() || \Input::json('email')) {
                return \Response::forge(json_encode(array(
                    'success' => false, 
                    'error' => 'このメールアドレスは既に登録されているか、エラーが発生しました。'
                )), 401, array('Content-Type' => 'application/json'));
            }
            $data['error'] = 'このメールアドレスは既に登録されているか、エラーが発生しました。';
        }
    }

    // ログイン処理
    public function post_login()
    {
        $email = \Input::json('email') ?: \Input::post('email');
        $password = \Input::json('password') ?: \Input::post('password');
        
        $redirect_to = \Input::json('redirect_to', '/users');
        if ($redirect_to === '/') {
            $redirect_to = '/users';
        }

        $user = \Model_User::authenticate($email, $password);

        if ($user) {
            // 認証成功
            \Session::set('user_id', $user['id']);
            
            if (\Input::is_ajax() || \Input::json('email')) {
                return \Response::forge(json_encode(array(
                    'success' => true,
                    'redirect_to' => $redirect_to
                )), 200, array('Content-Type' => 'application/json'));
            }
            \Response::redirect($redirect_to);
        } else {
            // 認証失敗
            if (\Input::is_ajax() || \Input::json('email')) {
                return \Response::forge(json_encode(array(
                    'success' => false, 
                    'error' => 'メールアドレスかパスワードが間違っています。'
                )), 401, array('Content-Type' => 'application/json'));
            }
            $data['error'] = 'メールアドレスかパスワードが間違っています。';
        }
    }

    // ログアウト処理
    public function post_logout()
    {
        \Session::destroy();
        \Response::redirect('/');
    }
}