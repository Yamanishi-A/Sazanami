<?php
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	
	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
	'users/(:num)' => '/users/view/$1',
	'playlists/(:num)' => 'playlists/view/$1'
);
