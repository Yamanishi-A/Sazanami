<?php
return array(
    'upload' => array(
        'allowed_extensions' => array('jpg', 'jpeg', 'png', 'gif'),
        'paths' => array(
            'cover_image' => '/assets/img/covers/',
            'icon'  => '/assets/img/icons/',
        ),
    ),

    'display' => array(
        'trending_limit' => 4, // トップページに表示するトレンドプレイリストの件数
    ),
);