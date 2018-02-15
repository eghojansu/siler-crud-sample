<?php

namespace App;

use Siler\Http;

render('admin/category/form', [
    'action_message' => Http\flash('action_message'),
    'title' => 'New Category',
]);
