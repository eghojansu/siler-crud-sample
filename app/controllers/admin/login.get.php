<?php

namespace App;

use Siler\Http;

render('login', [
    'error' => Http\flash('login_error'),
]);
