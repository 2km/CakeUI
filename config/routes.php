<?php
use Cake\Routing\Router;

Router::plugin('CakeUI', function ($routes) {
    $routes->fallbacks();
});
