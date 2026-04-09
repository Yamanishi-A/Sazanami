<?php
class Model_Playlist extends \Model
{
    public static function create($user_id, $title, $description, $cover_image = null)
    {
        list($insert_id, $rows) = \DB::insert('playlists')->set(array(
            'user_id'     => $user_id,
            'title'       => $title,
            'description' => $description,
            'cover_image' => $cover_image,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s')
        ))->execute();

        return $insert_id;
    }

    public static function update($id, $user_id, $update_data)
    {
        $update_data['updated_at'] = date('Y-m-d H:i:s');
        return \DB::update('playlists')
            ->set($update_data)
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->execute();
    }

    public static function delete($id, $user_id)
    {
        // 中間テーブルの削除
        \DB::delete('playlist_tracks')->where('playlist_id', $id)->execute();
        // プレイリスト本体の削除
        return \DB::delete('playlists')->where('id', $id)->where('user_id', $user_id)->execute();
    }

    public static function update_updated_at($playlist_id)
    {
        return \DB::update('playlists')
            ->value('updated_at', date('Y-m-d H:i:s'))
            ->where('id', $playlist_id)
            ->execute();
    }

    public static function reorder_tracks($playlist_id, $user_id, $track_ids)
    {
        // 権限チェック
        $playlist = \DB::select('user_id')->from('playlists')->where('id', $playlist_id)->execute()->current();
        if (!$playlist || $playlist['user_id'] != $user_id) {
            throw new \Exception('Unauthorized', 403);
        }

        foreach ($track_ids as $index => $pt_id) {
            \DB::update('playlist_tracks')
                ->value('sort_order', $index)
                ->where('id', $pt_id)
                ->where('playlist_id', $playlist_id)
                ->execute();
        }
        
        self::update_updated_at($playlist_id);
    }

    public static function get_user_playlists($user_id)
    {
        return \DB::select()->from('playlists')
            ->where('user_id', $user_id)
            ->order_by('created_at', 'desc')
            ->execute()
            ->as_array();
    }

    public static function get_playlist_by_id($id)
    {
        return \DB::select()->from('playlists')->where('id', $id)->execute()->current();
    }

    public static function get_discover_playlists()
    {
        // 1. プレイリスト本体と、作成者の情報を結合して取得
        $query = \DB::select(
            'p.id', 'p.title', 'p.cover_image',
            array('u.username', 'creatorName'),
            array('u.icon', 'creatorAvatar')
        )
        ->from(array('playlists', 'p'))
        ->join(array('users', 'u'), 'INNER')->on('p.user_id', '=', 'u.id')
        ->order_by('p.updated_at', 'desc')
        ->order_by('p.created_at', 'desc')
        ->execute()
        ->as_array();

        $discover_playlists = array();

        // 2. 各プレイリストに含まれる「楽曲数」と「プラットフォーム」を集計
        foreach ($query as $playlist) {
            $track_count = \DB::select(\DB::expr('COUNT(*) as count'))
                ->from('playlist_tracks')
                ->where('playlist_id', $playlist['id'])
                ->execute()
                ->get('count', 0);

            $platforms_query = \DB::select('t.platform')
                ->from(array('playlist_tracks', 'pt'))
                ->join(array('tracks', 't'), 'INNER')->on('pt.track_id', '=', 't.id')
                ->where('pt.playlist_id', $playlist['id'])
                ->group_by('t.platform')
                ->execute()
                ->as_array();

            // array_column を使ってシンプルに配列化
            $platforms = array_column($platforms_query, 'platform');

            $discover_playlists[] = array(
                'id'            => $playlist['id'],
                'title'         => $playlist['title'],
                'coverImage'    => $playlist['cover_image'],
                'creatorName'   => $playlist['creatorName'],
                'creatorAvatar' => $playlist['creatorAvatar'],
                'trackCount'    => (int)$track_count,
                'platforms'     => $platforms
            );
        }

        return $discover_playlists;
    }

    public static function get_trending_playlists($limit = 4)
    {
        $query = \DB::select(
            'p.id',
            'p.title',
            'p.cover_image',
            array('u.username', 'creatorName')
        )
        ->from(array('playlists', 'p'))
        ->join(array('users', 'u'), 'INNER')->on('p.user_id', '=', 'u.id')
        ->order_by('p.updated_at', 'desc')
        ->order_by('p.created_at', 'desc')
        ->limit($limit) // 取得件数を制限
        ->execute()
        ->as_array();

        $trending_playlists = array();

        foreach ($query as $playlist) {
            // 楽曲数を取得
            $track_count = \DB::select(\DB::expr('COUNT(*) as count'))
                ->from('playlist_tracks')
                ->where('playlist_id', $playlist['id'])
                ->execute()
                ->get('count', 0);

            // プラットフォームの種類を取得
            $platforms_query = \DB::select('t.platform')
                ->from(array('playlist_tracks', 'pt'))
                ->join(array('tracks', 't'), 'INNER')->on('pt.track_id', '=', 't.id')
                ->where('pt.playlist_id', $playlist['id'])
                ->group_by('t.platform')
                ->execute()
                ->as_array();

            // array_column でシンプルに配列化
            $platforms = array_column($platforms_query, 'platform');

            $trending_playlists[] = array(
                'id'            => $playlist['id'],
                'title'         => $playlist['title'],
                'coverImage'    => $playlist['cover_image'],
                'creatorName'   => $playlist['creatorName'],
                'trackCount'    => (int)$track_count,
                'platforms'     => $platforms
            );
        }

        return $trending_playlists;
    }
}