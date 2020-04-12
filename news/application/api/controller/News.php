<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class News extends Controller
{
    protected $beforeActionList = [
        'login',
        'filter' =>  ['only'=>'post']
    ];
    
    
    public function post()
    {
        $param = urldecode(input('get.param'));
        
        $data = parse_url($param);
        return jsonp(['code' => 0,'info' => $data]);
        $result = $this->validate($data,'News');        

        if ($result != 1) {
            return jsonp(['code' => 1,'info' => $result]);
        } 
        $cursor = Db::name('news')->where('date','=',$data['date'])->select();
        foreach ($cursor as $item) {
            if($item && ($item['type'] == 1 && $item['content'] == trim($data['content']) 
                || $item['type'] == 2 && $item['title'] == trim($data['title'])))
            {
                if(empty($item['img']) && isset($data['img']) && $data['img'])
                {
                    $newdata = ['$set' => ["img" => trim($data['img'])]];
                    Db::name('news')->update(["_id" => $item['_id']], $newdata);                
                }        
                if(empty($item['url']) && isset($data['url']) && $data['url'])
                {
                    $newdata = ['$set' => ["url" => 'https:'.trim($data['url']),'ext' => $ext,'source' => 'jinshi']];
                    Db::name('news')->update(["_id" => $item['_id']], $newdata);        
                }
                jsonp(['code' => 0,'info' => '记录已经存在']);
            }
        }        
        unset($data['__token__']);
        $succ = Db::name('news')->insertGetId($data);
        $code = $succ > 0 ? 0:1; 
        $info = $succ > 0 ? '插入成功':'插入失败'; 
        return jsonp(['code' => $code,'info' => $info]); 
    }    
    
    protected function filter()
    {
        Request::instance()->filter(['strip_tags']);
    }
    
    protected function login()
    {
        if(!Session::has('user_id'))
        {
            return jsonp(['code' => 401,'info' => '未认证']); 
        }
    }    
}