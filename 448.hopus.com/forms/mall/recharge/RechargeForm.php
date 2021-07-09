<?php
/**
 * link: http://www.zjhejiang.com/
 * copyright: Copyright (c) 2018 浙江禾匠信息科技有限公司
 * author: xay
 */
namespace app\forms\mall\recharge;

use app\core\response\ApiCode;
use app\models\Coupon;
use app\models\Model;
use app\models\Recharge;

class RechargeForm extends Model
{
    public $id;
    public $keyword;
    public $mall_id;
    public $name;
    public $pay_price;
    public $send_price;
    public $is_delete;
    public $send_integral;
    public $send_member_id;
    public $pt_coupon_id;
    public $jy_coupon_id;

    public $pt_coupon_num;
    public $jy_coupon_num;

    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['id', 'mall_id', 'is_delete', 'send_integral'], 'integer'],
            [['pay_price', 'send_price'], 'number'],
            [['is_delete', 'send_price', 'send_integral', 'send_member_id'], 'default', 'value' => 0],
            [['keyword','pt_coupon_id','jy_coupon_id','pt_coupon_num','jy_coupon_num'], 'string'],
            [['pay_price', 'send_price'], 'number', 'max' => 2147483648],
            [['keyword'], 'default', 'value' => 0],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mall_id' => 'mall ID',
            'name' => '名称',
            'pay_price' => '支付价格',
            'send_price' => '赠送价格',
            'is_delete' => '删除',
            'send_integral' => '赠送积分',
            'send_member_id' => '赠送会员',
        ];
    }

    //GET
    public function getList()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        $query = Recharge::find()->where([
            'mall_id' => \Yii::$app->mall->id,
            'is_delete' => 0,
        ]);

        $list = $query->keyword($this->keyword, ['like', 'name', $this->keyword])
            ->with('member')
            ->orderBy('id DESC,created_at DESC')
            ->asArray()
            ->all();

        if($list){
            foreach($list as $k=>$v){
                if($v['pt_coupon_id']){
                    $ptCouponArr = explode(',',$v['pt_coupon_id']);
                    $ptCouponNameArr = [];
                    $ptCouponNumArr =explode(',',$v['pt_coupon_num']);
                    foreach($ptCouponArr as $k1=>$v1) {
                        $couponInfo = Coupon::find()->where(['id' => $v1, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])->one();
                        $ptCouponNameArr[] = $couponInfo->name.'('.$ptCouponNumArr[$k1].'张)';
                    }
                    if($ptCouponNameArr){
                        $list[$k]['ptCouponName'] = implode(',',$ptCouponNameArr);
                    }else {
                        $list[$k]['ptCouponName'] = '';
                    }
                }else{
                    $list[$k]['ptCouponName'] = '';
                }
                if($v['jy_coupon_id']){
                    $jyCouponArr = explode(',',$v['jy_coupon_id']);
                    $jyCouponNameArr = [];
                    $jyCouponNumArr =explode(',',$v['jy_coupon_num']);
                    foreach($jyCouponArr as $k1=>$v1) {
                        $couponInfo = Coupon::find()->where(['id' => $v1, 'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])->one();
                        $jyCouponNameArr[] = $couponInfo->name.'('.$jyCouponNumArr[$k1].'张)';
                    }
                    if($jyCouponNameArr) {
                        $list[$k]['jyCouponName'] = implode(',',$jyCouponNameArr);
                    }else{
                        $list[$k]['jyCouponName'] = '';
                    }
                }else{
                    $list[$k]['jyCouponName'] = '';
                }
            }
        }

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'list' => $list,
            ]
        ];
    }

    //DELETE
    public function destroy()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        
        $model = Recharge::findOne([
            'id' => $this->id,
            'mall_id' => \Yii::$app->mall->id,
            'is_delete' => 0
        ]);
        if (!$model) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => '数据不存在或已删除',
            ];
        }
        $model->is_delete = 1;
        $model->deleted_at = date('Y-m-d H:i:s');
        $model->save();
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => '删除成功'
        ];
    }

    //DELETE
    public function getDetail()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $list = Recharge::find()->where([
            'mall_id' => \Yii::$app->mall->id,
            'id' => $this->id
        ])
            ->with('member')
            ->asArray()
            ->one();

        if($list['jy_coupon_id']){
            $list['jy_coupon_id'] = explode(',',$list['jy_coupon_id']);
        }


        if($list['pt_coupon_id']){
            $list['pt_coupon_id'] = explode(',',$list['pt_coupon_id']);
        }



        $pt_coupon_list = Coupon::find()->where(['mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])->andWhere(['<>','appoint_type',4])->asArray()->all();
        $jy_coupon_list = Coupon::find()->where(['appoint_type'=>4,'mall_id' => \Yii::$app->mall->id, 'is_delete' => 0])->asArray()->all();
        
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'pt_coupon_list' => $pt_coupon_list,
                'jy_coupon_list' => $jy_coupon_list,
                'list' => $list,
            ]
        ];
    }

    //SAVE
    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $model = Recharge::findOne([
            'mall_id' => \Yii::$app->mall->id,
            'id' => $this->id
        ]);
        if (!$model) {
            $model = new Recharge();
        }

        $model->attributes = $this->attributes;

        $model->mall_id = \Yii::$app->mall->id;
        if ($model->save()) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功'
            ];
        } else {
            return $this->getErrorResponse($model);
        }
    }
}
