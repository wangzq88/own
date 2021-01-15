<?php


namespace App\Rpc;


use Hyperf\DbConnection\Db;
use Hyperf\RpcServer\Annotation\RpcService;

/**
 * @RpcService(name="FiscalRevExpService",protocol="jsonrpc-http",server="jsonrpc-http",publishTo="consul")
 */
class FiscalRevExpService implements FiscalRevExpServiceInterface
{

    public function getRevenueExpenditure($start=0,$limit=20): array
    {
        $result = Db::table('fiscal_revenue_expenditure')->orderBy('timestamp', 'desc')->offset($start)->limit($limit)->get();
        $res = array();
        foreach ($result as $key => $item)
        {
            $res[$key] = $item;
            $res[$key]['date'] = $item['time_type'] ==  '1' ? date('Y',$item['timestamp']):date('Y.1-n',$item['timestamp']);
            $res[$key]['all_revenue'] = bcadd($item['budget_revenue'],$item['fund_revenue'],2);
            $res[$key]['all_expenditure'] = bcadd($item['budget_expenditure'],$item['fund_expenditure'],2);
            $res[$key]['income_expend'] = bcsub($res[$key]['all_revenue'],$res[$key]['all_expenditure'],2);
        }
        $timestamp_l = array_column($res,'timestamp');
        array_multisort($timestamp_l,SORT_ASC,$res);
        return $res;
    }
}