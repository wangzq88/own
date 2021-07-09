<?php
/**
 * link: http://www.zjhejiang.com/
 * copyright: Copyright (c) 2018 浙江禾匠信息科技有限公司
 * author: jack_guo
 */

namespace app\forms\mall\export;

use app\core\CsvExport;
use app\plugins\gift\models\GiftOrder;

class GiftStatisticsExport extends BaseExport
{

    public $type;

    public function fieldsList()
    {
        return [
            [
                'key' => 'name',
                'value' => '商品名称',
            ],
            [
                'key' => 'attr',
                'value' => '规格',
            ],
            [
                'key' => 'goods_num',
                'value' => '支付件数',
            ],
            [
                'key' => 'total_price',
                'value' => '支付金额',
            ],
            [
                'key' => 'user_num',
                'value' => '支付人数',
            ],
            [
                'key' => 'convert_num',
                'value' => '领取件数',
            ],
        ];
    }

    public function export($query)
    {
        $list = $query
            ->asArray()
            ->all();
        $this->transform($list);
        $this->getFields();
        $dataList = $this->getDataList();

        $fileName = '礼物统计' . date('YmdHis');
        (new CsvExport())->export($dataList, $this->fieldsNameList, $fileName);
    }

    protected function transform($list)
    {
        $newList = [];
        $arr = [];

        $number = 1;
        foreach ($list as $key => $item) {
            $arr['number'] = $number++;
            $goods_info = json_decode($item['goods_info'], true);
            $item['attr'] = $goods_info['attr_list'][0]['attr_group_name'] . ':' . $goods_info['attr_list'][0]['attr_name'];
            $item['convert_num'] = GiftOrder::find()
                    ->andWhere(['goods_id' => $item['goods_id'], 'goods_attr_id' => $item['goods_attr_id']])
                    ->sum('num') ?? '0';
            $arr = array_merge($arr, $item);


            $newList[] = $arr;
        }
        $this->dataList = $newList;
    }

    protected function getFields()
    {
        $arr = [];
        foreach ($this->fieldsList() as $key => $item) {
            $arr[$key] = $item['key'];
        }
        $this->fieldsKeyList = $arr;
        parent::getFields(); // TODO: Change the autogenerated stub
    }
}
