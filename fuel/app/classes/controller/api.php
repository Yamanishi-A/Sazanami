<?php

class Controller_Api extends \Controller_Base
{
    private function response_json($data, $status = 200)
    {
        $response = \Response::forge(json_encode($data), $status);
        $response->set_header('Content-Type', 'application/json');
        return $response;
    }

    public function post_create_playlist()
    {
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);
        
        $title = \Input::json('title');
        $description = \Input::json('description');

        if (empty($title)) {
            return $this->response_json(array('error' => 'タイトルは必須です'), 400);
        }

        try {
            $insert_id = \Model_Playlist::create($user_id, $title, $description);
            return $this->response_json(array('id' => $insert_id, 'title' => $title, 'description' => $description));
        } catch (\Exception $e) {
            return $this->response_json(array('error' => '作成に失敗しました'), 500);
        }
    }

    public function post_add_track()
    {
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);

        $playlist_id = \Input::json('playlist_id');
        $url = \Input::json('url');

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->response_json(array('error' => 'Invalid URL'), 400);
        }

        try {
            $track_data = \Model_Track::add_to_playlist($playlist_id, $user_id, $url);
            \Model_Playlist::update_updated_at($playlist_id);
            return $this->response_json(array('success' => true, 'track' => $track_data));
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 400;
            return $this->response_json(array('error' => $e->getMessage()), $status);
        }
    }

    public function post_delete_track()
    {
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);

        $pt_id = \Input::json('pt_id');
        if (empty($pt_id)) return $this->response_json(array('error' => 'IDが指定されていません'), 400);

        try {
            \Model_Track::delete_from_playlist($pt_id, $user_id);
            return $this->response_json(array('success' => true));
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return $this->response_json(array('error' => '削除に失敗しました'), $status);
        }
    }

    public function post_save_playlist()
    {
        $id = \Input::post('id');
        $title = \Input::post('title');
        $description = \Input::post('description');
        
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);

        $cover_image = $this->handle_upload('cover_image');

        try {
            if ($id) {
                $update_data = array('title' => $title, 'description' => $description);
                if (\Input::post('remove_cover') === '1') $update_data['cover_image'] = null;
                if ($cover_image) $update_data['cover_image'] = $cover_image;
                
                \Model_Playlist::update($id, $user_id, $update_data);
                return $this->response_json(array('success' => true, 'mode' => 'edit', 'cover_image' => $cover_image));
            } else {
                $new_id = \Model_Playlist::create($user_id, $title, $description, $cover_image);
                return $this->response_json(array('success' => true, 'mode' => 'create', 'id' => $new_id, 'cover_image' => $cover_image));
            }
        } catch (\Exception $e) {
            return $this->response_json(array('error' => 'データベースエラーが発生しました'), 500);
        }
    }

    public function post_delete_playlist()
    {
        $id = \Input::json('id');
        $user_id = \Session::get('user_id');

        if (empty($id) || !$user_id) return $this->response_json(array('error' => '不正なリクエストです'), 400);

        try {
            \Model_Playlist::delete($id, $user_id);
            return $this->response_json(array('success' => true));
        } catch (\Exception $e) {
            return $this->response_json(array('error' => '削除に失敗しました'), 500);
        }
    }

    public function post_update_settings()
    {
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);

        $update_data = array(
            'username' => \Input::post('username'),
            'bio' => \Input::post('bio')
        );

        $icon_url = $this->handle_upload('icon');

        if (\Input::post('remove_icon') === '1') {
            $update_data['icon'] = null;
        } elseif ($icon_url) {
            $update_data['icon'] = $icon_url;
        }

        try {
            \Model_User::update_settings($user_id, $update_data);
            return $this->response_json(array('success' => true));
        } catch (\Exception $e) {
            return $this->response_json(array('error' => 'DB Error'), 500);
        }
    }

    public function post_reset_password()
    {
        $user_id = \Session::get('user_id');
        $new_password = \Input::json('password');

        if (!$user_id || empty($new_password)) return $this->response_json(array('error' => 'Invalid request'), 400);

        try {
            \Model_User::reset_password($user_id, $new_password);
            return $this->response_json(array('success' => true));
        } catch (\Exception $e) {
            return $this->response_json(array('error' => $e->getMessage()), 500);
        }
    }

    public function post_delete_account()
    {
        $user_id = \Session::get('user_id');
        if (!$user_id) return $this->response_json(array('error' => 'ログインが必要です'), 401);

        try {
            \Model_User::delete_account($user_id);
            \Session::destroy();
            return $this->response_json(array('success' => true));
        } catch (\Exception $e) {
            return $this->response_json(array('error' => 'Delete failed'), 500);
        }
    }

    public function post_reorder_tracks()
    {
        $user_id = \Session::get('user_id');
        $playlist_id = \Input::json('playlist_id');
        $track_ids = \Input::json('track_ids');

        if (!$user_id || empty($playlist_id) || !is_array($track_ids)) {
            return $this->response_json(array('error' => 'Invalid request'), 400);
        }

        try {
            \Model_Playlist::reorder_tracks($playlist_id, $user_id, $track_ids);
            return $this->response_json(array('success' => true));
        } catch (\Exception $e) {
            $status = $e->getCode() ?: 500;
            return $this->response_json(array('error' => $e->getMessage()), $status);
        }
    }

    /**
     * ファイルアップロードを処理する共通ヘルパーメソッド
     */
    private function handle_upload($field_name)
    {
        if (!empty($_FILES) && isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] !== UPLOAD_ERR_NO_FILE) {
            \Config::load('sazanami', true);

            $web_path = \Config::get('sazanami.upload.paths.' . $field_name);
            $allowed_ext = \Config::get('sazanami.upload.allowed_extensions');

            $upload_dir = DOCROOT . ltrim($web_path, '/');

            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $config = array(
                'path' => $upload_dir,
                'randomize' => true,
                'ext_whitelist' => $allowed_ext,
            );

            try {
                \Upload::process($config);
                if (\Upload::is_valid()) {
                    \Upload::save();
                    $file = \Upload::get_files(0);
                    return $web_path . $file['saved_as'];
                }
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
}