<?php

namespace App;

use Siler\Request;

render('article', [
    'post' => db\find_post($params['slug'] ?? null, $params['category']),
    'prefix' => 'category/'.$params['category'].'/',
    'category' => $params['category'],
]);
