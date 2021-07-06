<?php
namespace app\news\middleware;

use think\facade\Session;

class Auth
{
    public function handle($request, \Closure $next)
    {
        if(!Session::has('user_id'))
        {
            $req_with = $request->header('X-Requested-With');
            if(strtoupper($req_with) == 'XMLHTTPREQUEST')            
            {
                return json(['code' => 401,'info' => '未认证']); 
            }
            else
            {
                return redirect('/news/user/login');
            }
        }
        return $next($request);
    }
}