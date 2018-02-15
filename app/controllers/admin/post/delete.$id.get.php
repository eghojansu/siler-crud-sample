<?php

namespace App;

use Siler\Http;

$filter = [
    'id = :id and user_id = :user',
    'id' => $params['id'],
    'user' => user('id'),
];
db\delete('post', $filter);

Http\setsession('action_message', 'Post has been deleted');
redirect('admin');
