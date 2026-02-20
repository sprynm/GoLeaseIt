<?php
Router::connect('/admin/settings/:action/*', array(
    'admin' => true, 
    'plugin' => 'settings', 
    'controller' => 'settings'
));
