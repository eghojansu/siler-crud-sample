<?php

session_start();

require __DIR__.'/../vendor/autoload.php';

App\env_load(__DIR__.'/../.env');

require __DIR__.'/../app/bootstrap.php';
require __DIR__.'/../app/routes.php';
