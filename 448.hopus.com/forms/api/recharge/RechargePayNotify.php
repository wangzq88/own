<?php
/**
 * link: http://www.zjhejiang.com/
 * copyright: Copyright (c) 2018 浙江禾匠信息科技有限公司
 * author: wxf
 */

namespace app\forms\api\recharge;


use app\core\payment\PaymentNotify;
use app\core\response\ApiCode;
use app\forms\common\coupon\CommonCoupon;
use app\forms\common\coupon\UserCouponCenter;
use app\models\MallMembers;
use app\models\RechargeOrders;
use app\models\User;
use yii\helpers\ArrayHelper;

class RechargePayNotify extends PaymentNotify
{
    public function notify($paymentOrder)
    {
        try {
            /* @var RechargeOrders $order */
            $order = RechargeOrders::find()->where(['order_no' => $paymentOrder->orderNo])->one();

            if (!$order) {
                throw new \Exception('订单不存在:' . $paymentOrder->orderNo);
            }

            if ($order->pay_type != 1) {
                throw new \Exception('必须使用微信支付');
            }

            $order->is_pay = 1;
            $order->pay_time = date('Y-m-d H:i:s', time());
            $res = $order->save();

            if (!$res) {
                throw new \Exception('充值订单支付状态更新失败');
            }

            $user = User::findOne($order->user_id);
            if (!$user) {
                throw new \Exception('用户不存在');
            }

            $desc = '充值余额：' . $order->pay_price . '元,赠送：' . $order->send_price . '元';
            $desc = $this->sendMember($user, $order, $desc);
            $this->sendBalance($user, $order, $desc);
            $this->sendIntegral($user, $order);
            $this->sendCoupon1($user, $order); //送券 普通券
            $this->sendCoupon2($user, $order); //送券 加油券
        } catch (\Exception $e) {
            \Yii::error($e);
            throw $e;
        }
    }


    protected function sendCoupon1($user, $order)
    {

        if($order->pt_coupon_id) {

            $ptCouponIdArr = explode(',',$order->pt_coupon_id);
            $ptCouponIdNum = explode(',',$order->pt_coupon_num);

            foreach($ptCouponIdArr as $k=>$v) {
                $common = new CommonCoupon(['coupon_id' => $v], false);
                $common->user = $user;
                $coupon = $common->getDetail();
                if ($coupon->is_delete == 1) {
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '优惠券不存在'
                    ];
                }
                if ($coupon->expire_type == 2 && $coupon->end_time < mysql_timestamp()) {
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '优惠券已过期'
                    ];
                }
                $count = $common->checkAllReceive($coupon->id);
                if ($count >= $coupon->can_receive_count && $coupon->can_receive_count != -1) {
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '优惠券领取次数已达上限'
                    ];
                } else {
                    $class = new UserCouponCenter($coupon, $common->user);
                    if ($common->receive($coupon, $class, '充值领取',$ptCouponIdNum[$k])) {
                        if ($coupon->can_receive_count == -1) {
                            $rest = -1;
                        } elseif ($coupon->can_receive_count <= $count + 1) {
                            $rest = 0;
                        } else {
                            $rest = $coupon->can_receive_count - $count - 1;
                        }
                        $coupon = ArrayHelper::toArray($coupon);
                        $coupon['rest'] = $rest;
                        $coupon['type'] = (string)$coupon['type'];
                    }
                }
            }
        }

    }


    protected function sendCoupon2($user, $order)
    {


        if($order->jy_coupon_id) {

            $jyCouponIdArr = explode(',',$order->jy_coupon_id);
            $jyCouponIdNum = explode(',',$order->jy_coupon_num);

            foreach($jyCouponIdArr as $k=>$v) {
                $common = new CommonCoupon(['coupon_id' => $v], false);
                $common->user = $user;
                $coupon = $common->getDetail();
                if ($coupon->is_delete == 1) {
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '优惠券不存在'
                    ];
                }
                if ($coupon->expire_type == 2 && $coupon->end_time < mysql_timestamp()) {
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '优惠券已过期'
                    ];
                }
                $count = $common->checkAllReceive($coupon->id);
                if ($count >= $coupon->can_receive_count && $coupon->can_receive_count != -1) {
                    return [
                        'code' => ApiCode::CODE_ERROR,
                        'msg' => '优惠券领取次数已达上限'
                    ];
                } else {
                    $class = new UserCouponCenter($coupon, $common->user);
                    if ($common->receive($coupon, $class, '充值领取',$jyCouponIdNum[$k])) {
                        if ($coupon->can_receive_count == -1) {
                            $rest = -1;
                        } elseif ($coupon->can_receive_count <= $count + 1) {
                            $rest = 0;
                        } else {
                            $rest = $coupon->can_receive_count - $count - 1;
                        }
                        $coupon = ArrayHelper::toArray($coupon);
                        $coupon['rest'] = $rest;
                        $coupon['type'] = (string)$coupon['type'];
                    }
                }
            }
        }

    }


    protected function sendMember($user, $order, $desc)
    {
        if (!empty($order->send_member_id)) {
            $mallMembersModel = MallMembers::findOne([
                'id' => $order->send_member_id,
                'status' => 1,
                'is_delete' => 0,
            ]);
            if ($mallMembersModel) {
                if ($user->identity->member_level >= $mallMembersModel->level) {
                    $desc .= ',赠送会员失败：用户会员等级高于或等于赠送等级';
                } else {
                    $desc .= sprintf(',赠送会员成功：会员ID=>%s', $mallMembersModel->id);
                    $user->identity->member_level = $mallMembersModel->level;
                    $user->identity->save();
                }
            } else {
                $desc .= ',赠送会员失败：会员状态异常，请查看会员是否启用';
            }
        }

        return $desc;
    }





    protected function sendBalance($user, $order, $desc)
    {
        $price = (float)($order->pay_price + $order->send_price);
        \Yii::$app->currency->setUser($user)->balance->add(
            $price,
            $desc,
            \Yii::$app->serializer->encode($order->attributes),
            $order->order_no
        );
    }

    protected function sendIntegral($user, $order)
    {
        \Yii::$app->currency->setUser($user)->integral->add(
            $order->send_integral,
            "余额充值,赠送积分{$order->send_integral}",
            \Yii::$app->serializer->encode($order->attributes),
            $order->order_no
        );
    }
}
