<?php

namespace App;

use Siler\Request;

render('article', [
    'post' => db\find_post($params['slug'] ?? null),
]);
