<?php

namespace App;

use Siler\Http;

$filter = [
    'id = :id',
    'id' => $params['id'],
];
$category = db\findone('category', $filter);

if ($category) {
    render('admin/category/form', [
        'action_message' => Http\flash('action_message'),
        'category' => $category,
        'title' => 'Edit Category',
    ]);
} else {
    Http\setsession('action_message', 'Category was not found');

    redirect('admin/category');
}
