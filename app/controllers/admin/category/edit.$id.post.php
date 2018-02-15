<?php

namespace App;

use Siler\Http;

$filter = [
    'id = :id',
    'id' => $params['id'],
];
$category = db\findone('category', $filter);

if ($category) {
    $data = input([
        'category' => 'trim|required',
    ]);

    if ($data['error']) {
        Http\setsession('action_message', 'Error!<br>'.implode('<br>', $data['error']));
        redirect('admin/category/edit/'.$params['id']);
    } else {
        db\update('category', $data, $filter);

        Http\setsession('action_message', 'Category has been updated');
        redirect('admin/category');
    }
} else {
    Http\setsession('action_message', 'Category was not found');
    redirect('admin/category');
}
