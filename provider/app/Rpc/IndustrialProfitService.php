<?php


namespace App\Rpc;

use Hyperf\DbConnection\Db;
use Hyperf\RpcServer\Annotation\RpcService;

/**
 * @RpcService(name="IndustrialProfitService",protocol="jsonrpc-http",server="jsonrpc-http",publishTo="consul")
 */
class IndustrialProfitService implements IndustrialProfitServiceInterface
{
    public function getList($start, $limit): array
    {
        $list = Db::table('industrial_enterprises_profit')->orderBy('timestamp', 'desc')->offset($start)->limit($limit+1)->get();
        $res = array();
        $res['count'] = Db::table('industrial_enterprises_profit')->count();
        $res['limit'] = $limit;
        $res['total_page'] = ceil($res['count']/$limit);
        foreach ($list as $key => $item)
        {
            $res['list'][$key] = $item;
            $res['list'][$key]['date'] = $item['time_type'] ==  '1' ? date('Y',$item['timestamp']):date('Y.1-n',$item['timestamp']);
            $industrial_profit_increase = isset($list[$key+1]) ? bcdiv($item['industrial_profit']-$list[$key+1]['industrial_profit'],$list[$key+1]['industrial_profit'],4):0;
            $res['list'][$key]['industrial_profit_increase'] = bcmul($industrial_profit_increase,100,2);
            $stateowned_profit_increase = isset($list[$key+1]) ? bcdiv($item['stateowned_profit']-$list[$key+1]['stateowned_profit'],$list[$key+1]['stateowned_profit'],4):0;
            $res['list'][$key]['stateowned_profit_increase'] = bcmul($stateowned_profit_increase,100,2);
            $private_profit_increase = isset($list[$key+1]) ? bcdiv($item['private_profit']-$list[$key+1]['private_profit'],$list[$key+1]['private_profit'],4):0;
            $res['list'][$key]['private_profit_increase'] = bcmul($private_profit_increase,100,2);
            $foreign_profit_increase = isset($list[$key+1]) ? bcdiv($item['foreign_profit']-$list[$key+1]['foreign_profit'],$list[$key+1]['foreign_profit'],4):0;
            $res['list'][$key]['foreign_profit_increase'] = bcmul($foreign_profit_increase,100,2);
            if($key == $limit - 1) break;
        }
        return $res;
    }
}