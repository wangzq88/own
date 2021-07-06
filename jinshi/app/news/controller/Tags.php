<?php
namespace app\news\controller;

use app\BaseController;
use think\Request;
use think\facade\Session;
use think\facade\View;
use think\facade\Db;

class Tags extends BaseController
{
	protected $middleware = ['auth' => ['except' => ['index'] ],'admin' => ['except' => ['index']] ];
	
	public function index()
	{
        $list = Db::name('tags')->select()->toArray();
        View::assign('breadcrumb', [['title' => '标签云']]);
        View::assign('bclen', 1);		
        View::assign('list', $list);
        return View::fetch(); 		
	}
	
    public function edit()
    {
		$id = input('get._id');
		if($id)
		{
			$row = Db::name('tags')->where('_id','=',$id)->find();
			View::assign('row', $row);
		}		
		View::assign('action', url('news/tags/update'));
        View::assign('breadcrumb', [['title' => '编辑标签']]);
        View::assign('bclen', 1);
        return View::fetch();   
    }	
	
    public function update()
    {
		$this->request->filter(['strip_tags','trim']);
        $data = input('post.');
        $data['tag'] = $data['tag'];
        $data['category'] = $data['category'];
        
        $check = $this->request->checkToken('__token__');
        
        if(false === $check) {
			return json(['code' => 1,'info' => 'invalid token','token' => token()]);
        }		
        $result = $this->validate($data,'Tags');        

        if ($result != 1) {
            return json(['code' => 1,'info' => $result,'token' => token()]);
        } 
        $row = Db::name('tags')->where('category','=',$data['category'])->find();
        if($row && empty($data['_id']))
        {
            return json(['code' => 1,'info' => '分类已经存在','token' => token()]);
        }
        unset($data['__token__']);
		$data['tag'] = preg_split('/[\s,]+/',$data['tag']);
		if(empty($data['_id']))
		{
			$succ = Db::name('tags')->insertGetId($data);
		}
		else
		{
			$succ = Db::name('tags')->update($data);
			
		}
        $code = $succ > 0 ? 0:1; 
        $info = $succ > 0 ? '插入成功':'插入失败'; 
        return json(['code' => $code,'info' => $info,'token' => token()]); 
    }
	
    public function delete()
    {
		$id = input('post._id');
		if(empty($id))
		{
			return json(['code' => 1,'info' => '参数不能为空']);
		}
		Db::name('tags')->where('_id','=',$id)->delete();
		return json(['code' => 0,'info' => '删除成功','_id' => $id]); 
	}	
}