<?php
namespace app\news\validate;
use think\Validate;

class News extends Validate
{
    // 验证规则
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'url' => 'require|url',
        'date' => 'dateFormat:Y-m-d H:i:s'
    ];
}