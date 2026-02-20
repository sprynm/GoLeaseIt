<?php
Router::connect('/' . Configure::read('Plugins.EmailForms.slug') . '/success/*', array(
    'plugin' => 'email_forms', 
    'controller' => 'email_forms', 
    'action' => 'success'
));