<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class User extends Controller
{
    protected $beforeActionList = [
        'check',
        'filter' =>  ['only'=>'go']
    ];
    
    public function login()
    {
        $get = input('get.');
        $this->assign('get', $get);
        $this->assign('breadcrumb', [['title' => '登录']]);
        $this->assign('bclen', 1);        
        return $this->fetch(); 
    }    
    
    public function go()
    {
        $code = 1; 
        $info = '用户名或者密码错误';         
        $data = input('post.');        
        if($data['email'] == '751446682@qq.com' && $data['pwd'] == '123456')
        {
            Session::set('user_id','1');
            $code = 0; 
            $info = '登录成功'; 
        }
        return json(['code' => $code,'info' => $info]); 
    }
    
    protected function filter()
    {
        Request::instance()->filter(['strip_tags']);
    }    
    
    protected function check()
    {
        if(Session::has('user_id'))
        {
            redirect()->restore();
        }            
    }        
}