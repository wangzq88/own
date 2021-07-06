<?php
namespace app\news\controller;

use app\BaseController;
use think\Request;
use think\facade\Session;
use think\facade\View;

class User extends BaseController
{
    
    public function login()
    {
        if(Session::has('user_id'))
        {
            return redirect('/index.php/news');
        }		
        $get = input('get.');
        View::assign('get', $get);
        View::assign('breadcrumb', [['title' => '登录']]);
        View::assign('bclen', 1);        
        return View::fetch(); 
    }    
    
    public function go()
    {
        $code = 1; 
        $info = '用户名或者密码错误';         
		$this->request->filter(['strip_tags']);
        $data = input('post.');        
        if($data['email'] == '751446682@qq.com' && $data['pwd'] == '123456!abcdef')
        {
            Session::set('user_id','1');
			Session::set('user_name','admin');
            $code = 0; 
            $info = '登录成功'; 
        }
        return json(['code' => $code,'info' => $info]); 
    }
         
}