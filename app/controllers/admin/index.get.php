<?php

namespace App;

use Siler\Http;

render('admin/dashboard', [
    'posts' => db\find('post', ['user_id = ?', user('id')]),
    'action_message' => Http\flash('action_message'),
]);
