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
    $data = input([
        'title' => 'trim|required',
        'content' => 'trim|required',
        'created_at' => 'trim|required|datetime',
    ]);

    if ($data['error']) {
        Http\setsession('action_message', 'Error!<br>'.implode('<br>', $data['error']));
        redirect('admin/post/edit/'.$params['id']);
    } else {
        db\update('post', $data, $filter);

        Http\setsession('action_message', 'Post has been updated');
        redirect('admin');
    }
} else {
    Http\setsession('action_message', 'Post was not found');
    redirect('admin');
}
