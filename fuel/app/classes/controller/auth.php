<?php

class Controller_Auth extends \Controller_Base
{
    // サインアップ処理
    public function action_signup()
    {
        if (\Input::method() == 'POST') {
            $username = \Input::post('username');
            $email = \Input::post('email');
            $password = \Input::post('password');

            try {
                // ▼ 修正: Modelにユーザー作成を依頼
                $insert_id = \Model_User::create_user($username, $email, $password);

                if ($insert_id) {
                    \Session::set('user_id', $insert_id);
                    \Response::redirect('playlists/index');
                }
            } catch (\Exception $e) {
                // メールアドレス重複などのエラーハンドリング
                $data['error'] = 'このメールアドレスは既に登録されているか、エラーが発生しました。';
            }
        }

        return \View::forge('auth/signup', isset($data) ? $data : array());
    }

    // ログイン処理
    public function action_login()
    {
        if (\Input::method() == 'POST') {
            $email = \Input::json('email') ?: \Input::post('email');
            $password = \Input::json('password') ?: \Input::post('password');
            
            $redirect_to = \Input::json('redirect_to', '/playlists/index');
            if ($redirect_to === '/') {
                $redirect_to = '/playlists/index';
            }

            // ▼ 修正: Modelに認証を依頼
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

        return \View::forge('auth/login', isset($data) ? $data : array());
    }

    // ログアウト処理 (※セッション破棄とリダイレクトはControllerの管轄なのでこのままでOKです)
    public function action_logout()
    {
        \Session::destroy();
        \Response::redirect('/');
    }
}