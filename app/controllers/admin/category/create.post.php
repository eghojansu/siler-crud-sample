<?php

namespace App;

use Siler\Http;

$data = input([
    'category' => 'trim|required',
]);

if (empty($data['error'])) {
    db\insert('category', $data);

    Http\setsession('action_message', 'Category has been created');
    redirect('admin/category');
} else {
    Http\setsession('action_message', 'Error!<br>'.implode('<br>', $data['error']));
    redirect('admin/category/create');
}
