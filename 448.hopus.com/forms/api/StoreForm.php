<?php

namespace app\forms\api;

use app\core\response\ApiCode;
use app\models\Model;
use app\models\Store;

class StoreForm extends Model
{
    public $page;
    public $limit;
    public $id;
    public $keyword;
    public $longitude;
    public $latitude;

    public $store_id;

    public function rules()
    {
        return [
            [['longitude', 'latitude', 'keyword'], 'trim'],
            [['id', 'limit', 'page','store_id'], 'integer',],
            [['limit',], 'default', 'value' => 20],
            [['page'], 'default', 'value' => 1]
        ];
    }

    public function search()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        //误删
        //getDistance(36.8103, 118.014, latitude, longitude)

        // CREATE FUNCTION getDistance(curLat DOUBLE, curLon DOUBLE, shopLat DOUBLE, shopLon DOUBLE)
        // RETURNS DOUBLE
        // BEGIN
        //   DECLARE  dis DOUBLE;
        //     set dis = ACOS(SIN((curLat * 3.1415) / 180 ) * SIN((shopLat * 3.1415) / 180 ) + COS((curLat * 3.1415) / 180 ) * COS((shopLat * 3.1415) / 180 ) * COS((curLon * 3.1415) / 180 - (shopLon * 3.1415) / 180 ) ) * 6370.996 ;
        //     RETURN dis;
        // END;

        if (!$this->longitude || !$this->latitude) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '地址获取失败'
            ];
        }
        if (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->mch_id) {
            $mch_id = \Yii::$app->user->identity->mch_id;
        } else {
            $mch_id = 0;
        }
        $query = Store::find()->where([
            'mall_id' => \Yii::$app->mall->id,
            'mch_id' => $mch_id,
            'is_delete' => 0
        ])
            //可换为自定义
            ->select(['*', "(st_distance(point(longitude, latitude), point($this->longitude, $this->latitude)) * 111195) as distance"])
            ->orderBy('distance DESC');
        if ($this->keyword) {
            $query->andWhere(['like', 'name', $this->keyword]);
        }
        $list = $query->page($pagination, $this->limit)->orderBy("distance ASC")->asArray()->all();

        array_walk($list, function (&$v) {
            if ($v['distance'] >= 1000) {
                $v['distance'] = round($v['distance'] / 1000, 2) . 'km';
            } else {
                $v['distance'] = round($v['distance'], 2) . 'm';
            }
        });
        unset($v);

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
            ],
        ];
    }

    public function detail()
    {
        if (!$this->validate()) {
            return $this->errorResponse;
        }
        $list = Store::findOne([
            'mall_id' => \Yii::$app->mall->id,
            'id' => $this->id,
            'is_delete' => 0,
        ]);
        if (!$list) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '店铺不存在',
            ];
        }
        $list->pic_url = json_decode($list->pic_url, true);

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
            ]
        ];
    }


    public function getLatStore(){

        if (!$this->validate()) {
            return $this->getErrorResponse();
        }


        $mall_id = \Yii::$app->mall->id;

        if($this->latitude && $this->longitude){

            $sql = '
                SELECT *,
    round(
        (
        6371 * acos (
        cos ( radians('.$this->latitude.') )
        * cos( radians( latitude ) )
        * cos( radians( longitude ) - radians('.$this->longitude.') )
        + sin ( radians('.$this->latitude.') )
        * sin( radians( latitude ) )
        )
        )
    ,2)AS distance
FROM
    zjhj_bd_store
WHERE is_delete=0 and mall_id='.$mall_id.' ORDER BY distance asc
            ';
            $result = \Yii::$app->db->createCommand($sql)->queryAll();

            if($result){
                $list = $result[0];
            }else{
                $list = [];
            }

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '请求成功',
                'data' => [
                    'list' => $list,
                ]
            ];

        }else{
            $list = Store::find()->where([
                'mall_id' => \Yii::$app->mall->id,
                'is_delete' => 0,
            ])->one();
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '请求成功',
                'data' => [
                    'list' => $list,
                ]
            ];
        }

    }

    public function getAllStore(){

        if (!$this->validate()) {
            return $this->getErrorResponse();
        }


        $mall_id = \Yii::$app->mall->id;

        if($this->latitude && $this->longitude){


            if($this->keyword){
                $where = "is_delete=0 and mall_id=" . $mall_id ." and (name like '%".$this->keyword."%' or address like '%".$this->keyword."%')";
            }else {
                $where = 'is_delete=0 and mall_id=' . $mall_id;
            }

            $sql = '
                SELECT *,
    round(
        (
        6371 * acos (
        cos ( radians('.$this->latitude.') )
        * cos( radians( latitude ) )
        * cos( radians( longitude ) - radians('.$this->longitude.') )
        + sin ( radians('.$this->latitude.') )
        * sin( radians( latitude ) )
        )
        )
    ,2)AS distance
FROM
    zjhj_bd_store
WHERE '.$where.' ORDER BY distance asc
            ';
            $result = \Yii::$app->db->createCommand($sql)->queryAll();


            if($this->store_id){
                $storeInfo = Store::find()->where(['id'=>$this->store_id])->asArray()->one();
            }else{
                $storeInfo = [];
            }


            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '请求成功',
                'data' => [
                    'list' => $result,
                    'storeInfo' => $storeInfo
                ]
            ];

        }else{
            $query = Store::find()->where([
                'mall_id' => \Yii::$app->mall->id,
                'is_delete' => 0,
            ]);

            if($this->keyword){
                $query->andWhere(['or',['like','name',$this->keyword],['like','address',$this->keyword]]);
            }

            $list = $query->asArray()->all();

            if($list){
                foreach($list as $k=>$v){
                    $list[$k]['distance'] = '0';
                }
            }


            if($this->store_id){
                $storeInfo = Store::find()->where(['id'=>$this->store_id])->asArray()->one();
            }else{
                $storeInfo = [];
            }

            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '请求成功',
                'data' => [
                    'list' => $list,
                    'storeInfo' => $storeInfo
                ]
            ];
        }

    }

}
