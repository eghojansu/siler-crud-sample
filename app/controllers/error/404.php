<?php

namespace App;

render('error', [
    'error' => '404 - Not Found',
    'text' => 'Page Not Found',
    'message' => "That's all we know.",
], 404);
