<?php

class Controller_Auth extends Controller_Base
{
    // サインアップ処理
    public function action_signup()
    {
        if (Input::method() == 'POST') {
            $username = Input::post('username');
            $email = Input::post('email');
            $password = Input::post('password');

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            list($insert_id, $rows_affected) = DB::insert('users')->set(array(
                'username'      => $username,
                'email'         => $email,
                'password_hash' => $password_hash,
                'created_at'    => date('Y-m-d H:i:s'), // To DATETIME
            ))->execute();

            if ($insert_id) {
                Session::set('user_id', $insert_id);
                Response::redirect('playlists/index');
            }
        }

        return View::forge('auth/signup');
    }

    // ログイン処理
    public function action_login()
    {
        if (Input::method() == 'POST') {
            $email = Input::post('email');
            $password = Input::post('password');

            $user = DB::select()->from('users')->where('email', $email)->execute()->current();

            if ($user && password_verify($password, $user['password_hash'])) {
                Session::set('user_id', $user['id']);
                Response::redirect('playlists/index');
            } else {
                $data['error'] = 'メールアドレスかパスワードが間違っています。';
            }
        }

        return View::forge('auth/login', isset($data) ? $data : array());
    }

    // ログアウト処理
    public function action_logout()
    {
        Session::destroy();
        Response::redirect('/');
    }
}