<?php


namespace App\Rpc;

use Hyperf\DbConnection\Db;
use Hyperf\RpcServer\Annotation\RpcService;

/**
 * @RpcService(name="IncomeExpenditureService",protocol="jsonrpc-http",server="jsonrpc-http",publishTo="consul")
 */
class IncomeExpenditureService implements IncomeExpenditureServiceInterface
{

    public function getList($start, $limit): array
    {
        $list = Db::table('resident_income_expenditure')->orderBy('timestamp', 'desc')->offset($start)->limit($limit+1)->get();
        $res = array();
        $res['count'] = Db::table('resident_income_expenditure')->count();
        $res['limit'] = $limit;
        $res['total_page'] = ceil($res['count']/$limit);
        foreach ($list as $key => $item)
        {
            $res['list'][$key] = $item;
            $res['list'][$key]['date'] = date('Y',$item['timestamp']);
            $city_income_increase = isset($list[$key+1]['city_income']) ? bcdiv($item['city_income']-$list[$key+1]['city_income'],$list[$key+1]['city_income'],4):0;
            $res['list'][$key]['city_income_increase'] = bcmul($city_income_increase,100,2);
            $city_expenses_increase = isset($list[$key+1]['city_expenses']) ? bcdiv($item['city_expenses']-$list[$key+1]['city_expenses'],$list[$key+1]['city_expenses'],4):0;
            $res['list'][$key]['city_expenses_increase'] = bcmul($city_expenses_increase,100,2);
            $res['list'][$key]['city_left'] = bcsub($item['city_income'],$item['city_expenses'],2);
            $all_city_left = ($item['city_income']-$item['city_expenses'])*$item['city_population'];
            $res['list'][$key]['all_city_left']  = bcdiv($all_city_left,pow(10,4),2);
            if($key == $limit - 1) break;
        }
        return $res;
    }
}