<?php


namespace App\Rpc;

use Hyperf\DbConnection\Db;
use Hyperf\RpcServer\Annotation\RpcService;

/**
 * @RpcService(name="AssetInvestmentService",protocol="jsonrpc-http",server="jsonrpc-http",publishTo="consul")
 */
class AssetInvestmentService implements AssetInvestmentServiceInterface
{

    public function getList($start, $limit): array
    {
        $list = Db::table('fixed_asset_investment')->orderBy('timestamp', 'desc')->offset($start)->limit($limit+1)->get();
        $res = array();
        $res['count'] = Db::table('fixed_asset_investment')->count();
        $res['limit'] = $limit;
        $res['total_page'] = ceil($res['count']/$limit);
        foreach ($list as $key => $item)
        {
            $res['list'][$key] = $item;
            $res['list'][$key]['date'] = $item['time_type'] ==  '1' ? date('Y',$item['timestamp']):date('Y.1-n',$item['timestamp']);
            $not_farmers_increase = isset($list[$key+1]) ? bcdiv($item['not_farmers']-$list[$key+1]['not_farmers'],$list[$key+1]['not_farmers'],3):0;
            $res['list'][$key]['not_farmers_increase'] = bcmul($not_farmers_increase,100,1);
            $secondary_industry_increase = isset($list[$key+1]) ? bcdiv($item['secondary_industry']-$list[$key+1]['secondary_industry'],$list[$key+1]['secondary_industry'],3):0;
            $res['list'][$key]['secondary_industry_increase'] = bcmul($secondary_industry_increase,100,1);
            $real_estate_increase = isset($list[$key+1]) ? bcdiv($item['real_estate']-$list[$key+1]['real_estate'],$list[$key+1]['real_estate'],3):0;
            $res['list'][$key]['real_estate_increase'] = bcmul($real_estate_increase,100,1);
            if($key == $limit - 1) break;
        }
        return $res;
    }
}