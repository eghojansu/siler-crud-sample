<?php

namespace App;

use Siler\Http;

$filter = [
    'id = :id and user_id = :user',
    'id' => $params['id'],
    'user' => user('id'),
];
$post = db\findone('post', $filter);

if ($post) {
    render('admin/post/form', [
        'action_message' => Http\flash('action_message'),
        'post' => $post,
        'title' => 'Edit Post',
        'categories' => db\find('category'),
    ]);
} else {
    Http\setsession('action_message', 'Post was not found');

    redirect('admin');
}
