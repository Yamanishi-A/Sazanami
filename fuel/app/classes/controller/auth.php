<?php

class Controller_Auth extends \Controller_Base
{
    /**
     * redirect_to を安全な相対パスに正規化する。
     * 外部 URL（http://... や //evil.com 等）はデフォルト値に置き換える。
     */
    private function sanitize_redirect($redirect_to, $default = '/users')
    {
        // 相対パス（/ で始まり // で始まらない）のみ許可
        if (is_string($redirect_to) && strpos($redirect_to, '/') === 0 && strpos($redirect_to, '//') !== 0) {
            return $redirect_to === '/' ? $default : $redirect_to;
        }
        return $default;
    }

    // サインアップ処理
    public function post_signup()
    {
        $username = \Input::json('username') ?: \Input::post('username');
        $email = \Input::json('email') ?: \Input::post('email');
        $password = \Input::json('password') ?: \Input::post('password');

        $redirect_to = $this->sanitize_redirect(\Input::json('redirect_to'));

        if (empty($username) || empty($email) || empty($password)) {
            return \Response::forge(json_encode(array('success' => false, 'error' => '全ての項目を入力してください')), 400, array('Content-Type' => 'application/json'));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return \Response::forge(json_encode(array('success' => false, 'error' => 'メールアドレスの形式が正しくありません')), 400, array('Content-Type' => 'application/json'));
        }
        if (strlen($password) < 8) {
            return \Response::forge(json_encode(array('success' => false, 'error' => 'パスワードは8文字以上で入力してください')), 400, array('Content-Type' => 'application/json'));
        }
        if (mb_strlen($username) > 50) {
            return \Response::forge(json_encode(array('success' => false, 'error' => 'ユーザー名は50文字以内で入力してください')), 400, array('Content-Type' => 'application/json'));
        }

        try {
            $insert_id = \Model_User::create_user($username, $email, $password);

            if ($insert_id) {
                \Session::rotate(); // セッション固定化攻撃対策
                \Session::set('user_id', $insert_id);

                if (\Input::is_ajax() || \Input::json('email')) {
                    return \Response::forge(json_encode(array(
                        'success' => true,
                        'redirect_to' => $redirect_to
                    )), 200, array('Content-Type' => 'application/json'));
                }
                
                \Response::redirect($redirect_to);
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

        $redirect_to = $this->sanitize_redirect(\Input::json('redirect_to'));

        if (empty($email) || empty($password)) {
            return \Response::forge(json_encode(array('success' => false, 'error' => 'メールアドレスとパスワードを入力してください')), 400, array('Content-Type' => 'application/json'));
        }

        $user = \Model_User::authenticate($email, $password);

        if ($user) {
            // 認証成功
            \Session::rotate(); // セッション固定化攻撃対策
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