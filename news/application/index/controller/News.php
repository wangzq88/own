<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class News extends Controller
{
    protected $beforeActionList = [
        'login',
        'filter' =>  ['only'=>'add']
    ];
    
    public function create()
    {
        $this->assign('breadcrumb', [['title' => '添加资讯']]);
        $this->assign('bclen', 1);
        return $this->fetch(); 
    }
    
    public function add()
    {
        $data = input('post.');
        $data['title'] = trim($data['title']);
        $data['content'] = trim($data['content']);
        $data['date'] = str_replace('T',' ',$data['date']).':00';
        // 数据验证
        $data['type'] = 3;
        
        $result = $this->validate($data,'News');        

        if ($result != 1) {
            return json(['code' => 1,'info' => $result]);
        } 
        $row = Db::name('news')->where('content','=',$data['content'])->find();
        if($row)
        {
            return json(['code' => 1,'info' => '记录已经存在']);
        }
        unset($data['__token__']);
        $succ = Db::name('news')->insertGetId($data);
        $code = $succ > 0 ? 0:1; 
        $info = $succ > 0 ? '插入成功':'插入失败'; 
        return json(['code' => $code,'info' => $info]); 
    }    
    
    protected function filter()
    {
        Request::instance()->filter(['strip_tags']);
    }
    
    protected function login()
    {
        if(!Session::has('user_id'))
        {
            $request = Request::instance()->header('X-Requested-With');
            if(strtoupper($request) == 'XMLHTTPREQUEST')            
            {
                return json(['code' => 401,'info' => '未认证']); 
            }
            else
            {
                $this->redirect('user/login');
            }
        }
    }    
}