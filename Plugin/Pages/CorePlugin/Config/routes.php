<?php
Router::connect('/access/*', array(
	'plugin' => 'pages',
	'controller' => 'pages',
	'action' => 'access'
));

//allow accessing pages by id
Router::connect('/page/:path'
  , array(
    'plugin' => 'pages'
    , 'controller' => 'pages'
    ,	'action' => 'view'
  )
  , array(
    'pass' => array('path')
    , 'path' => '[0-9]+'
  )
);