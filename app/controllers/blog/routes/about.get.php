<?php

namespace App;

render('article', [
    'post' => [
        'post' => [
            'title' => 'About',
            'slug' => 'about',
            'content' => '<p>This is a simple blog built with <a href="https://siler.leocavalcante.com">Siler Framework</a>.</p>',
            'nometa' => true,
        ],
    ],
]);
