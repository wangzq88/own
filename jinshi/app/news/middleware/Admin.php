<?php
namespace app\news\middleware;

use think\facade\Session;

class Admin
{
    public function handle($request, \Closure $next)
    {
        if(!Session::has('user_id') || Session::get('user_name') != 'admin')
        {
            $req_with = $request->header('X-Requested-With');
            if(strtoupper($req_with) == 'XMLHTTPREQUEST')            
            {
                return json(['code' => 401,'info' => '没有权限']); 
            }
            else
            {
                return redirect('/news/index');
            }
        }
        return $next($request);
    }
}