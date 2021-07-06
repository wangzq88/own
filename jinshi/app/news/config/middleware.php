<?php
// 应用中间件定义文件
return [
    'alias' => [
        'auth'  => app\news\middleware\Auth::class,
		'admin'  => app\news\middleware\Admin::class,
    ],
];