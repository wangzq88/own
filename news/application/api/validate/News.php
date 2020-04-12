<?php
namespace app\api\validate;
use think\Validate;

class News extends Validate
{
    // 验证规则
    protected $rule = [
        'content' => 'require|min:5',
        'url' => 'require|url',
        'date' => 'dateFormat:Y-m-d H:i:s',
        'type' => 'require|number|in:1,2'
    ];
    
    protected $message  =   [
        'content.require' => '内容必须',
        'content.max'     => '内容不能少于5个字符',
        'date.dateFormat'   => '日期必须如下格式：Y-m-d H:i:s',
        'type.number'   => '类型必须是数字',
        'type.in'  => '类型只能在1和2之间',
        'email'        => '邮箱格式错误',    
    ];    
}