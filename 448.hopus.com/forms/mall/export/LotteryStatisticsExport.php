<?php
/**
 * link: http://www.zjhejiang.com/
 * copyright: Copyright (c) 2018 浙江禾匠信息科技有限公司
 * author: jack_guo
 */

namespace app\forms\mall\export;

use app\core\CsvExport;

class LotteryStatisticsExport extends BaseExport
{

    public function fieldsList()
    {
        return [
            [
                'key' => 'name',
                'value' => '商品名称',
            ],
            [
                'key' => 'attr_groups',
                'value' => '规格',
            ],
            [
                'key' => 'start_at',
                'value' => '开始时间',
            ],
            [
                'key' => 'end_at',
                'value' => '结束时间',
            ],
            [
                'key' => 'invitee',
                'value' => '被邀请人数',
            ],
            [
                'key' => 'participant',
                'value' => '参与人数',
            ],
            [
                'key' => 'code_num',
                'value' => '抽奖劵码数量',
            ],
            [
                'key' => 'status',
                'value' => '状态',
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

        $fileName = '幸运抽奖统计' . date('YmdHis');
        (new CsvExport())->export($dataList, $this->fieldsNameList, $fileName);
    }

    protected function transform($list)
    {
        $newList = [];
        $arr = [];

        $number = 1;
        foreach ($list as $key => $item) {
            $arr['number'] = $number++;
            $item['attr_groups'] = $this->attr_groups($item['attr_groups']);
            $item['participant'] = intval($item['participant']);
            $item['invitee'] = intval($item['invitee']);
            $item['code_num'] = intval($item['code_num']);

            $arr = array_merge($arr, $item);

            $newList[] = $arr;
        }
        $this->dataList = $newList;
    }

    protected function attr_groups($value)
    {
        $value = json_decode($value, true);
        if (is_array($value)) {
            $attr = '';
            foreach ($value as $v) {
                $attr .= $v['attr_group_name'] . ':' . $v['attr_list'][0]['attr_name'] . ' ';
            }
        }
        return $attr;
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
