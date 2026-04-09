<?php
class Model_User extends \Model
{
    public static function create_user($username, $email, $password)
    {
        // Model内でハッシュ化とDB保存を完結させる
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        list($insert_id, $rows_affected) = \DB::insert('users')->set(array(
            'username'      => $username,
            'email'         => $email,
            'password_hash' => $password_hash,
            'created_at'    => date('Y-m-d H:i:s'),
        ))->execute();

        return $insert_id;
    }

    public static function authenticate($email, $password)
    {
        // メールアドレスからユーザーを検索
        $user = \DB::select()->from('users')->where('email', $email)->execute()->current();

        // ユーザーが存在し、かつパスワードが一致するか検証
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user; // 成功時はユーザー情報を返す
        }

        return false; // 失敗時はfalseを返す
    }


    public static function update_settings($user_id, $update_data)
    {
        $update_data['updated_at'] = date('Y-m-d H:i:s');
        return \DB::update('users')->set($update_data)->where('id', $user_id)->execute();
    }

    public static function reset_password($user_id, $new_password)
    {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        return \DB::update('users')->set(array(
            'password_hash' => $hashed_password, 
            'updated_at' => date('Y-m-d H:i:s')
        ))->where('id', $user_id)->execute();
    }

    public static function delete_account($user_id)
    {
        \DB::delete('playlists')->where('user_id', $user_id)->execute();
        return \DB::delete('users')->where('id', $user_id)->execute();
    }

    public static function get_user_by_id($user_id)
    {
        if (!$user_id) return null;
        $result = \DB::select()
                ->from('users')
                ->where('id', $user_id)
                ->execute()
                ->current();

        return ($result) ? $result : null;
    }
}