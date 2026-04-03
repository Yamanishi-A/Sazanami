<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Welcome extends Controller_Base
{
    public function action_index()
    {
        // ログインユーザーの情報を取得（ヘッダー用）
        $data['user'] = $this->current_user;

        // ==========================================
        // ▼ 追加: トレンドプレイリストの取得（DBから）
        // ==========================================
        $query = \DB::select(
            'p.id',
            'p.title',
            'p.cover_image',
            array('u.username', 'creatorName')
        )
        ->from(array('playlists', 'p'))
        ->join(array('users', 'u'), 'INNER')->on('p.user_id', '=', 'u.id')
        ->order_by('p.created_at', 'desc')
        ->limit(4)
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

            $platforms = array();
            foreach ($platforms_query as $pq) {
                $platforms[] = $pq['platform'];
            }

            $trending_playlists[] = array(
                'id'            => $playlist['id'],
                'title'         => $playlist['title'],
                'coverImage'    => $playlist['cover_image'],
                'creatorName'   => $playlist['creatorName'],
                'trackCount'    => (int)$track_count,
                'platforms'     => $platforms
            );
        }

        $data['trending_playlists'] = $trending_playlists;
        // ==========================================

        return Response::forge(View::forge('welcome/index', $data));
    }
    
	/**
	 * A typical "Hello, Bob!" type example.  This uses a Presenter to
	 * show how to use them.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_hello()
	{
		return Response::forge(Presenter::forge('welcome/hello'));
	}

	/**
	 * The 404 action for the application.
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_404()
	{
		return Response::forge(Presenter::forge('welcome/404'), 404);
	}
}
