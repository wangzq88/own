<?php


namespace App\Rpc;


use Hyperf\DbConnection\Db;
use Hyperf\RpcServer\Annotation\RpcService;

/**
 * @RpcService(name="GlassActivitiesService",protocol="jsonrpc-http",server="jsonrpc-http",publishTo="consul")
 */
class GlassActivitiesService implements GlassActivitiesServiceInterface
{

    public function getInventory($start,$limit): array
    {
        $list = Db::table('glass_activities_data')->orderBy('timestamp', 'desc')->offset($start)->limit($limit+1)->get();
        $res = array();
        $res['count'] = Db::table('glass_activities_data')->count();
        $res['limit'] = $limit;
        $res['total_page'] = ceil($res['count']/$limit);
        foreach ($list as $key => $item)
        {
            $res['list'][$key] = $item;
            $res['list'][$key]['date'] = date('Y-m-d',$item['timestamp']);
            $res['list'][$key]['inventory_increase'] = isset($list[$key+1]['inventory']) ? $item['inventory']-$list[$key+1]['inventory']:0;
            if($key == $limit - 1) break;
        }
        return $res;
    }
}