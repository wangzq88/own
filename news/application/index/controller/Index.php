<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Page;
use think\Validate;

class Index extends Controller
{
    public function index()
    {
        $list = Db::name('news')->order('date','desc')->paginate(config('paginate.list_rows'),true)
        ->each(function($item, $key){
            return $this->_format_list($item, $key);
        });
        $this->assign('list', $list);
        return $this->fetch();
    }
    
    public function search()
    {
        $data = input('get.');
        $data['keyword'] = input('keyword'); 
        $data['start'] = input('start'); 
        $data['end'] = input('end'); 
        $validate = new Validate(['keyword' => 'require','start' => 'date','end' => 'date']);
        $result = $validate->check($data);
        if ($result != 1) {
            $this->error($validate->getError());
        } 
        $query = Db::name('news')->where('content','like',$data['keyword']);
        if($data['start'])
        {
            $query = $query->where('date','>',$data['start']);
        }
        if($data['end'])
        {
            $query = $query->where('date','<',$data['end']);
        }        
        $list = $query->order('date','desc')->paginate(config('paginate.list_rows'),true)
       ->each(function($item, $key){
            return $this->_format_list($item, $key);
        });
        $this->assign('list', $list);
        return $this->fetch('index');        
    }

    private function _format_list($item,$key)
    {
        if($item['type'] == 2 && isset($item['num_json']))
        {
            $item['num_json'] = json_decode($item['num_json']);
        }   
        else
        {
            $item['content'] = mb_strlen($item['content']) > 300 ? mb_substr($item['content'],0,300).'……':$item['content'];     
        }
        return $item;
    }
    
}