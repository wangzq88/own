<?php
namespace app\news\validate;
use think\Validate;

class Tags extends Validate
{
    // 验证规则
    protected $rule = [
        'tag' => 'require',
        'category' => 'require|chsAlphaNum'
    ];
}