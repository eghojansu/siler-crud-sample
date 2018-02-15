<?php

namespace App;

use Siler\Http;

if (user('login')) {
    redirect('admin');
}

$user = db\findone('user', ['username = ?', Http\Request\post('username')]);
if ($user) {
    if (password_verify(Http\Request\post('password'), $user['password'])) {
        unset($user['password']);

        user($user);
        redirect('admin');
    }
}

Http\setsession('login_error', 'Invalid credentials.');
redirect('admin/login');
