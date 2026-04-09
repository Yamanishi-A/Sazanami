<?php
class Model_Track extends \Model
{
    public static function add_to_playlist($playlist_id, $url)
    {
        $platform = self::detect_platform($url);
        $metadata = self::fetch_metadata($url, $platform);
        $title = $metadata['title'];
        $artist = $metadata['artist'];

        // 楽曲が既に存在するかチェック
        $existing_track = \DB::select()->from('tracks')->where('url', $url)->execute()->current();
        
        if ($existing_track) {
            $track_id = $existing_track['id'];
            $title = $existing_track['title']; 
        } else {
            list($track_id, $rows) = \DB::insert('tracks')->set(array(
                'url'           => $url,
                'platform'      => $platform,
                'title'         => $title,
                'artist'        => $artist,
                'thumbnail_url' => $metadata['thumbnail'],
                'created_at'    => date('Y-m-d H:i:s'),
            ))->execute();
        }

        // 中間テーブルへ登録
        list($pt_id, $rows) = \DB::insert('playlist_tracks')->set(array(
            'playlist_id' => $playlist_id,
            'track_id'    => $track_id,
            'created_at'  => date('Y-m-d H:i:s'),
        ))->execute();

        return array(
            'pt_id' => $pt_id,
            'id' => $track_id,
            'title' => $title,
            'platform' => $platform,
            'url' => $url,
            'thumbnail_url' => $metadata['thumbnail'],
            'artist' => $artist
        );
    }

    public static function delete_from_playlist($pt_id)
    {
        $pt_record = \DB::select('playlist_id')->from('playlist_tracks')->where('id', $pt_id)->execute()->current();
        if ($pt_record) {
            $playlist_id = $pt_record['playlist_id'];
            \DB::delete('playlist_tracks')->where('id', $pt_id)->execute();
            \Model_Playlist::update_updated_at($playlist_id);
            return true;
        }
        return false;
    }

    private static function detect_platform($url)
    {
        if (strpos($url, 'youtu') !== false) return 'youtube';
        if (strpos($url, 'spotify') !== false) return 'spotify';
        if (strpos($url, 'nicovideo') !== false || strpos($url, 'nico.ms') !== false) return 'niconico';
        return 'other';
    }

    private static function fetch_metadata($url, $platform)
    {
        $meta = array('title' => 'Unknown Track', 'artist' => 'Unknown Artist', 'thumbnail' => '');

        $oembed_url = '';
        if ($platform === 'youtube') {
            $oembed_url = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';
        } elseif ($platform === 'spotify') {
            $oembed_url = 'https://open.spotify.com/oembed?url=' . urlencode($url);
        }

        if ($oembed_url) {
            $context = stream_context_create(array('http' => array('ignore_errors' => true)));
            $response = @file_get_contents($oembed_url, false, $context);
            if ($response && ($data = json_decode($response, true))) {
                $meta['title']     = isset($data['title']) ? $data['title'] : $meta['title'];
                $meta['artist']    = isset($data['author_name']) ? $data['author_name'] : $meta['artist'];
                $meta['thumbnail'] = isset($data['thumbnail_url']) ? $data['thumbnail_url'] : $meta['thumbnail'];
                return $meta;
            }
        }

        // HTMLスクレイピング
        $context = stream_context_create(array('http' => array('ignore_errors' => true, 'header' => "User-Agent: Mozilla/5.0\r\n")));
        $html = @file_get_contents($url, false, $context);
        if ($html && preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            $title = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
            $meta['title'] = str_replace(array(' - YouTube', ' - ニコニコ動画'), '', $title);
        }
        return $meta;
    }

    public static function get_tracks_by_playlist($playlist_id)
    {
        return \DB::select('t.*', array('pt.id', 'pt_id'))
            ->from(array('playlist_tracks', 'pt'))
            ->join(array('tracks', 't'), 'INNER')->on('pt.track_id', '=', 't.id')
            ->where('pt.playlist_id', $playlist_id)
            ->order_by('pt.sort_order', 'asc')
            ->order_by('pt.created_at', 'asc')
            ->execute()
            ->as_array();
    }
}