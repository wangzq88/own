<?php


namespace App\Rpc;

use Hyperf\DbConnection\Db;
use Hyperf\RpcServer\Annotation\RpcService;
/**
 * @RpcService(name="CentralbankService",protocol="jsonrpc-http",server="jsonrpc-http",publishTo="consul")
 */
class CentralbankService implements CentralbankServiceInterface
{
    public function assets($start,$limit): array
    {
        $list = Db::table('central_bank_assets')->orderBy('timestamp', 'desc')->offset($start)->limit($limit)->get();
        $res = array();
        $res['count'] = Db::table('central_bank_assets')->count();
        $res['limit'] = $limit;
        $res['total_page'] = ceil($res['count']/$limit);
        foreach ($list as $key => $item)
        {
            $res['list'][$key] = $item;
            $res['list'][$key]['year'] = $item['time_type'] ==  '1' ? date('Y',$item['timestamp']):date('Y.1-n',$item['timestamp']);
            $res['list'][$key]['foreign_exchange_rate'] = bcmul(bcdiv($item['foreign_exchange'],$item['all_assets'],4),'100',2);
            $res['list'][$key]['blank_claims_rate'] = bcmul(bcdiv($item['blank_claims'],$item['all_assets'],4),'100',2);
        }
        return $res;
    }


}