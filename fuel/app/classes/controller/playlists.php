<?php

class Controller_Playlists extends Controller_Base
{
    // 一覧表示
    public function action_index()
    {
        $data['user'] = $this->current_user;
        
        $data['playlists'] = \DB::select()->from('playlists')
            ->where('user_id', $this->current_user['id'])
            ->order_by('created_at', 'desc')
            ->execute()
            ->as_array();

        return \View::forge('playlists/index', $data);
    }

    // 新規作成
    public function action_create()
    {
        if (\Input::method() == 'POST') {
            $title = \Input::post('title');
            $description = \Input::post('description');

            \DB::insert('playlists')->set(array(
                'user_id'     => $this->current_user['id'],
                'title'       => $title,
                'description' => $description,
                'created_at'  => date('Y-m-d H:i:s'),
            ))->execute();

            \Response::redirect('playlists/index');
        }
        return \View::forge('playlists/create');
    }

    // 詳細表示
    public function action_view($id = null)
    {
        if ( ! $id) \Response::redirect('playlists/index');

        // プレイリストの所有者チェック
        $playlist = \DB::select()->from('playlists')
            ->where('id', $id)
            ->where('user_id', $this->current_user['id'])
            ->execute()->current();

        if ( ! $playlist) {
            \Response::redirect('playlists/index');
        }

        // 楽曲追加
        if (\Input::method() == 'POST') {
            $url = \Input::post('url');
            
            try {
                $track_info = $this->_parse_url($url);

                $existing_track = \DB::select()->from('tracks')->where('url', $url)->execute()->current();
                
                if ($existing_track) {
                    $track_id = $existing_track['id'];
                } else {
                    list($track_id, $rows) = \DB::insert('tracks')->set(array(
                        'url'        => $url,
                        'platform'   => $track_info['platform'],
                        'title'      => $track_info['title'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ))->execute();
                }

                \DB::insert('playlist_tracks')->set(array(
                    'playlist_id' => $id,
                    'track_id'    => $track_id,
                    'created_at'  => date('Y-m-d H:i:s'),
                ))->execute();

                \Response::redirect('playlists/view/' . $id);

            } catch (\Exception $e) {
                $data['error'] = $e->getMessage();
            }
        }

        // プレイリスト内の楽曲取得
        $data['tracks'] = \DB::select('t.*', array('pt.id', 'pt_id'), array('pt.created_at', 'added_at'))
            ->from(array('tracks', 't'))
            ->join(array('playlist_tracks', 'pt'), 'INNER')
            ->on('t.id', '=', 'pt.track_id')
            ->where('pt.playlist_id', $id)
            ->order_by('pt.created_at', 'desc')
            ->execute()->as_array();

        $data['playlist'] = $playlist;
        return \View::forge('playlists/view', $data);
    }

    // 編集
    public function action_edit($id = null)
    {
        if (\Input::method() == 'POST') {
            \DB::update('playlists')
                ->set(array(
                    'title'       => \Input::post('title'),
                    'description' => \Input::post('description'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ))
                ->where('id', $id)
                ->where('user_id', $this->current_user['id']) 
                ->execute();

            \Response::redirect('playlists/index');
        }
    }

    // 削除
    public function action_delete($id = null)
    {
        \DB::delete('playlists')
            ->where('id', $id)
            ->where('user_id', $this->current_user['id'])
            ->execute();

        \Response::redirect('playlists/index');
    }

    // url解析
    private function _parse_url($url)
    {
        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid URL');
        }

        $platform = '';
        $title = '';

        \Config::load('sazanami', true);
        $platforms = \Config::get('sazanami.supported_platforms', array());

        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            $platform = 'youtube';
            $title = 'YouTube Video';
        } elseif (strpos($url, 'spotify.com') !== false) {
            $platform = 'spotify';
            $title = 'Spotify Track';
        } elseif (strpos($url, 'nicovideo.jp') !== false) {
            $platform = 'niconico';
            $title = 'Niconico Video';
        } else {
            throw new \Exception('Invalid URL: 未対応のプラットフォームです');
        }

        return array(
            'platform' => $platform,
            'title'    => $title,
        );
    }
}