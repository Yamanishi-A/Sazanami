<?php
class Model_Track extends \Model
{
    public static function add_to_playlist($playlist_id, $user_id, $url)
    {
        $playlist = \DB::select('user_id')->from('playlists')->where('id', $playlist_id)->execute()->current();
        if (!$playlist || $playlist['user_id'] != $user_id) {
            throw new \Exception('このプレイリストを編集する権限がありません', 403);
        }
        
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

    public static function delete_from_playlist($pt_id, $user_id)
    {
        $pt_record = \DB::select('playlist_id')->from('playlist_tracks')->where('id', $pt_id)->execute()->current();
        if (!$pt_record) return false;

        $playlist_id = $pt_record['playlist_id'];
        $playlist = \DB::select('user_id')->from('playlists')->where('id', $playlist_id)->execute()->current();
        if (!$playlist || $playlist['user_id'] != $user_id) {
            throw new \Exception('この楽曲を削除する権限がありません', 403);
        }

        \DB::delete('playlist_tracks')->where('id', $pt_id)->execute();
        \Model_Playlist::update_updated_at($playlist_id);
        return true;
    }

    private static function detect_platform($url)
    {
        if (strpos($url, 'youtu') !== false) return 'youtube';
        if (strpos($url, 'nicovideo') !== false || strpos($url, 'nico.ms') !== false) return 'niconico';
        return 'other';
    }

    /**
     * SSRF 対策：URL のホストがプライベート/予約済み IP に解決されないか検証する
     */
    private static function is_safe_url($url)
    {
        $parsed = parse_url($url);

        // HTTP/HTTPS のみ許可
        if (!isset($parsed['scheme']) || !in_array(strtolower($parsed['scheme']), array('http', 'https'))) {
            return false;
        }

        $host = isset($parsed['host']) ? $parsed['host'] : '';
        if (empty($host)) return false;

        // ホスト名を IP に解決
        $ip = gethostbyname($host);
        if ($ip === $host) return false; // DNS 解決失敗

        $long_ip = ip2long($ip);
        if ($long_ip === false) return false; // IPv6 等、ip2long が扱えない場合は拒否

        // アクセスを禁止するプライベート/予約済みアドレス範囲
        $blocked_ranges = array(
            array('0.0.0.0',         '0.255.255.255'),   // "This" network
            array('10.0.0.0',        '10.255.255.255'),  // Private
            array('100.64.0.0',      '100.127.255.255'), // Shared address space
            array('127.0.0.0',       '127.255.255.255'), // Loopback
            array('169.254.0.0',     '169.254.255.255'), // Link-local (クラウドメタデータ等)
            array('172.16.0.0',      '172.31.255.255'),  // Private
            array('192.168.0.0',     '192.168.255.255'), // Private
            array('198.18.0.0',      '198.19.255.255'),  // Benchmark testing
            array('240.0.0.0',       '255.255.255.255'), // Reserved
        );

        foreach ($blocked_ranges as $range) {
            if ($long_ip >= ip2long($range[0]) && $long_ip <= ip2long($range[1])) {
                return false;
            }
        }

        return true;
    }

    private static function fetch_metadata($url, $platform)
    {
        $meta = array('title' => 'Unknown Track', 'artist' => 'Unknown Artist', 'thumbnail' => '');

        $oembed_url = '';
        if ($platform === 'youtube') {
            $oembed_url = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';
        }

        if ($oembed_url) {
            $context = stream_context_create(array('http' => array('ignore_errors' => true, 'timeout' => 5)));
            $response = @file_get_contents($oembed_url, false, $context);
            if ($response && ($data = json_decode($response, true))) {
                $meta['title']     = isset($data['title']) ? $data['title'] : $meta['title'];
                $meta['artist']    = isset($data['author_name']) ? $data['author_name'] : $meta['artist'];
                $meta['thumbnail'] = isset($data['thumbnail_url']) ? $data['thumbnail_url'] : $meta['thumbnail'];
                return $meta;
            }
        }

        // HTMLスクレイピング（SSRF チェック通過済みの URL のみフェッチ）
        if (!self::is_safe_url($url)) {
            return $meta;
        }

        $context = stream_context_create(array('http' => array('ignore_errors' => true, 'timeout' => 5, 'header' => "User-Agent: Mozilla/5.0\r\n")));
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