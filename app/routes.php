<?php

use Siler\Container;
use Siler\Route;

$request = [Container\get('method'), Container\get('path')];

// Check user
if (
    // on admin path
    false !== strpos($request[1], '/admin')
    // but not on login path
    && false === strpos($request[1], '/login')
    // not login yet
    && !App\user('login')
) {
    App\redirect();
}

// Load from blog routes
Route\files(__DIR__.'/controllers/blog/routes', '/', $request);

// Admin routes
Route\files(__DIR__.'/controllers/admin', '/admin', $request);

// Defaults to article
Route\get('/{slug}?', __DIR__.'/controllers/blog/article.php', $request);

// No match
require __DIR__.'/controllers/error/404.php';
