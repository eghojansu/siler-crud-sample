<?php

use Siler\Container;
use Siler\Http;
use Siler\Twig;

App\record('start');

Container\set('path', Http\path());
Container\set('method', Http\Request\method());

$twig = Twig\init(__DIR__.'/templates', dirname(__DIR__).'/var/twig', App\env('DEBUG'));
    $twig->addFunction(new Twig_SimpleFunction('url', 'Siler\Http\url'));
    $twig->addFunction(new Twig_SimpleFunction('record', 'App\record'));
    $twig->addGlobal('app', [
        'title' => 'Siler-Blog-Example',
        'desc'  => 'Simple Blog built with Siler Framework',
    ]);
    $twig->addGlobal('base', Http\url(''));
    $twig->addGlobal('path', Container\get('path'));
    $twig->addGlobal('user', App\user());
    $twig->addGlobal('debug', App\env('DEBUG'));
