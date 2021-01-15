<?php


namespace App\Rpc;


use Hyperf\DbConnection\Db;
use Hyperf\RpcServer\Annotation\RpcService;

/**
 * @RpcService(name="ImportExportService",protocol="jsonrpc-http",server="jsonrpc-http",publishTo="consul")
 */
class ImportExportService implements ImportExportServiceInterface
{

    public function getImportExport($start, $limit): array
    {
        $result = Db::table('service_goods_import_export')->orderBy('timestamp', 'desc')->offset($start)->limit($limit)->get();
        $res = array();
        foreach ($result as $key => $item)
        {
            $res[$key] = $item;
            $res[$key]['date'] = $item['time_type'] ==  '1' ? date('Y',$item['timestamp']):date('Y.1-n',$item['timestamp']);
            $res[$key]['all_service'] = bcsub($item['service_export'],$item['service_import'],2);
            $res[$key]['all_goods'] = bcsub($item['goods_export'],$item['goods_import'],2);
            $res[$key]['all_foreign'] = bcsub($item['utilize_foreign'],$item['foreign_investment'],2);
            $res[$key]['all_service_goods'] = bcadd($res[$key]['all_service'],$res[$key]['all_goods'],2);
            $res[$key]['income_expend'] = bcadd($res[$key]['all_service_goods'],$res[$key]['all_foreign'],2);
        }
        $timestamp_l = array_column($res,'timestamp');
        array_multisort($timestamp_l,SORT_ASC,$res);
        return $res;
    }
}