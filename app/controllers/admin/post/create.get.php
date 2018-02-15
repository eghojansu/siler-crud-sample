<?php

namespace App;

use Siler\Http;

render('admin/post/form', [
    'action_message' => Http\flash('action_message'),
    'title' => 'New Post',
    'categories' => db\find('category'),
    'post' => [
        'created_at' => date('Y-m-d H:i:s'),
    ]
]);
