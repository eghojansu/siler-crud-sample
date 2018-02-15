<?php

namespace App;

use Siler\Http;

$data = input([
    'title' => 'trim|required',
    'content' => 'trim|required',
    'created_at' => 'trim|required|datetime',
]);
$categories = input([
    'categories' => 'required',
]);

if (empty($data['error'])) {
    $id = db\insert('post', $data + [
        'user_id' => user('id'),
        'slug' => slug($data['title']),
    ]);
    if ($id && empty($categories['error'])) {
        db\insert_batch('post_category', $categories['categories'], [
            'post_id' => $id,
            'category_id' => null,
        ]);
    }

    Http\setsession('action_message', 'Post has been created');
    redirect('admin');
} else {
    Http\setsession('action_message', 'Error!<br>'.implode('<br>', $data['error']));
    redirect('admin/post/create');
}
