<?php

namespace App;

use Siler\Http;

render('admin/category/index', [
    'categories' => db\find('category'),
    'action_message' => Http\flash('action_message'),
]);
