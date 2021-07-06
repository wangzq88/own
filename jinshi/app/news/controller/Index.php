<?php
namespace app\news\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\Validate;

class Index extends BaseController
{
	protected $middleware = ['auth' => ['only' => ['create','add'] ],'admin' => ['except' => ['index','search']] ];
	
    public function index()
    {
        $list = Db::name('news')->order('date','desc')->paginate(env('paginate.list_rows',15),true)
        ->each(function($item, $key){
            return $this->_format_list($item, $key);
        });
        View::assign('list', $list);
        return View::fetch();    
	}

    public function search()
    {
        $this->request->filter(['strip_tags','trim']);
        $data['keyword'] = input('keyword'); 
        $data['start'] = input('start'); 
        $data['end'] = input('end'); 
    
        $validate = new Validate(['start' => 'date','end' => 'date|gt:start']);
        $result = $validate->check($data);
        if ($result != 1) {
            $this->error($validate->getError());
        } 

        $query = Db::name('news')->where('content','like',$data['keyword']);
        if($data['start'])
        {
            $query = $query->where('date','>=',$data['start']);
        }
        if($data['end'])
        {
            $query = $query->where('date','<',$data['end']);
        }        
        $list = $query->order('date','desc')->paginate([
            'list_rows' => env('paginate.list_rows'),
            'query'     => $data
        ])
        ->each(function($item, $key){
            return $this->_format_list($item, $key);
        });
        return View::fetch('index',['list' => $list,'breadcrumb' => [['title' => '搜索']],'bclen' => 1]);        
    }

    public function create()
    {
		View::assign('action', url('news/index/add'));
        View::assign('breadcrumb', [['title' => '添加资讯']]);
        View::assign('bclen', 1);
        return View::fetch(); 
    }

    public function edit()
    {
		$id = input('_id');
		$row = Db::name('news')->where('_id','=',$id)->find();
		View::assign('action', url('news/index/update'));
        View::assign('breadcrumb', [['title' => '编辑资讯']]);
        View::assign('bclen', 1);
		View::assign('row', $row);
        return View::fetch('create');   
    }
    
    public function add()
    {
		$this->request->filter(['strip_tags','trim']);
        $data = input('post.');
        $data['title'] = trim($data['title']);
        $data['content'] = trim($data['content']);
        $data['date'] = str_replace('T',' ',$data['date']).':00';
        // 数据验证
        $data['type'] = 3;
        
        $check = $this->request->checkToken('__token__');
        
        if(false === $check) {
			return json(['code' => 1,'info' => 'invalid token']);
        }		
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

    public function update()
    {
		$this->request->filter(['strip_tags','trim']);
        $data = input('post.');
        $data['title'] = trim($data['title']);
        $data['content'] = trim($data['content']);
        $data['date'] = str_replace('T',' ',$data['date']).':00';
        $id = $data['_id'];
        $check = $this->request->checkToken('__token__');
        
        if(false === $check) {
			return json(['code' => 1,'info' => 'invalid token']);
        }		
        $result = $this->validate($data,'News');        

        if ($result != 1) {
            return json(['code' => 1,'info' => $result]);
        } 
        $row = Db::name('news')->where('_id','=',$id)->find();
        if(!$row)
        {
            return json(['code' => 1,'info' => '记录不存在']);
        }
        unset($data['__token__']);
        $succ = Db::name('news')->update($data);
        $code = $succ > 0 ? 0:1; 
        $info = $succ > 0 ? '更新成功':'更新失败'; 
        return json(['code' => $code,'info' => $info]); 
    } 

    public function delete()
    {
		$id = input('post._id');
		if(empty($id))
		{
			return json(['code' => 1,'info' => '参数不能为空']);
		}
		Db::name('news')->where('_id','=',$id)->delete();
		return json(['code' => 0,'info' => '删除成功','_id' => $id]); 
	}

    private function _format_list($item,$key)
    {
        if (in_array($item['type'],[1,3])) 
        {
			$item['content'] = mb_strlen($item['content']) > 300 ? mb_substr($item['content'],0,300).'……':$item['content'];      
            $item['content'] = preg_replace_callback("@[a-zA-z]+://[^\s]*@i",function($matches){
                return '<a href="'.$matches[0].'" target="_blank">'.$matches[0].'</a>';
            },$item['content']);
        } 
        return $item;
    }
}
